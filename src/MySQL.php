<?php

namespace Database;
use Database\Exceptions\BadQuery;

class MySQL {

  const CONFIG = './database.json';

  public  $db = null,
          $_query = '',
          $queryResult = null,
          $columns = [];

  public static function connect( $configFile = './database.json', array $options = [] ) {

    if ( empty( $configFile ) ) {
      $host     = $options['host'];
      $database = $options['database'];
      $username = $options['username'];
      $password = $options['password'];
    } elseif ( !empty( $configFile ) && empty( $options ) ) {
      $config = json_decode( file_get_contents( $configFile ) );
      $host     = $config->host;
      $database = $config->database;
      $username = $config->username;
      $password = $config->password;
    } elseif ( !empty( $configFile ) && !empty( $options ) ) {
      $config   = json_decode( file_get_contents( $configFile ) );
      $host     = !empty($options['host'])     ? $options['host']     : $config->host;
      $database = !empty($options['database']) ? $options['database'] : $config->database;
      $username = !empty($options['username']) ? $options['username'] : $config->username;
      $password = !empty($options['password']) ? $options['password'] : $config->password;
    }

    $mysqli = new \mysqli( $host, $username, $password, $database );

    # Check for errors
    if ( mysqli_connect_errno()) {
      exit( mysqli_connect_error() );
    }

    if ( !$mysqli->set_charset( 'utf8' ) ) {
      exit( "Error loading character set utf8 for db $database: %s\n".$mysqli->error );
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

  function __construct( $configFile = './database.json', array $options = [] ) {

    if ( empty( $configFile ) ) {
      $this->host     = $options['host'];
      $this->database = $options['database'];
      $this->username = $options['username'];
      $this->password = $options['password'];
    } elseif ( !empty( $configFile ) && empty( $options ) ) {
      $config = json_decode( file_get_contents( $configFile ) );
      $this->host     = $config->host;
      $this->database = $config->database;
      $this->username = $config->username;
      $this->password = $config->password;
    } elseif ( !empty( $configFile ) && !empty( $options ) ) {
      $config = json_decode( file_get_contents( $configFile ) );
      $this->host     = !empty($options['host'])     ? $options['host']     : $config->host;
      $this->database = !empty($options['database']) ? $options['database'] : $config->database;
      $this->username = !empty($options['username']) ? $options['username'] : $config->username;
      $this->password = !empty($options['password']) ? $options['password'] : $config->password;
    }

    $mysqli = new \mysqli( $this->host, $this->username, $this->password, $this->database );

    # Check for errors
    if ( mysqli_connect_errno() ) {
      exit( mysqli_connect_error() );
    }
    if ( !$mysqli->set_charset( 'utf8' ) ) {
      exit( "Error loading character set utf8 for db {$this->database}: %s\n" . $mysqli->error );
    }

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

  public function insert( $table, array $insertPairs, $update = false ) {
    if ( empty( $insertPairs ) ) {
      return false;
    }

    $escapedInserts = $this->buildInserts( $insertPairs );

    if ( $update ) {
      $updateStr = $this->buildUpdate( $insertPairs );
      $this->query("INSERT INTO `$table` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']}) ON DUPLICATE KEY UPDATE $updateStr;");
    } else {
      $this->query("INSERT INTO `$table` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']});");
    }

    if ($this->getResult() === true) {
      return $this->db->insert_id;
    } else {
      return false;
    }
  }

  public function insertOnUpdate( $table, array $insertPairs, array $updatePairs ) {
    if ( empty( $insertPairs ) || empty( $updatePairs ) ) {
      return false;
    }

    $escapedInserts = $this->buildInserts( $insertPairs );
    $updateStr = $this->buildUpdate( $updatePairs );

    $this->query("INSERT INTO `$table` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']}) ON DUPLICATE KEY UPDATE $updateStr;");

    if ($this->getResult() === true) {
      return $this->db->insert_id;
    } else {
      return false;
    }
  }

  public function getColumns( $table, $database = '' ) {
    if ( empty( $database ) ) {
      $database = $this->database;
    }

    $this->query(
      "SELECT column_name FROM information_schema.columns WHERE table_name = '$table' AND table_schema = '$database'"
    );

    $this->iterateResult(
      function ( $row ) use ( $table ) {
        foreach ( $row as $index => $columnName ) {
          $this->columns[$table][] = $columnName;
        }
      }
    );

    return $this->columns[$table];
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
