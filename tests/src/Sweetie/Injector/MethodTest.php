<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Reader\XML;
use Sweetie\Binder;
use Sweetie\Injector\Magic;
use Sweetie\Blueprint;

/**
 * Test cases for the class binder
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class MethodTest extends \TestCase
{

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        // Reader must be purged
        Binder::resetInstance();
    }

    public function testMethodCall()
    {
        $foo = $this->getMock('Foo', array('setBar'));
        $foo->expects($this->once())
            ->method('setBar');

        $blueprint = new Blueprint('myId', get_class($foo));
        $blueprint->addProperty('bar', 'Bar');

        $methodInjector = $this->getMock('Sweetie\Injector\\Method', array('_getDependency', '_createObject'), array(), '', false);
        $methodInjector->expects($this->once())
                       ->method('_getDependency')
                       ->with($this->isInstanceOf('Sweetie\\Blueprint\\Property'));

        $methodInjector->expects($this->once())
                       ->method('_createObject')
                       ->with($this->isInstanceOf('Sweetie\\Blueprint'))
                       ->will($this->returnValue($foo));

        $methodInjector->inject($blueprint);
    }

}