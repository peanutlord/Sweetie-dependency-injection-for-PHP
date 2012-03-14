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

        $bindings = $this->_reader->getClassBindings($id);

        $injector = $this->_getInjector();
        return $injector->inject($bindings);
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