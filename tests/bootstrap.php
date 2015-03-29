<?php
chdir(dirname(__FILE__));

require __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function($class) {
    $file = __DIR__ . '/' . str_replace('\\', '//', $class) . '.php';

    if (is_file($file)) {
        include $file;
    }
});
