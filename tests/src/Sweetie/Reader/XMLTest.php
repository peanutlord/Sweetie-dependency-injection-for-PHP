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
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $xml = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<sweetie>
    <bindings>
        <blueprint id="stubTest" class="Foo">
            <property name="bar" ref="Bar" />
        </blueprint>
    </bindings>

</sweetie>
XML;
        $this->_writeXML($xml);
    }

    /**
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
       @unlink('/tmp/bind.xml');
    }

    /**
     * Writes the XML into a file
     *
     * @param string $data
     *
     * @return void
     */
    protected function _writeXML($data)
    {
        file_put_contents('/tmp/bind.xml', $data);
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

        $this->assertEquals('Foo', $binding->getClassName());
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
        $this->_writeXML($xml);

        $this->setExpectedException('InvalidArgumentException');

        $reader = new XML();
        $reader->load('/tmp/bind.xml');
    }

}