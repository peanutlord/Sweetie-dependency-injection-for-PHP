<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie\Scope;

use Sweetie\Scope;

/**
 * Request Scope
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class Request extends Scope
{

    /**
     * Storage for the created objects
     *
     * @var objects[]
     */
    protected $_objects = array();

    /**
     * @see Sweetie\Scope::store()
     */
    public function store($key, $object)
    {
        $this->_objects[$key] = $object;
    }

    /**
     * @see Sweetie\Scope::retrieve()
     */
    public function retrieve($key)
    {
        return $this->_objects[$key];
    }

    /**
     * @see Sweetie\Scope::contains()
     */
    public function contains($key)
    {
        return isset($this->_objects[$key]);
    }

}