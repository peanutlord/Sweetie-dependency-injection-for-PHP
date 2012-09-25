<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;
use Sweetie\Injector;

/**
 * Factory to get the object
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Binder
{

    /**
     * Holds the instance to the binder
     *
     * @var Sweetie\Binder
     */
    protected static $_instance = null;

    /**
     * Holds the closure for the cache
     *
     * @var \Closure
     */
    protected static $_cache = null;

    /**
     * Holds the closure for the session handler
     *
     * @var \Closure
     */
    protected static $_sessionHandler = null;

    /**
     * Holds the instance of the reader
     *
     * @var Sweetie\Reader
     */
    protected $_reader = null;

    /**
     * Holds the default injector
     *
     * @var Sweetie\Injector
     */
    protected $_defaultInjector = null;

    /**
     * Holds all objects with a request scope
     *
     * @var object[]
     */
    protected $_objects = array();

    /**
     * Configures the class to work properly
     *
     * @return void
     */
    public static function boostrap(Reader $reader)
    {
        // @todo allow multiple boostrap calls to change configuration?
        if (static::$_instance === null) {
            static::$_instance = new self($reader);
        }

        return static::$_instance;
    }

    /**
     * Sets a closure for the cache.
     *
     * The method signature is as following:
     *
     * function cache($key, $value = null) {
     *      // Do something funky with it
     * };
     *
     * When given a only a key, the closure should return the value from the
     * cache. If nothing has been found, a false should be returned. If you
     * pass $key and $value, it should store it.
     *
     * @param \Closure $cache
     *
     * @return void
     */
    public static function setCache(\Closure $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Sets a closure as a session handler. This is required if you work with
     * a session scope. The method signature and behavior is the same as
     * with {@see Binder::setCache()}
     *
     * @param \Closure $sessionHandler
     *
     * @return void
     */
    public static function setSessionHandler(\Closure $sessionHandler)
    {
        self::$_sessionHandler = $sessionHandler;
    }

    /**
     * Resets the instance of the Binder
     *
     * @return void
     */
    public static function resetInstance()
    {
        static::$_instance = null;
    }

    /**
     * Does some basic setup for the binder to work
     *
     * @return void
     */
    protected function __construct(Reader $reader)
    {
        $this->_reader = $reader;

        // Default behavior
        self::$_cache = function($key, $value = null) {
             return false;
        };

        self::$_sessionHandler = function($key, $value = null) {
             return false;
        };
    }

    /**
     * Creates a object from a blueprint (by id) and returns it
     *
     * @param string $id
     */
    public static function factory($id)
    {
        return static::$_instance->create($id);
    }

    /**
     * @see Binder::factory()
     */
    public function create($id)
    {
        if ($this->_reader === null) {
            $message = 'No reader found, did you run Binder::bootstrap()?';
            throw new \BadMethodCallException($message);
        }

        $blueprint = $this->_reader->getBlueprint($id);

        switch ($blueprint->getScope()) {
            case Blueprint::SCOPE_SESSION:
                $object = $this->_handleSessionScope($blueprint);
                break;

            case Blueprint::SCOPE_REQUEST:
                $object = $this->_handleRequestScope($blueprint);
                break;

            case Blueprint::SCOPE_NONE:
            default:
                $object = $this->_handleNoScope($blueprint);
                break;
        }

        return $object;
    }

    /**
     * Handles a session scope
     *
     * @todo unittest the call
     * @todo unittest with a closure
     *
     * @param Blueprint $blueprint
     *
     * @return object
     */
    protected function _handleSessionScope(Blueprint $blueprint)
    {
        $object = self::$_sessionHandler($blueprint->getId());
        if ($object !== false) {
            return $object;
        }

        $object = $this->_getInjector()->inject($blueprint);
        self::$_sessionHandler($blueprint->getId(), $object);

        return $object;
    }

    /**
     * Handles a request scope
     *
     * @param Blueprint $blueprint
     *
     * @return object
     */
    protected function _handleRequestScope(Blueprint $blueprint)
    {
        if (isset($this->_objects[$blueprint->getId()])) {
            return $this->_objects[$blueprint->getId()];
        }

        $object = $this->_getInjector()->inject($blueprint);
        $this->_objects[$blueprint->getId()] = $object;

        return $object;
    }

    /**
     * Handles a blueprint with no scope
     *
     * @param Blueprint $blueprint
     *
     * @return object
     */
    protected function _handleNoScope(Blueprint $blueprint)
    {
        return $this->_getInjector()->inject($blueprint);
    }

    /**
     * Returns the default injector
     *
     * @return Injector
     */
    protected function _getInjector()
    {
        if ($this->_defaultInjector === null) {
            // The XML holds an option which one of the injectors is the default one
            $injectorName = $this->_reader->getOption('injector', 'Sweetie\Injector\Magic');

            // @todo check type
            $this->_defaultInjector = new $injectorName($this);
        }

        return $this->_defaultInjector;
    }


}