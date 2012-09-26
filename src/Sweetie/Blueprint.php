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
use Sweetie\Blueprint\Property;

class Blueprint implements \Iterator, \Countable
{

    /**#@+
     * Scope for the binding
     *
     * @var string
     */
    const SCOPE_REQUEST = 'request';
    const SCOPE_SESSION = 'session';
    const SCOPE_NONE = 'none';
    /**#@-*/

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
     * Holds the scope of the blueprint
     *
     * @var string
     */
    protected $_scope = self::SCOPE_NONE;

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
     * Returns the id of the blueprint
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
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
     * @return Property
     */
    public function addProperty($name, $value)
    {
        $property = new Property($name, $value);

        $position = $this->_findProperty($name);
        if ($position === false) {
            $this->_properties[] = $property;
        } else {
            // Might happend when using a template for the blueprint
            $this->_properties[$position] = $property;
        }

        return $property;
    }

    /**
     * Finds a property by name
     *
     * @param string $name
     *
     * @return int the position of the property inside the $_properties array
     */
    protected function _findProperty($name)
    {
        // @todo check if we can use array search or something similiar
        foreach ($this->_properties as $key => $property) {
            if ($property->getName() === $name) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Sets the scope of the blueprint
     *
     * @todo unittest
     * @param string $scope
     *
     * @return void
     */
    public function setScope($scope = self::SCOPE_NONE)
    {
        $this->_scope = $scope;
    }

    /**
     * Return the scope of the blueprint
     *
     * @return string
     */
    public function getScope()
    {
        return $this->_scope;
    }

    /**
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->_properties[$this->_head];
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
        return $this->_head;
    }

    /**
     * @see Iterator::valid()
     */
    public function valid()
    {
        return isset($this->_properties[$this->_head]);
    }

    /**
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->_head = 0;
    }

    /**
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->_properties);
    }


}