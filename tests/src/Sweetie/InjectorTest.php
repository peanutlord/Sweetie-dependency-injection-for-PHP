<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Blueprint\Property;

class toBeInvoked
{

    public static function staticInvoke($propertyName)
    {
        return new stdClass();
    }

    public function invoke($propertyName)
    {
        return new stdClass();
    }

    public function invokeWithParams($propertyName, $a, $b)
    {
        return new stdClass();
    }

    private function privateInvoke()
    {

    }

}

/**
 * Test cases for the injector class
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class InjectorTest extends \TestCase
{

    public function testIdReferencingBehavesCorrectly()
    {
        $property = $this->getMock('\Sweetie\Blueprint\Property', array('isClassReference', 'getReferenceType'), array(), '', false);
        $property->expects($this->once())
                 ->method('isClassReference')
                 ->will($this->returnValue(false));

        $property->expects($this->once())
                 ->method('getReferenceType')
                 ->will($this->returnValue('id'));

        $injector = $this->getMock('\Sweetie\Injector', array('_getIdDependency', 'inject'), array(), '', false);
        $injector->expects($this->once())
                 ->method('_getIdDependency')
                 ->with($this->isInstanceOf('\Sweetie\Blueprint\Property'))
                 ->will($this->returnValue(null));

        $reflection = new \ReflectionObject($injector);

        /* @var $method ReflectionMethod */
        $method = $reflection->getMethod('_getDependency');

        $method->setAccessible(true);
        $method->invokeArgs($injector, array($property));
    }

    public function testInvokeReferencingWithStaticMethod()
    {
        $property = new Property('foo', '@invoke(toBeInvoked, staticInvoke)');
        $injector = $this->getMock('\Sweetie\Injector', array('inject'), array(), '', false);

        $reflection = new \ReflectionObject($injector);

        /* @var $method ReflectionMethod */
        $method = $reflection->getMethod('_getDependency');

        $method->setAccessible(true);
        $object = $method->invokeArgs($injector, array($property));

        $this->assertTrue($object instanceof \stdClass);
    }

    public function testInvokeReferencingWithNormalMethod()
    {
        $property = new Property('foo', '@invoke(toBeInvoked, invoke)');
        $injector = $this->getMock('\Sweetie\Injector', array('inject'), array(), '', false);

        $reflection = new \ReflectionObject($injector);

        /* @var $method ReflectionMethod */
        $method = $reflection->getMethod('_getDependency');

        $method->setAccessible(true);
        $object = $method->invokeArgs($injector, array($property));

        $this->assertTrue($object instanceof \stdClass);
    }

    public function testInvokeReferencingWithPrivateMethod()
    {
        $this->setExpectedException('BadMethodCallException', 'Method "privateInvoke" is not public');

        $property = new Property('foo', '@invoke(toBeInvoked, privateInvoke)');
        $injector = $this->getMock('\Sweetie\Injector', array('inject'), array(), '', false);

        $reflection = new \ReflectionObject($injector);

        /* @var $method ReflectionMethod */
        $method = $reflection->getMethod('_getDependency');

        $method->setAccessible(true);
        $object = $method->invokeArgs($injector, array($property));

        $this->assertTrue($object instanceof \stdClass);
    }

    public function testInvokeReferencingWithParameters()
    {
        // The invoke tag creats a new instance of the tobeInvoked Mock, thus failing the
        // unit test. Until there is no good way to test that feature, I skip the
        // test
        $this->markTestSkipped();

        $toBeInvoked = $this->getMock('toBeInvoked', array('invokeWithParams'));
        $toBeInvoked->expects($this->once())
                    ->method('invokeWithParams')
                    ->with($this->equalTo('foo'),
                           $this->equalTo('hello'),
                           $this->equalTo('world'))
                    ->will($this->returnValue(null));

        $property = new Property('foo', sprintf('@invoke(%s, invokeWithParams, hello, world)', get_class($toBeInvoked)));
        $injector = $this->getMock('\Sweetie\Injector', array('inject'), array(), '', false);

        $reflection = new \ReflectionObject($injector);

        /* @var $method ReflectionMethod */
        $method = $reflection->getMethod('_getDependency');

        $method->setAccessible(true);
        $object = $method->invokeArgs($injector, array($property));
    }

    public function testInvokeReferencingWithUnknownMethod()
    {
        $this->setExpectedException('BadMethodCallException', 'Unknown Method "unknownInvoke"');

        $property = new Property('foo', '@invoke(toBeInvoked, unknownInvoke)');
        $injector = $this->getMock('\Sweetie\Injector', array('inject'), array(), '', false);

        $reflection = new \ReflectionObject($injector);

        /* @var $method ReflectionMethod */
        $method = $reflection->getMethod('_getDependency');

        $method->setAccessible(true);
        $object = $method->invokeArgs($injector, array($property));
    }

}