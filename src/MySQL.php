<?php

namespace Database;
use Database\Exceptions\BadQuery;

class MySQL {

  public  $db = null,
          $_query = '',
          $queryResult = null,
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

    $mysqli = new \mysqli( $hostName, $dbUserName, $dbPassword, $databaseName );

    # Check for errors
    if ( mysqli_connect_errno()) {
      exit( mysqli_connect_error() );
    }

    if ( !$mysqli->set_charset( 'utf8' ) ) {
      exit( "Error loading character set utf8 for db $databaseName: %s\n".$mysqli->error );
    }

    return $mysqli;
  }

  public static function SQLDateToPath( $SQLDate, $timezone = "America/Phoenix" ) {
    date_default_timezone_set( $timezone );
    $timeStamp = strtotime( $SQLDate );

    return implode( DIRECTORY_SEPARATOR, [
      date('Y', $timeStamp),
      date('m', $timeStamp),
      date('d', $timeStamp),
    ]);
  }

  public static function getSQLDate( $timezone = "America/Phoenix" ) {
    date_default_timezone_set( $timezone );
    return date('Y-m-d H:i:s');
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

    $mysqli = new \mysqli( $this->hostName, $this->dbUserName, $this->dbPassword, $this->databaseName );

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

    return $this;
  }

  public function getLastQuery() {
    if ( isset( $this->_query ) ) {
      return $this->_query;
    }
    return null;
  }

  public function iterateResult( $cb ) {
    $args = array_slice( func_get_args(), 1 );

    if ( is_callable( $cb ) ) {
      while ( $record = $this->queryResult->fetch_assoc() ) {
        $params = array_merge( [ $record ], $args );
        call_user_func_array( $cb, $params );
      }
    }

    return $this;
  }

  public function getResult() {
    if ( isset( $this->queryResult ) ) {
      return $this->queryResult;
    }
    return null;
  }

  public function getColumns( $tableName, $databaseName = '' ) {
    if ( empty( $databaseName ) ) {
      $databaseName = $this->databaseName;
    }

    $this->query(
      "SELECT column_name FROM information_schema.columns WHERE table_name = '$tableName' AND table_schema = '$databaseName'"
    );

    $this->iterateResult(
      function ( $row ) use ( $tableName ) {
        foreach ( $row as $index => $columnName ) {
          $this->columns[$tableName][] = $columnName;
        }
      }
    );

    return $this->columns[$tableName];
  }

  public function buildInserts( array $insertList ) {
    $escapedKeyValuePairs = $this->escapeKeyValuePairs( $insertList );

    return [
      'keys' => implode(',', $escapedKeyValuePairs['keys']),
      'values' => implode(',', $escapedKeyValuePairs['values'])
    ];
  }

  public function buildUpdate( array $insertList ) {
    $updateList = [];
    $escapedKeyValuePairs = $this->escapeKeyValuePairs( $insertList );
    $length = count( $escapedKeyValuePairs['keys'] );

    for ( $i=0 ; $i < $length ; $i++ ) {
      $updateList[] = "{$escapedKeyValuePairs['keys'][$i]}={$escapedKeyValuePairs['values'][$i]}";
    }

    return implode( ', ', $updateList );
  }

  public function escapeKeyValuePairs( array $assoc_array ) {
    $keys = [];
    $values = [];

    foreach ( $assoc_array as $key => $val ) {
      switch ( gettype( $val ) ) {
        case 'object': # handle objects
        case 'array': # and arrays the same
          $safeKey = $this->db->real_escape_string( trim( $key ) );
          $keys[] = ("`$safeKey`");

          $jsonStrValue = json_encode($val);
          $safeValue = $this->db->real_escape_string( $jsonStrValue );
          $values[] = "'$safeValue'";
          break;
        case 'string': # escape key & value and wrap in quotes
          $safeKey = $this->db->real_escape_string( trim( $key ) );
          $keys[] = ("`$safeKey`");

          $safeValue = $this->db->real_escape_string( $val );
          $values[] = "'$safeValue'";
          break;
        case 'boolean': # booleans
        case 'double': # doubles
        case 'integer': # & integers don't need escaping or quotations
          $safeKey = $this->db->real_escape_string( trim( $key ) );
          $keys[] = "`$safeKey`";

          $values[] = $val;
          break;
        default: # if the value doesn't match these types,
          break; # skip inclusion
      }
    }

    return [
      'keys' => $keys,
      'values' => $values,
    ];
  }

}
