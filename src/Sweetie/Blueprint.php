<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;

/**
 * Represents class properties and its references to other classes
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Blueprint implements \Iterator
{

    /**
     * Holds the id the blueprint
     *
     * @var string
     */
    protected $_id = null;

    /**
     * Holds the class of the blueprint
     *
     * @var string
     */
    protected $_class = null;

    /**
     * Holds all properties
     *
     * @var string[]
     */
    protected $_properties = array();

    /**
     * Array Pointrer
     *
     * @var int
     */
    protected $_head = 0;

    /**
     *
     *
     */
    public function __construct($id, $class)
    {
        $this->_id = $id;
        $this->_class = $class;
    }

    /**
     * Returns the name of the class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Adds a property of the blueprint
     *
     * @return void
     */
    public function addProperty($name, $value)
    {
        $this->_properties[$name] = $value;
    }

    /**
     * Returns all property names
     *
     * @return string
     */
    public function getPropertyNames()
    {
        return array_keys($this->_properties);
    }

    /**
     * @see Iterator::current()
     */
    public function current()
    {
        $keys = $this->getPropertyNames();
        return $this->_properties[$keys[$this->_head]];
    }

    /**
     * @see Iterator::next()
     */
    public function next()
    {
        $this->_head++;
    }

    /**
     * @see Iterator::key()
     */
    public function key()
    {
        $keys = $this->getPropertyNames();
        return $keys[$this->_head];
    }

    /**
     * @see Iterator::valid()
     */
    public function valid()
    {
        $keys = $this->getPropertyNames();
        return isset($keys[$this->_head]);
    }

    /**
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->_head = 0;
    }

    /**
     * Returns if the given string is a reference to a blueprint id
     *
     * @todo moved here so Reader and Injector can use it; perhaps blueprint should use
     *       a another class like "Blueprint\Property with methods like isReference()
     *
     * @param string $ref
     *
     * @return bool
     */
    public function isIdReference($ref)
    {
        return strpos($ref, '@id:') === 0;
    }

    /**
     * Returns the id from a given reference
     *
     * @todo moved here so Reader and Injector can use it; perhaps blueprint should use
     *       a another class like "Blueprint\Property with methods like isReference()
     *
     * @param string $ref
     *
     * @return string
     */
    public function getIdFromReference($ref)
    {
        return substr($ref, 4);
    }

}