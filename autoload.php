<?php

$pathToRoot = dirname(__DIR__);

spl_autoload_register(function ($class) use ($pathToRoot) {
    if (substr($class, 0, 15) === 'providerBundle\\') {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $file = $pathToRoot  . DIRECTORY_SEPARATOR . $path . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
}, true, true);
