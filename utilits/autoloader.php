<?php

spl_autoload_register(function($className){

    $path = ROOT . '/' .strtolower(str_replace('\\', '/',$className)) . '.php';

    if (file_exists($path)) {
        include $path;
    }
});

function dd($test, $die = true)
{
    echo '<pre>' . print_r($test, 1) . '</pre>';
    if($die)die();
}
?>