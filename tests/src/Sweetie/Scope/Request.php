<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

/**
 * Test cases for the session scope
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class SessionTest extends \TestCase
{

    public function testClosureIsUsed()
    {
        $that = $this;
        $handler = function($key, $value = null) use($that) {
            if ($value === null) {
                $that->assertEquals('myKey', $key);
            } else {
                $that->assertEquals('myKey', $key);
                $that->assertInstanceOf('stdClass', $value);
            }
        };

        Sweetie\Scope\Session::registerSessionHandler($handler);

        $session = new Sweetie\Scope\Session();
        $session->store('myKey', new stdClass());
        $session->retrieve('myKey');
    }

}