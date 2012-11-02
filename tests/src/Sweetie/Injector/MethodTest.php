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

    public function testMethodGetsCalled()
    {
        $foo = $this->getMock('Foo', array('setBar'));
        $foo->expects($this->once())
            ->method('setBar')
            ->with($this->isInstanceOf('Bar'));

        $blueprint = new Blueprint('myId', get_class($foo));
        $blueprint->addProperty('bar', 'Bar');

        $methodInjector = $this->getMock('Sweetie\Injector\\Method', array('_getDependency', '_createObject'), array(), '', false);
        $methodInjector->expects($this->once())
                       ->method('_getDependency')
                       ->with($this->isInstanceOf('Sweetie\\Blueprint\\Property'))
                       ->will($this->returnValue(new Bar()));

        $methodInjector->expects($this->once())
                       ->method('_createObject')
                       ->with($this->isInstanceOf('Sweetie\\Blueprint'))
                       ->will($this->returnValue($foo));

        $methodInjector->inject($blueprint);
    }

    public function testNotExistingMethodTriggersException()
    {
        $this->setExpectedException('BadMethodCallException');

        $blueprint = new Blueprint('myId', 'Foo');
        $blueprint->addProperty('baz', 'Bar');

        $methodInjector = $this->getMock('Sweetie\Injector\\Method', array('_createObject'), array(), '', false);
        $methodInjector->expects($this->once())
                       ->method('_createObject')
                       ->with($this->isInstanceOf('Sweetie\\Blueprint'))
                       ->will($this->returnValue(new Foo()));

        $methodInjector->inject($blueprint);
    }

}