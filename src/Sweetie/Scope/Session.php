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
class Session extends Scope
{

    /**
     * The handler for the session
     *
     * @var \Closure
     */
    protected static $_handler = null;

    /**
     * Sets a session handler closure, which will be used to store/retrieve the data
     *
     * function handler($key, $value = null) {
     *     if ($value === null) {
     *         // @todo return value, otherwise false
     *     } else {
     *         // @todo store value
     *     }
     * }
     *
     * @param \Closure $handler
     *
     * @return void
     */
    public static function registerSessionHandler(\Closure $handler)
    {
        self::$_handler = $handler;
    }

    /**
     *
     */
    public function _construct()
    {
        if (self::$_handler === null) {
            $this->_createDefaultSessionHandler();
        }
    }

    /**
     * Creates a default session handler
     *
     * @return void
     */
    protected function _createDefaultSessionHandler()
    {
        self::$_handler = function($key, $value = null) {
            if ($value === null) {
                if (!isset($_SESSION[$value])) {
                    return false;
                }

                return $_SESSION[$value];
            } else {
                $_SESSION[$key] = $value;
            }
        };
    }

    /**
     * We can't use the property directly, even when it's a closure, because
     * PHP will throw a fatal error...
     *
     * @param string $key
     * @param object $object
     *
     * @return mixed
     */
    protected function _invoke($key, $object = null)
    {
        $closure = self::$_handler;
        return $closure($key, $object);
    }

    /**
     * @see Sweetie\Scope::store()
     */
    public function store($key, $object)
    {
        return $this->_invoke($key, $object);
    }

    /**
     * @see Sweetie\Scope::retrieve()
     */
    public function retrieve($key)
    {
        return $this->_invoke($key);
    }

    /**
     * @see Sweetie\Scope::contains()
     */
    public function contains($key)
    {
        return $this->_invoke($key) !== false;
    }

}