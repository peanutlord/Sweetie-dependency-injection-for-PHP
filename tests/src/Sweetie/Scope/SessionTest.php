<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

/**
 * Test cases for the request scope
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
class RequestTest extends \TestCase
{

    public function testStorageWorks()
    {
        $scope = new Sweetie\Scope\Request();
        $scope->store('myClass', new stdClass());

        $this->assertTrue($scope->contains('myClass'));
        $this->assertInstanceOf('stdClass', $scope->retrieve('myClass'));
    }

}