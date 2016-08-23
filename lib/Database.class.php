<?php

/**
 * Database::connect()
 * Public Static method for connecting to a database and returning a handle
 *
 */
class Database {

  public $db;

  public static $DATABASE_CONFIG_OPTIONS = [
    'hostName'     => 'yourHostName',
    'databaseName' => 'yourDatabaseName',
    'dbUserName'   => 'yourUsername',
    'dbPassword'   => 'yourpassword',
  ];

  public static function connect($options = []) {

    if (!empty($options)) {
      $hostName     = !empty($options['hostName'])     ? $options['hostName']     : self::$DATABASE_CONFIG_OPTIONS['hostName'];
      $databaseName = !empty($options['databaseName']) ? $options['databaseName'] : self::$DATABASE_CONFIG_OPTIONS['databaseName'];
      $dbUserName   = !empty($options['dbUserName'])   ? $options['dbUserName']   : self::$DATABASE_CONFIG_OPTIONS['dbUserName'];
      $dbPassword   = !empty($options['dbPassword'])   ? $options['dbPassword']   : self::$DATABASE_CONFIG_OPTIONS['dbPassword'];
    } else {
      $hostName     = self::$DATABASE_CONFIG_OPTIONS['hostName'];
      $databaseName = self::$DATABASE_CONFIG_OPTIONS['databaseName'];
      $dbUserName   = self::$DATABASE_CONFIG_OPTIONS['dbUserName'];
      $dbPassword   = self::$DATABASE_CONFIG_OPTIONS['dbPassword'];
    }

    $mysqli = new mysqli( $hostName, $dbUserName, $dbPassword, $databaseName );

    # Check for errors
    if (mysqli_connect_errno()) { exit(mysqli_connect_error()); }
    if (!$mysqli->set_charset('utf8')) { exit("Error loading character set utf8 for db $databaseName: %s\n".$mysqli->error); }

    return $mysqli;
  }

  function __construct($options = []) {
    $this->db = self::connect($options);
  }

  function __destruct() {
    $this->db->close();
  }

}
