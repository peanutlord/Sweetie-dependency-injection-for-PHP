<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Blueprint\Property;
use Sweetie\Blueprint;
use Sweetie\Binder;
use Sweetie\Reader\XML;

/**
 * Test cases for the binder class
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class BinderTest extends \TestCase
{

    public function setUp()
    {
        parent::setUp();

        Binder::resetInstance();
    }

    public function testSessionScope()
    {
        $blueprint = new Blueprint('blueprintId', 'Foo');
        $blueprint->setScope(Blueprint::SCOPE_SESSION);

        $that = $this;
        $handler = function($key, $value = null) use ($that, $blueprint) {
            if ($value == null) {
                $that->assertEquals($blueprint->getId(), $key);
            } else {
                $that->assertEquals($blueprint->getId(), $key);
                $that->assertInstanceOf('Foo', $value);
            }
        };

        $reader = $this->getMock('Sweetie\\Reader\\XML', array('getBlueprint'));
        $reader->expects($this->once())
               ->method('getBlueprint')
               ->with($this->equalTo('stubTest'))
               ->will($this->returnValue($blueprint));

        Binder::setSessionHandler($handler);
        Binder::boostrap($reader);
        Binder::factory('stubTest');
    }

    public function testRequestScope()
    {
        $blueprint = new Blueprint('blueprintId', 'Bar');
        $blueprint->setScope(Blueprint::SCOPE_REQUEST);

        $reader = $this->getMock('Sweetie\\Reader\\XML', array('getBlueprint'));
        $reader->expects($this->once())
               ->method('getBlueprint')
               ->with($this->equalTo('stubTest'))
               ->will($this->returnValue($blueprint));

        Binder::boostrap($reader);
        Binder::factory('stubTest');
    }

}