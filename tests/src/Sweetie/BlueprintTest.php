<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

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

}