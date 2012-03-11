<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;

/**
 * "Interface" for all readers which shall be used for getting dependencies
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
abstract class Reader
{

    /**
     * Holds all bindings from the file
     *
     * @var string[]
     */
    protected $_bindings = array();

    /**
     * Reads and parses the file to get the blueprints
     *
     * @param string $file
     *
     * @return void
     */
    public abstract function load($file);

	/**
     * Returns an option defined in the file
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public abstract function getOption($key, $default);

    /**
     * Gets the binding between the classes
     *
     * @param string $id id of the blueprint
     *
     * @return Sweetie\ClassBindings
     */
    public function getClassBindings($id)
    {
        if (!isset($this->_blueprints[$id])) {
            $message = sprintf('Unknown ID "%s"', $id);
            throw new \InvalidArgumentException($message);
        }

        return $this->_bindings[$id];
    }

}