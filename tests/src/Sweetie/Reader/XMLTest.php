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

    public function testAddingTemplates()
    {
        $xml = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<sweetie>
    <templates>
        <template id="foo">
            <property name="bar" ref="@id:Bar" />
            <property name="foo" ref="@id:Foo" />
        </template>
    </templates>
</sweetie>
XML;
        $this->_writeFile('/tmp/bind.xml', $xml);

        $reader = $this->getMock('\Sweetie\Reader\XML', array('_addTemplate'));
        $reader->expects($this->exactly(2))
               ->method('_addTemplate')
               ->with($this->equalTo('foo'),
                      $this->logicalOr($this->equalTo('bar'), $this->equalTo('foo')),
                      $this->logicalOr($this->equalTo('@id:Bar'), $this->equalTo('@id:Foo'))
                )
               ->will($this->returnValue(null));

        $reader->load('/tmp/bind.xml');
    }

}