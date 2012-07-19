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
     * The regular expression to parse a @id and @invoke tag
     *
     * @var string
     */
    private $_regexp = '/@(\w*)\(([^)]*)\)/';

    /**
     * Inline cache for the parsed reference
     *
     * @var string[]
     */
    private $_parsedReference = array();

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
     * Returns if the reference is a class name
     *
     * @return bool
     */
    public function isClassReference()
    {
        return strpos($this->_ref, '@') === false;
    }

    /**
     * Parses the reference
     *
     * @return string[]
     */
    protected function _parseReference()
    {
        if (count($this->_parsedReference) !== 0) {
            return $this->_parsedReference;
        }

        $matches = array();
        preg_match($this->_regexp, $this->_ref, $matches);

        if (count($matches) === 0) {
            // @todo happens with syntax error's, change error message
            throw new \InvalidArgumentException(sprintf('Can\'t parse reference "%s", sure its a tag?', $this->_ref));
        }

        $args = trim($matches[2]);

        if ($args === '') {
            throw new \InvalidArgumentException(sprintf('No arguments supplied within "%s" tag', $matches[1]));
        }

        $this->_parsedReference = array('tag' => $matches[1], 'args' => $args);
        return $this->_parsedReference;
    }

    /**
     * Returns if the reference is a blueprint id
     *
     * @return bool
     */
    public function isIdReference()
    {
        return $this->getReferenceType() === 'id';
    }

    /**
     * Returns the id from a @id($anyId) reference
     *
     * @return string
     */
    public function getIdFromReference()
    {
        $parts = $this->_parseReference();
        return $parts['args'];
    }

    /**
     * Returns if the reference is a method invocation
     *
     * @return bool
     */
    public function isInvokeReference()
    {
        return $this->getReferenceType() === 'invoke';
    }

    /**
     * Returns all parameters required to make a successfull invocation
     *
     */
    public function getInvokeParams()
    {
        $parts = $this->_parseReference();

        $args = explode(',', $parts['args']);
        $f = function($item) {
            $item = trim($item);
            return $item;
        };

        return array_map($f, $args);
    }

    /**
     * Returns the type of reference (id or invoke)
     *
     * @return string
     */
    public function getReferenceType()
    {
        $parts = $this->_parseReference();
        return $parts['tag'];
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