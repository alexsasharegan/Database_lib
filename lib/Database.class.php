<?php

/**
 * Database::connect()
 * Public Static method for connecting to a database and returning a handle
 *
 */
class Database {

  public static function connect($options = []) {

    $DATABASE_CONFIG_OPTIONS = [
      'hostName'     => 'yourHostName',
      'databaseName' => 'yourDatabaseName',
      'dbUserName'   => 'yourUsername',
      'dbPassword'   => 'yourpassword',
    ];

    if (!empty($options)) {
      $hostName     = !empty($options['hostName'])     ? $options['hostName']     : $DATABASE_CONFIG_OPTIONS['hostName'];
      $databaseName = !empty($options['databaseName']) ? $options['databaseName'] : $DATABASE_CONFIG_OPTIONS['databaseName'];
      $dbUserName   = !empty($options['dbUserName'])   ? $options['dbUserName']   : $DATABASE_CONFIG_OPTIONS['dbUserName'];
      $dbPassword   = !empty($options['dbPassword'])   ? $options['dbPassword']   : $DATABASE_CONFIG_OPTIONS['dbPassword'];
    } else {
      $hostName     = $DATABASE_CONFIG_OPTIONS['hostName'];
      $databaseName = $DATABASE_CONFIG_OPTIONS['databaseName'];
      $dbUserName   = $DATABASE_CONFIG_OPTIONS['dbUserName'];
      $dbPassword   = $DATABASE_CONFIG_OPTIONS['dbPassword'];
    }

    $mysqli = new mysqli( $hostName, $dbUserName, $dbPassword, $databaseName );

    # Check for errors
    if (mysqli_connect_errno()) { exit(mysqli_connect_error()); }
    if (!$mysqli->set_charset('utf8')) { exit("Error loading character set utf8 for db $databaseName: %s\n".$mysqli->error); }

    return $mysqli;
  }

}
