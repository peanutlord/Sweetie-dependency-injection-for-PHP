<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Reader\XML;
use Sweetie\Binder;
use Sweetie\Injector\Magic;
use Sweetie\Blueprint;

class Foo { public $bar = null; }
class Bar { }

/**
 * Test cases for the class binder
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class MagicTest extends \TestCase
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
        $blueprint = new Blueprint('someId', 'Foo');
        $blueprint->addProperty('bar', 'Bar');

        $binder = $this->getMock('Sweetie\Binder', array(), array(), '', false);

        $magic = new Magic($binder);
        $actualObject = $magic->inject($blueprint);

        $expected = new Bar();
        $this->assertEquals($expected, $actualObject->bar);
    }

    /**
     *
     *
     * @return void
     */
    public function testReferencingIDs()
    {
        $blueprintOne = new Blueprint('someId', 'Foo');
        $blueprintOne->addProperty('bar', '@id:someOtherId');

        $blueprintTwo = new Blueprint('someOtherId', 'Bar');

        $f = function($i) use($blueprintOne, $blueprintTwo) {
            if ($i == 'someOtherId') {
                return $blueprintTwo;
            } else {
                return $blueprintOne;
            }
        };

        /* @var $reader Reader */
        $reader = $this->getMock('Sweetie\Reader\XML', array('getBlueprint'));
        $reader->expects($this->once())
               ->method('getBlueprint')
               ->will($this->returnCallback($f));

        $instance = Binder::boostrap($reader);

        $magic = new Magic($instance);
        $magic->inject($blueprintOne);
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
        $this->_writeFile('/tmp/bind.xml', $xml);

        $reader = new XML();
        $reader->load('/tmp/bind.xml');

        Binder::boostrap($reader);
        Binder::factory('stubTest');
    }

}