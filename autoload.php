<?php

function PSolrAutoload($class)
{
    $path = str_replace('\\', '/', $class);

    $file = LIBRARY_DIR . 
            DIRECTORY_SEPARATOR . $path . '.php';

    if (file_exists($file)) {
        include_once $file;
    }
    
}

spl_autoload_register('PSolrAutoload');