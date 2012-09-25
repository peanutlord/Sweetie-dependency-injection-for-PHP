<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie\Scope;

use Sweetie\Scope;

/**
 * Session Scope
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class None extends Scope
{

    /**
     * @see Sweetie\Scope::store()
     */
    public function store($key, $object)
    {
        // Do nothing
    }

    /**
     * @see Sweetie\Scope::retrieve()
     */
    public function retrieve($key)
    {
        throw new \BadMethodCallException('Scope "None" never has something to retrieve');
    }

    /**
     * @see Sweetie\Scope::contains()
     */
    public function contains($key)
    {
        return false;
    }

}