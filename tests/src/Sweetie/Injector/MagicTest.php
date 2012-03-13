<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Reader\XML;
use Sweetie\Binder;
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
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        // Reader must be purged
        Binder::resetInstance();
    }

    /**
     *
     *
     * @return void
     */
    public function testReflectionInjection()
    {
        // Fake a class binding
        $bindings = new ClassBindings('someId', 'Foo');
        $bindings->addProperty('bar', 'Bar');

        $binder = $this->getMock('Sweetie\Binder', array(), array(), '', false);

        $magic = new Magic($binder);
        $actualObject = $magic->inject($bindings);

        $expected = new Bar();
        $this->assertEquals($expected, $actualObject->bar);
    }

    /**
     * Tests if a self referencing id throws an exception
     *
     * @return void
     */
    public function testSelfReferencingIDsThrowsException()
    {
        $bindings = new ClassBindings('someId', 'Foo');
        $bindings->addProperty('bar', '@id:someId');

		/* @var $reader Reader */
        $reader = $this->getMock('Sweetie\Reader\XML', array('getClassBindings'));
        $reader->expects($this->any())
               ->method('getClassBindings')
               ->will($this->returnValue($bindings));

        $instance = Binder::boostrap($reader);

        $message = 'Reference-ID someId references itself';
        $this->setExpectedException('InvalidArgumentException', $message);

        $magic = new Magic($instance);
        $magic->inject($bindings);
    }

	/**
     *
     *
     * @return void
     */
    public function testReferencingIDs()
    {
        $bindingsOne = new ClassBindings('someId', 'Foo');
        $bindingsOne->addProperty('bar', '@id:someOtherId');

        $bindingsTwo = new ClassBindings('someOtherId', 'Bar');

        $f = function($i) use($bindingsOne, $bindingsTwo) {
            if ($i == 'someOtherId') {
                return $bindingsTwo;
            } else {
                return $bindingsOne;
            }
        };

		/* @var $reader Reader */
        $reader = $this->getMock('Sweetie\Reader\XML', array('getClassBindings'));
        $reader->expects($this->any())
               ->method('getClassBindings')
               ->will($this->returnCallback($f));

        $instance = Binder::boostrap($reader);

        $magic = new Magic($instance);
        $magic->inject($bindingsOne);
    }

    /**
     *
     *
     */
    public function testExampleProblem()
    {
        $xml = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<sweetie>
    <option key="injector" value="Sweetie\Injector\Magic" />

    <bindings>
        <blueprint id="Bar" class="Bar" />
        <blueprint id="stubTest" class="Foo">
            <property name="bar" ref="@id:Bar" />
        </blueprint>
    </bindings>

</sweetie>
XML;
        file_put_contents('/tmp/bind.xml', $xml);

        $reader = new XML();
        $reader->load('/tmp/bind.xml');

        Binder::boostrap($reader);
        Binder::factory('stubTest');
    }

}