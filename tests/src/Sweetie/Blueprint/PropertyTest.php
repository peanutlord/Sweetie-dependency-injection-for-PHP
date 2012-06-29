<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Blueprint\Property;

/**
 * Test cases for the blueprint property class
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class PropertyTest extends \TestCase
{

    public function testNameMatches()
    {
        $property = new Property('foo', 'bar');
        $this->assertEquals('foo', $property->getName());
    }

    public function testReferenceMatches()
    {
        $property = new Property('foo', 'bar');
        $this->assertEquals('bar', $property->getReference());
    }

    public function testReferenceIsDetected()
    {
        $property = new Property('foo', '@id:myId');

        $this->assertTrue($property->isIdReference());
        $this->assertEquals('myId', $property->getIdFromReference());
    }

}