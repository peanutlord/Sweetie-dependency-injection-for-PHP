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
$foo = Binder::factory('someId');
echo $foo->getBar()->sayHello();