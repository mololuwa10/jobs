<?php

function autoload($className): void
{
    $file = '' . str_replace('\\', '/', $className) . '.php';
    require $file;
}

spl_autoload_register('autoload');