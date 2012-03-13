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
class ClassBindings
{

    /**
     * Holds the name of the class
     *
     * @var string
     */
    protected $_className = '';

    /**
     * Holds the ID of the binding (assigned in the blueprint id)
     *
     * @var string
     */
    protected $_id = '';

    /**
     * Holds the properties which shall be bound to a reference
     *
     * @var string[]
     */
    protected $_properties = array();

    /**
     * Creates an class binding object
     *
     * @param string $id
     * @param string $className
     */
    public function __construct($id, $className)
    {
        $this->_id = $id;
        $this->_className = $className;
    }

    /**
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->_className;
    }

    /**
     * Returns the id of the class binding
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns all properties of the class which need to be referenced
     *
     * @return string[]
     */
    public function getProperties()
    {
        return array_keys($this->_properties);
    }

    /**
     * Adds a property to the binding
     *
     * @param string $name
     * @param string $reference
     *
     * @return void
     */
    public function addProperty($name, $reference)
    {
        $this->_properties[$name] = $reference;
    }

    /**
     * Returns the reference of a property
     *
     * @param string $property
     *
     * @return string
     */
    public function getReference($property)
    {
        if (!isset($this->_properties[$property])) {
            $message = sprintf(sprintf('Property "%s" has no reference', $property));
            throw new \InvalidArgumentException($message);
        }

        return $this->_properties[$property];
    }

}