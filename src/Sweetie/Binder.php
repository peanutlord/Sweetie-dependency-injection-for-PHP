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
     * Holds all registered scopes
     *
     * @var Sweetie\Scope
     */
    protected static $_scopes = array();

    /**
     * Configures the class to work properly
     *
     * @return void
     */
    public static function bootstrap(Reader $reader)
    {
        self::registerDefaultScopes();

        // @todo allow multiple bootstrap calls to change configuration?
        if (static::$_instance === null) {
            static::$_instance = new self($reader);
        }

        return static::$_instance;
    }

    /**
     * Registers all default scopes
     *
     * @return void
     */
    public static function registerDefaultScopes()
    {
        self::registerScope('none', new Scope\None());
        self::registerScope('request', new Scope\Request());
        self::registerScope('session', new Scope\Session());
    }

    /**
     * Registers a single scope under a given name

     *
     * @param string $name
     * @param Scope $scope
     *
     * @return void
     */
    public static function registerScope($name, Scope $scope)
    {
        self::$_scopes[$name] = $scope;
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
        $scopeType = $blueprint->getScope();

        if (!isset(self::$_scopes[$scopeType])) {
            throw new \InvalidArgumentException(sprintf('Unknown scope "%s"', $scopeType));
        }

        $scope = self::$_scopes[$scopeType];
        if ($scope->contains($blueprint->getId())) {
            return $scope->retrieve($blueprint->getId());
        }

        $object = $this->_getInjector()->inject($blueprint);
        $scope->store($blueprint->getId(), $object);

        return $object;
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