<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

namespace Sweetie;

/**
 * Abstract class for all scoping behaviors
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
abstract class Scope
{

    /**
     * Stores the object under the key in a repository
     *
     * @param string $key the key to store, mostly the blueprint-id
     * @param object $object
     *
     * @return void
     */
    public abstract function store($key, $object);

    /**
     * Returns the requested object
     *
     * @param string $key
     *
     * @return object
     */
    public abstract function retrieve($key);

    /**
     * Checks if a object is stored under the given key
     *
     * @param string $key
     *
     * @return bool
     */
    public abstract function contains($key);

}