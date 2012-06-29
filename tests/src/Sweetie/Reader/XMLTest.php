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
class XMLTest extends \TestCase
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
        $this->_writeFile('/tmp/bind.xml', $xml);
    }

    /**
     * @return void
     */
    public function testInvalidBlueprintIdThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');

        $reader = new XML();
        $reader->load('/tmp/bind.xml');

        $reader->getBlueprint('someFalseId');
    }

    /**
     * @return void
     */
    public function testParseSimpleBlueprint()
    {
        $reader = new XML();
        $reader->load('/tmp/bind.xml');

        $blueprint = $reader->getBlueprint('stubTest');

        $this->assertEquals('Foo', $blueprint->getClass());
        $this->assertContains('bar', $blueprint->getPropertyNames());
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
        $this->_writeFile('/tmp/bind.xml', $xml);

        $this->setExpectedException('InvalidArgumentException');

        $reader = new XML();
        $reader->load('/tmp/bind.xml');
    }

}