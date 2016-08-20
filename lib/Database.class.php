<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'Connection.config.php';

/**
 * Database::connect()
 * Public Static method for connecting to a database
 *
 */
class Database {

  public static function connect($options) {
    global $default_connection_settings;

    if (!empty($options)) {
      $hostName     = !empty($options['hostName'])     ? $options['hostName']     : $default_connection_settings['hostName'];
      $databaseName = !empty($options['databaseName']) ? $options['databaseName'] : $default_connection_settings['databaseName'];
      $dbUserName   = !empty($options['dbUserName'])   ? $options['dbUserName']   : $default_connection_settings['dbUserName'];
      $dbPassword   = !empty($options['dbPassword'])   ? $options['dbPassword']   : $default_connection_settings['dbPassword'];
    }

    $mysqli = new mysqli( $hostName, $dbUserName, $dbPassword, $databaseName );

    // Check for errors
    if (mysqli_connect_errno()) { exit(mysqli_connect_error()); }
    if (!$mysqli->set_charset('utf8')) { exit("Error loading character set utf8 for db $databaseName: %s\n".$mysqli->error); }

    return $mysqli;
  }

}
