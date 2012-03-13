<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Reader\XML;

/**
 * Test cases for the class binder
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class XMLTest extends PHPUnit_Framework_TestCase
{

    /**
     * Write the XML (needs only once)
     *
     * @return void
     */
    public function __construct()
    {
        $xml = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<sweetie>
    <option key="injector" value="Sweetie\Injector\Magic" />

    <bindings>
        <blueprint id="stubTest" class="Foo">
            <property name="bar" ref="Bar" />
        </blueprint>
    </bindings>

</sweetie>
XML;
        file_put_contents('/tmp/bind.xml', $xml);
    }

    /**
     * @return void
     */
    public function testInvalidFileThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $reader = new XML();
        $reader->load('falseBind.xml');
    }

    /**
     * @return void
     */
    public function testInvalidBlueprintIdThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $reader = new XML();
        $reader->load('/tmp/bind.xml');

        $reader->getClassBindings('someFalseId');
    }

    /**
     * @return void
     */
    public function testParseSimpleBlueprint()
    {
        $reader = new XML();
        $reader->load('/tmp/bind.xml');

        $binding = $reader->getClassBindings('stubTest');

        $this->assertEquals('Foo', $binding->getClass());
        $this->assertContains('bar', $binding->getProperties());
        $this->assertEquals('Bar', $binding->getReference('bar'));
    }

    /**
     *
     * @return void
     */
    public function testRedeclaringBlueprintIdThrowsException()
    {
        $xml = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<sweetie>
    <option key="injector" value="Sweetie\Injector\Magic" />

    <bindings>
        <blueprint id="stubTest" class="Foo">
            <property name="bar" ref="Bar" />
        </blueprint>
        <blueprint id="stubTest" class="Foo">
            <property name="bar" ref="Bar" />
        </blueprint>
    </bindings>

</sweetie>
XML;
        file_put_contents('/tmp/bind.xml', $xml);

        $this->setExpectedException('InvalidArgumentException');

        $reader = new XML();
        $reader->load('/tmp/bind.xml');
    }

}