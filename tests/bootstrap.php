<?php
require 'PHPUnit/TestMore.php';
require 'Universal/ClassLoader/BasePathClassLoader.php';

define( 'BASEDIR' , dirname(__DIR__) );
$loader = new \Universal\ClassLoader\BasePathClassLoader(array(
    BASEDIR . '/src',
    BASEDIR . '/vendor/pear'
));
$loader->useIncludePath(true);
$loader->register();
