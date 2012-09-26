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
        Binder::resetInstance();
    }

    public function testInvalidScope()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unknown scope "InvalidScope"');

        $blueprint = new Blueprint('myId', 'Foo');
        $blueprint->setScope('InvalidScope');

        $reader = $this->getMock('Sweetie\\Reader\\XML', array('getBlueprint'));
        $reader->expects($this->once())
               ->method('getBlueprint')
               ->with($this->equalTo($blueprint->getId()))
               ->will($this->returnValue($blueprint));

        Binder::bootstrap($reader);
        Binder::factory($blueprint->getId());
    }

}