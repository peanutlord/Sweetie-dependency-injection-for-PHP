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

    public function testIdReferenceIsDetected()
    {
        $property = new Property('foo', '@id(myId)');

        $this->assertTrue($property->isIdReference());
        $this->assertEquals('myId', $property->getIdFromReference());
    }

    public function testClassReferenceDetected()
    {
        $property = new Property('foo', 'bar');
        $this->assertTrue($property->isClassReference());

        $property = new Property('foo', '@id(foo)');
        $this->assertFalse($property->isClassReference());
    }

    public function testInvokeReferenceDetected()
    {
        $property = new Property('foo', '@invoke(class, method)');
        $this->assertTrue($property->isInvokeReference());

        $this->assertEquals($property->getInvokeParams(), array('class', 'method'));
    }

    public function testMissingArgumentsForTagRaisesException()
    {
        $this->setExpectedException('InvalidArgumentException', 'No arguments supplied within "invoke" tag');

        $property = new Property('foo', '@invoke()');
        $this->assertTrue($property->isInvokeReference());
    }

    public function testReferenceTypeIsCorrect()
    {
        $propertyA = new Property('foo', '@id(bar)');
        $this->assertTrue($propertyA->getReferenceType() === 'id');

        $propertyB = new Property('foo', '@invoke(something)');
        $this->assertTrue($propertyB->getReferenceType() === 'invoke');
    }

    public function testWrongTag()
    {
        $this->setExpectedException('InvalidArgumentException', "Can't parse reference \"@something\", sure its a tag?");

        $property = new Property('foo', '@something');
        $property->getReferenceType();
    }

}