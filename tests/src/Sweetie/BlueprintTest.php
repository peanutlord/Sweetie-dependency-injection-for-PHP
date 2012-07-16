<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Blueprint\Property;
use Sweetie\Blueprint;

/**
 * Test cases for the blueprint class
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class BlueprintTest extends \TestCase
{

    public function testBlueprintClass()
    {
        $blueprint = new Blueprint('foo', 'bar');
        $this->assertEquals('bar', $blueprint->getClass());
    }

    public function testAddProperty()
    {
        $blueprint = new Blueprint('foo', 'bar');
        $first = $blueprint->addProperty('test1', 'test1');
        $second = $blueprint->addProperty('test2', 'test2');
        $third = $blueprint->addProperty('test3', 'test3');

        $this->assertEquals(3, count($blueprint));
    }

    public function testBlueprintPropertyGetsReplaced()
    {
        $blueprint = new Blueprint('foo', 'bar');
        $first = $blueprint->addProperty('test1', 'test1');
        $second = $blueprint->addProperty('test2', 'test2');

        $currentProperty = $blueprint->current();

        $this->assertTrue($currentProperty instanceof Property);
        $this->assertEquals($first->getName(), $currentProperty->getReference());

        $blueprint->addProperty('test1', 'test3');

        $replaced = $blueprint->current();
        $this->assertEquals('test1', $replaced->getName());
        $this->assertEquals('test3', $replaced->getReference());
    }

    public function testFindsProperty()
    {
        $blueprint = new Blueprint('foo', 'bar');
        $blueprint->addProperty('test1', 'test1');
        $blueprint->addProperty('test2', 'test2');

        $reflection = new ReflectionObject($blueprint);

        /*@var $method ReflectionMethod */
        $method = $reflection->getMethod('_findProperty');
        $method->setAccessible(true);

        $this->assertEquals(1, $method->invokeArgs($blueprint, array('test2')));
        $this->assertFalse($method->invokeArgs($blueprint, array('test3')));
    }

}