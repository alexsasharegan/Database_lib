<?php

require_once __DIR__."/Exceptions/BadQuery.exception.class.php";

class Database {

  public  $db,
          $_query,
          $queryResult,
          $hostName,
          $dbUserName,
          $dbPassword,
          $databaseName,
          $columns = [];

  public static $DATABASE_CONFIG_OPTIONS = [
    'hostName'     => 'yourHostName',
    'databaseName' => 'yourDatabaseName',
    'dbUserName'   => 'yourUsername',
    'dbPassword'   => 'yourpassword',
  ];

  public static function connect($options = []) {

    if (!empty($options)) {
      $hostName     = !empty($options['hostName']) ? $options['hostName'] : self::$DATABASE_CONFIG_OPTIONS['hostName'];
      $databaseName = !empty($options['databaseName']) ? $options['databaseName'] : self::$DATABASE_CONFIG_OPTIONS['databaseName'];
      $dbUserName   = !empty($options['dbUserName']) ? $options['dbUserName'] : self::$DATABASE_CONFIG_OPTIONS['dbUserName'];
      $dbPassword   = !empty($options['dbPassword']) ? $options['dbPassword'] : self::$DATABASE_CONFIG_OPTIONS['dbPassword'];
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

    if (!empty($options)) {
      $this->hostName     = !empty($options['hostName']) ? $options['hostName'] : self::$DATABASE_CONFIG_OPTIONS['hostName'];
      $this->databaseName = !empty($options['databaseName']) ? $options['databaseName'] : self::$DATABASE_CONFIG_OPTIONS['databaseName'];
      $this->dbUserName   = !empty($options['dbUserName']) ? $options['dbUserName'] : self::$DATABASE_CONFIG_OPTIONS['dbUserName'];
      $this->dbPassword   = !empty($options['dbPassword']) ? $options['dbPassword'] : self::$DATABASE_CONFIG_OPTIONS['dbPassword'];
    } else {
      $this->hostName     = self::$DATABASE_CONFIG_OPTIONS['hostName'];
      $this->databaseName = self::$DATABASE_CONFIG_OPTIONS['databaseName'];
      $this->dbUserName   = self::$DATABASE_CONFIG_OPTIONS['dbUserName'];
      $this->dbPassword   = self::$DATABASE_CONFIG_OPTIONS['dbPassword'];
    }

    $mysqli = new mysqli( $this->hostName, $this->dbUserName, $this->dbPassword, $this->databaseName );

    # Check for errors
    if (mysqli_connect_errno()) { exit(mysqli_connect_error()); }
    if (!$mysqli->set_charset('utf8')) { exit("Error loading character set utf8 for db $databaseName: %s\n".$mysqli->error); }

    $this->db = $mysqli;
  }

  function __destruct() {
    $this->db->close();
  }

  public function query( $query ) {

    $this->_query = $query;

    if ( !$this->queryResult = $this->db->query( $this->_query ) ) {
      throw new BadQuery( $this->_query, $this->db->error );
    }

    return $this->queryResult;
  }

  public function getResults($cb) {

    $args = array_slice(func_get_args(), 1);

    if ( is_callable( $cb ) ) {
      while ( $record = $this->queryResult->fetch_assoc() ) {
        $params = array_merge( [ $record ], $args );
        call_user_func_array( $cb, $params );
      }
    }

  }

  public function getColumns( $tableName, $databaseName ) {

    if ( empty($databaseName) ) {
      $databaseName = $this->databaseName;
    }

    $this->query(
      "SELECT column_name FROM information_schema.columns WHERE table_name = '$tableName' AND table_schema = '$databaseName'"
    );

    $this->getResults(
      function ( $row, $tableName ) {
        foreach ( $row as $index => $columnName ) {
          $this->columns[$tableName][] = $columnName;
        }
      }
      , $tableName
    );

    return $this->columns[$tableName];
  }

}
