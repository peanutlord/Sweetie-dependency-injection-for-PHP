<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Blueprint;
use Sweetie\Blueprint\Property;
use Sweetie\Reader\XML;

class A { }
class B { protected $a; }


/**
 * Test cases for the class binder
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class ReaderTest extends \TestCase
{
    /**
     * @return void
     */
    public function testLoadCallsParseMethod()
    {
        $reader = $this->getMockForAbstractClass('\Sweetie\Reader', array('parse'));
    }

    /**
     * @return void
     */
    public function testInvalidFileThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $reader = $this->getMockForAbstractClass('\Sweetie\Reader');
        $reader->load('invalidBindFile.xml');
    }

    /**
     * @return void
     */
    public function testCyclicDependencyDetected()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $xml = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<sweetie>
	<bindings>
        <blueprint id="blueprint1" class="Foo">
            <property name="bar" ref="@id:blueprint2" />
        </blueprint>
        <blueprint id="blueprint2" class="Bar">
            <property name="bar" ref="@id:blueprint3" />
        </blueprint>
        <blueprint id="blueprint3" class="Baz">
            <property name="bar" ref="@id:blueprint1" />
        </blueprint>
    </bindings>

</sweetie>
XML;
        $this->_writeFile('/tmp/bind.xml', $xml);

        $actualObject = new XML();
        $actualObject->load('/tmp/bind.xml');
    }

    public function testApplyTemplateProperties()
    {
        $xmlReader = new XML();
        $reflection = new ReflectionObject($xmlReader);

        /* @var $addTemplate ReflectionMethod */
        $addTemplate = $reflection->getMethod('_addTemplate');
        $addTemplate->setAccessible(true);

        $addTemplate->invokeArgs($xmlReader, array('foo', 'bar', 'baz'));

        $blueprint = new Blueprint('someId', 'someClass');

        /* @var $applyTemplate ReflectionMethod */
        $applyTemplate = $reflection->getMethod('_applyTemplate');
        $applyTemplate->setAccessible(true);

        $applyTemplate->invokeArgs($xmlReader, array($blueprint, 'foo'));

        $this->assertEquals(1, $blueprint->count());

        $property = $blueprint->current();
        $this->assertEquals('bar', $property->getName());
        $this->assertEquals('baz', $property->getReference());
    }

    public function testApplyTemplatePropertiesGetOverriden()
    {
        $xmlReader = new XML();
        $reflection = new ReflectionObject($xmlReader);

        /* @var $addTemplate ReflectionMethod */
        $addTemplate = $reflection->getMethod('_addTemplate');
        $addTemplate->setAccessible(true);

        $addTemplate->invokeArgs($xmlReader, array('foo', 'bar', 'baz'));

        $blueprint = new Blueprint('someId', 'someClass');

        /* @var $applyTemplate ReflectionMethod */
        $applyTemplate = $reflection->getMethod('_applyTemplate');
        $applyTemplate->setAccessible(true);

        $applyTemplate->invokeArgs($xmlReader, array($blueprint, 'foo'));

        $blueprint->addProperty('bar', 'que');

        $this->assertEquals(1, $blueprint->count());

        $property = $blueprint->current();
        $this->assertEquals('bar', $property->getName());
        $this->assertEquals('que', $property->getReference());
    }
}