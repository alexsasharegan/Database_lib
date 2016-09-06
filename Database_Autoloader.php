<?php

function Database_Autoloader($classname) {
  $pathParts = explode("\\", $classname);
  array_shift($pathParts);
  array_unshift($pathParts, 'src');
  $path = implode(DIRECTORY_SEPARATOR, $pathParts);
  $filename = __DIR__.DIRECTORY_SEPARATOR.$path.'.php';
  if (is_readable($filename)) {
    require_once $filename;
  }
}

spl_autoload_register('Database_Autoloader', true);
