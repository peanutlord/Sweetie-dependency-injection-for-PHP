<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

use Sweetie\Stack;
use Sweetie\Reader\XML;

/**
 * Test cases for the class binder
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class StackTest extends \TestCase
{

    public function testClearStack()
    {
        $stack = new Stack();
        $stack->push('foo');
        $stack->push('bar');
        $stack->push('baz');

        $stack->clear();
        $this->assertEquals(0, count($stack));
    }

    public function testStackContainsElement()
    {
        $stack = new Stack();
        $stack->push('foo');
        $stack->push('bar');
        $stack->push('baz');

        $this->assertTrue($stack->contains('bar'));
    }

}