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
     * Holds the properties which shall be bound to a reference
     *
     * @var string[]
     */
    protected $_properties = array();

    /**
     * Creates an class binding object
     *
     * @param string $className
     * @param array $properties key value pair hash map (property => reference)
     */
    public function __construct($className, $properties)
    {
        $this->_className = $className;
        $this->_properties = $properties;
    }

    /**
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_className;
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