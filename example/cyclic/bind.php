<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

include '../init.php';

try {
    $reader = new \Sweetie\Reader\XML();
    $reader->load('bind.xml');
} catch(\Exception $e) {
    echo "You naughty boy!\n";
}