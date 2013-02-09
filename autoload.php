<?php

function PSolrAutoload($class)
{
    $path = array_slice(explode("\\", $class), 1);
    $file = LIBRARY_DIR . 
            DIRECTORY_SEPARATOR . 
            implode(DIRECTORY_SEPARATOR, $path) . '.php';
    
    if (file_exists($file)) {
        include_once $file;
    }
}

spl_autoload_register('PSolrAutoload');