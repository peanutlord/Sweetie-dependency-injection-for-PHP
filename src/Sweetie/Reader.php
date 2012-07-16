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
     * Inits the stack for parsing
     *
     */
    public function __construct()
    {
        $this->_stack = new Stack();
    }

    /**
     * Loads the file and passes it to Reader::parse()
     *
     * @param string $file
     *
     * @return void
     */
    public function load($file) {
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
            if (!$property->isIdReference()) {
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
    protected function _newBlueprint($id, $class)
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