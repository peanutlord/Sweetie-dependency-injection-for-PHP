<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\ClassBindings;

/**
 * Test cases for the class binder
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class ClassBindingsTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @return void
     */
    public function testStoresInformation()
    {
        $binding = new ClassBindings('Foo', array('bar' => 'Bar'));

        $this->assertEquals('Foo', $binding->getClass());
        $this->assertContains('bar', $binding->getProperties());
        $this->assertEquals('Bar', $binding->getReference('bar'));
    }

}