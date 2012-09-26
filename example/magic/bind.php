<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

include '../init.php';

use Sweetie\Binder;

$reader = new \Sweetie\Reader\XML();
$reader->load('bind.xml');

Binder::bootstrap($reader);

/* @var $foo Foo */
$foo = Binder::factory('withIdReference');
echo $foo->getBar()->sayHello();

/* @var $foo2 Foo */
$foo2 = Binder::factory('withoutIdReference');
echo $foo2->getBar()->sayHello();

/* @var $foo3 Foo */
$foo3 = Binder::factory('withTemplating');
echo $foo3->getBar()->sayHello();