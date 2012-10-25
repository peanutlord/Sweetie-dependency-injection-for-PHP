<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;
use Sweetie\Blueprint;

/**
 * "Interface" for all readers which shall be used for getting dependencies
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
abstract class Reader
{

    /**
     * Holds all blueprints from the configuration file
     *
     * @var string[]
     */
    private $_blueprints = array();

    /**
     * Holds all options (coming from the blueprint) as key value pair
     *
     * @var string[]
     */
    private $_options = array();

    /**
     * Stack for the cyclic dependency detection
     *
     * @var Sweetie\Stack
     */
    private $_stack = null;

    /**
     * Holds the template config
     *
     * @var string[]
     */
    protected $_templates = array();

    /**
     * Holds the cache handler for storing data
     *
     * @var \Closure
     */
    protected static $_cacheHandler = null;

    /**
     * Sets the cache handler.
     *
     * The handler is a thin wrapper over a libary of your choice to store
     * the data inside a cache. The method signature (and usage) is
     * the same as with the session scope handler.
     *
     * @param \Closure $handler
     */
    public static function setCacheHandler(\Closure $handler)
    {
        self::$_cacheHandler = $handler;
    }

    /**
     * Inits the stack for parsing
     *
     */
    public function __construct()
    {
        $this->_stack = new Stack();

        if (self::$_cacheHandler === null) {
            $this->_setDefaultCacheHandler();
        }
    }

    /**
     * Sets a default cache handler which does in fact nothing
     *
     * @return void
     */
    protected function _setDefaultCacheHandler()
    {
        self::$_cacheHandler = function($key, $value = null) {
            return false;
        };
    }

    /**
     * Loads the file and passes it to Reader::parse()
     *
     * @param string $file
     *
     * @return void
     */
    public function load($file)
    {
        $hash = $this->_calculateHash($file);
        if ($this->_hasCacheHit($hash)) {
            $this->_loadBlueprintsFromCache($hash);
            return;
        }

        if (!is_readable($file)) {
            $message = sprintf('File "%s" not found or not readable', $file);
            throw new \InvalidArgumentException($message);
        }

        $this->_parse(file_get_contents($file));

        foreach ($this->_blueprints as $blueprint) {
            $this->_stack->clear();

            if ($this->_hasCyclicDependency($blueprint)) {
                $message = sprintf('Cyclic dependency detected: %s', $this->_stack);
                throw new \InvalidArgumentException($message);
            }
        }

        // It's safe now to store the data inside the cache.
        // Also the cache should take care of serialization
        $this->_invokeCache($hash, $this->_blueprints);
    }

    /**
     * Returns a hash of the file
     *
     * @todo perhaps the content of the file should be used? It would prevent
     *       that files with the same name generate the same hash
     *
     * @param string $file
     *
     * @return string
     */
    protected function _calculateHash($file)
    {
        return md5($file);
    }

    /**
     * Wrapper because we can't use the property directly as closure
     *
     * @param string $key
     * @param mixed|null $value
     *
     * @return mixed
     */
    protected function _invokeCache($key, $value = null)
    {
        $closure = self::$_cacheHandler;
        return $closure($key, $value);
    }

    /**
     *
     * @param string $hash
     */
    protected function _hasCacheHit($hash)
    {
        return $this->_invokeCache($hash) !== false;
    }

    /**
     * Loads the blueprints invoking the cache handler.
     *
     * Important note: make sure Reader::_hasCacheHit was called before,
     * the closure might return a false when nothing was found inside the cache
     *
     * @param string $hash
     *
     * @return void
     */
    protected function _loadBlueprintsFromCache($hash)
    {
        $this->_blueprints = $this->_invokeCache($hash);
    }

    /**
     * Parses given file and extracts the bindings
     *
     * @param string $content
     *
     * @return string
     */
    protected abstract function _parse($content);

    /**
     * Returns if the blueprint defines a cyclic dependency within the XML, which
     * would lead to a endless loop.
     *
     * @param Blueprint $blueprint
     *
     * @return bool
     */
    protected function _hasCyclicDependency(Blueprint $blueprint)
    {
        /* @var $property Sweetie\Blueprint\Property */
        foreach ($blueprint as $property) {
            if ($property->isClassReference() || $property->isInvokeReference()) {
                continue;
            }

            $id = $property->getIdFromReference();

            if ($this->_stack->contains($id)) {
                return true;
            }

            $this->_stack->push($id);

            $blueprint = $this->getBlueprint($id);
            if ($this->_hasCyclicDependency($blueprint)) {
                return true;
            }

            $this->_stack->pop();
        }
    }

    /**
     * Returns an option defined in the file
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($key, $default = false)
    {
        return isset($this->_options[$key]) ? $this->_options[$key] : $default;
    }

    /**
     * Sets an option
     *
     * @param string $key
     * @param string $value
     *
     * @return mixed
     */
    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;
    }

    /**
     * Creates a new blueprint object and returns it
     *
     * @param string $id
     * @param string $class
     *
     * @return Sweetie\Blueprint
     */
    protected function _createBlueprint($id, $class)
    {
        if (isset($this->_blueprints[$id])) {
            throw new \InvalidArgumentException(sprintf('Cannot redeclare ID "%s"', $id));
        }

        $this->_blueprints[$id] = new Blueprint($id, $class);
        return $this->_blueprints[$id];
    }

    /**
     * Returns the (parsed) Blueprints as objects
     *
     * @return Blueprint
     */
    public function getBlueprint($id)
    {
        if (!isset($this->_blueprints[$id])) {
            throw new \InvalidArgumentException(sprintf('Unknown Blueprint "%s"', $id));
        }

        return $this->_blueprints[$id];
    }

    /**
     * Adds template properties
     *
     * @return void
     */
    protected function _addTemplate($templateId, $name, $ref)
    {
        $this->_templates[$templateId][] = array('name' => $name, 'ref' => $ref);
    }

    /**
     * Apply's the properties, set in the template, on a blueprint
     *
     * @param Blueprint $blueprint
     * @param int $templateId
     *
     * @return void
     */
    protected function _applyTemplate(Blueprint $blueprint, $templateId)
    {
        if (!isset($this->_templates[$templateId])) {
            throw new \InvalidArgumentException(sprintf('Unknown template "%s"', $templateId));
        }

        foreach ($this->_templates[$templateId] as $tpl) {
            $blueprint->addProperty($tpl['name'], $tpl['ref']);
        }
    }

}