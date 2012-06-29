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
}