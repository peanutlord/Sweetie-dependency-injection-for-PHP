<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie\Blueprint;

/**
 * Represents class properties and its references to other classes
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Property
{

    /**
     * Holds the name of the property
     *
     * @var string
     */
    protected $_name = '';

    /**
     * Holds the reference of the property
     *
     * @var string
     */
    protected $_ref = '';

    /**
     * Constructor
     *
     * @param string $name
     * @param string $ref
     *
     * @return void
     */
    public function __construct($name, $ref)
    {
        $this->_name = $name;
        $this->_ref = $ref;
    }

    /**
     * Returns of the property reference is a id
     *
     * @return bool
     */
    public function isIdReference()
    {
        return strpos($this->_ref, '@id:') === 0;
    }

    /**
     * Returns the id from the reference
     *
     * @return string
     */
    public function getIdFromReference()
    {
        return substr($this->_ref, 4);
    }

    /**
     * Returns the reference itself (unchanged)
     *
     * @return string
     */
    public function getReference()
    {
        return $this->_ref;
    }

    /**
     * Returns the name of the property
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

}