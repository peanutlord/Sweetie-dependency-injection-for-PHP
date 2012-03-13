<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Injector\Magic;
use Sweetie\ClassBindings;

class Foo { public $bar = null; }
class Bar { }

/**
 * Test cases for the class binder
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class MagicTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     *
     * @return void
     */
    public function testReflectionInjection()
    {
        // Fake a class binding
        $bindings = new ClassBindings('Foo', array('bar' => 'Bar'));

        $magic = new Magic();
        $actualObject = $magic->inject($bindings);

        $expected = new Bar();
        $this->assertEquals($expected, $actualObject->bar);
    }

}