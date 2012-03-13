<?php
// +----------------------------------------------------------+
// | Sweetie Dependency Injection 2012                        |
// +----------------------------------------------------------+

/**
 * Bootstrap to enable autoloading
 *
 * @author Christopher Marchfelder <marchfelder@googlemail.com>
 * @license MIT
 */
define('SWEETIE_PATH', realpath(__DIR__.'/../src'));

$f = function($name) {
    $parts = explode('\\', $name);

    $path = sprintf('%s/%s.php', SWEETIE_PATH, implode(DIRECTORY_SEPARATOR, $parts));
    include $path;
};

spl_autoload_register($f);