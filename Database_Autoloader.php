<?php

function Database_Autoloader($classname) {
  //Can't use __DIR__ as it's only in PHP 5.3+
  $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.$classname.'.class'.'.php';
  if (is_readable($filename)) {
    require_once $filename;
  }
}

if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
  //SPL autoloading was introduced in PHP 5.1.2
  if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
    spl_autoload_register('Database_Autoloader', true, true);
  } else {
    spl_autoload_register('Database_Autoloader');
  }
} else {
  /**
   * Fall back to traditional autoload for old PHP versions
   * @param string $classname The name of the class to load
   */
  function __autoload($classname) {
    Database_Autoloader($classname);
  }
}
