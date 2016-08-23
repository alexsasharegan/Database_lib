<?php

function Database_Autoloader($classname) {
  $filename = __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.$classname.'.class'.'.php';
  if (is_readable($filename)) {
    require_once $filename;
  }
}

spl_autoload_register('Database_Autoloader', true);
