<?php

namespace Database;

use Database\Exceptions\BadQuery;

/**
 * Class MySQL
 * @package Database
 */
class MySQL {
	
	const CONFIG = './database.json';
	
	public $db = null;
	public $queryResult = null;
	public $columns = [];
	
	private $_host;
	private $_database;
	private $_username;
	private $_password;
	
	private $_query = '';
	private $_logs = [];
	
	/**
	 * @param string $configFile
	 * @param array $options
	 * @return \mysqli
	 */
	public static function connect( $configFile = './database.json', array $options = [] ) {
		
		if ( empty($configFile) ) {
			$host     = $options['host'];
			$database = $options['database'];
			$username = $options['_username'];
			$password = $options['password'];
		} elseif ( !empty($configFile) && empty($options) ) {
			$config   = json_decode( file_get_contents( $configFile ) );
			$host     = $config->host;
			$database = $config->database;
			$username = $config->username;
			$password = $config->password;
		} elseif ( !empty($configFile) && !empty($options) ) {
			$config   = json_decode( file_get_contents( $configFile ) );
			$host     = !empty($options['host']) ? $options['host'] : $config->host;
			$database = !empty($options['database']) ? $options['database'] : $config->database;
			$username = !empty($options['_username']) ? $options['_username'] : $config->username;
			$password = !empty($options['password']) ? $options['password'] : $config->password;
		}
		
		$mysqli = new \mysqli( $host, $username, $password, $database );
		
		# Check for errors
		if ( mysqli_connect_errno() ) {
			exit(mysqli_connect_error());
		}
		
		if ( !$mysqli->set_charset( 'utf8' ) ) {
			exit("Error loading character set utf8 for db $database: %s\n" . $mysqli->error);
		}
		
		return $mysqli;
	}
	
	/**
	 * @param string $SQLDate
	 * @param string $timezone
	 * @return string
	 */
	public static function SQLDateToPath( $SQLDate, $timezone = "America/Phoenix" ) {
		date_default_timezone_set( $timezone );
		$timeStamp = strtotime( $SQLDate );
		
		return implode( DIRECTORY_SEPARATOR, [
			date( 'Y', $timeStamp ),
			date( 'm', $timeStamp ),
			date( 'd', $timeStamp ),
		] );
	}
	
	/**
	 * @param string $timezone
	 * @return false|string
	 */
	public static function getSQLDate( $timezone = "America/Phoenix" ) {
		date_default_timezone_set( $timezone );
		return date( 'Y-m-d H:i:s' );
	}
	
	/**
	 * MySQL constructor.
	 * @param string $configFile
	 * @param array $options
	 */
	function __construct( $configFile = './database.json', array $options = [] ) {
		
		if ( empty($configFile) ) {
			$this->_host     = $options['host'];
			$this->_database = $options['database'];
			$this->_username = $options['_username'];
			$this->_password = $options['password'];
		} elseif ( !empty($configFile) && empty($options) ) {
			$config          = json_decode( file_get_contents( $configFile ) );
			$this->_host     = $config->host;
			$this->_database = $config->database;
			$this->_username = $config->username;
			$this->_password = $config->password;
		} elseif ( !empty($configFile) && !empty($options) ) {
			$config          = json_decode( file_get_contents( $configFile ) );
			$this->_host     = !empty($options['host']) ? $options['host'] : $config->host;
			$this->_database = !empty($options['database']) ? $options['database'] : $config->database;
			$this->_username = !empty($options['_username']) ? $options['_username'] : $config->username;
			$this->_password = !empty($options['password']) ? $options['password'] : $config->password;
		}
		
		$mysqli = new \mysqli( $this->_host, $this->_username, $this->_password, $this->_database );
		
		# Check for errors
		if ( mysqli_connect_errno() ) {
			exit(mysqli_connect_error());
		}
		if ( !$mysqli->set_charset( 'utf8' ) ) {
			exit("Error loading character set utf8 for db {$this->_database}: %s\n" . $mysqli->error);
		}
		
		$this->db = $mysqli;
	}
	
	/**
	 * closes the connection to mysql
	 */
	function __destruct() {
		$this->db->close();
	}
	
	/**
	 * @param string $query
	 * @return MySQL $this
	 * @throws BadQuery
	 */
	public function query( $query ) {
		$this->setQuery( $query );
		
		if ( !$this->queryResult = $this->db->query( $this->_query ) ) {
			throw new BadQuery( $this->_query, $this->db->error );
		}
		
		return $this;
	}
	
	/**
	 * @param string $query
	 */
	public function setQuery( $query ) {
		$this->_query  = $query;
		$this->_logs[] = $query;
	}
	
	/**
	 * @return null|string
	 */
	public function getLastQuery() {
		if ( isset($this->_query) ) {
			return $this->_query;
		}
		return null;
	}
	
	/**
	 * @param callable $cb
	 * @return MySQL $this
	 */
	public function iterateResult( callable $cb ) {
		$args = array_slice( func_get_args(), 1 );
		
		if ( is_callable( $cb ) ) {
			while ( $record = $this->queryResult->fetch_assoc() ) {
				$params = array_merge( [ $record ], $args );
				call_user_func_array( $cb, $params );
			}
		}
		
		return $this;
	}
	
	/**
	 * @return null|\mysqli_result
	 */
	public function getResult() {
		if ( isset($this->queryResult) ) {
			return $this->queryResult;
		}
		return null;
	}
	
	/**
	 * @return string
	 */
	public function getError() {
		return $this->db->error;
	}
	
	/**
	 * @return null|integer
	 */
	public function affectedRows() {
		if ( isset($this->queryResult) ) {
			return $this->queryResult->num_rows;
		}
		return null;
	}
	
	/**
	 * @return null|integer
	 */
	public function insertId() {
		$id = $this->db->insert_id;
		if ( $id !== 0 ) {
			return $id;
		}
		return null;
	}
	
	/**
	 * @param string $table
	 * @param array $insertPairs
	 * @param bool $update
	 * @return bool|integer
	 */
	public function insert( $table, array $insertPairs, $update = false ) {
		if ( empty($insertPairs) ) {
			return false;
		}
		
		$escapedInserts = $this->buildInserts( $insertPairs );
		
		if ( $update ) {
			$updateStr = $this->buildUpdate( $insertPairs );
			$this->query( "INSERT INTO `$table` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']}) ON DUPLICATE KEY UPDATE $updateStr;" );
		} else {
			$this->query( "INSERT INTO `$table` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']});" );
		}
		
		if ( $this->getResult() === true ) {
			return $this->db->insert_id;
		} else {
			return false;
		}
	}
	
	/**
	 * @param string $table
	 * @param array $insertPairs
	 * @param array $updatePairs
	 * @return bool|integer
	 */
	public function insertOnUpdate( $table, array $insertPairs, array $updatePairs ) {
		if ( empty($insertPairs) || empty($updatePairs) ) {
			return false;
		}
		
		$escapedInserts = $this->buildInserts( $insertPairs );
		$updateStr      = $this->buildUpdate( $updatePairs );
		
		$this->query( "INSERT INTO `$table` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']}) ON DUPLICATE KEY UPDATE $updateStr;" );
		
		if ( $this->getResult() === true ) {
			return $this->db->insert_id;
		} else {
			return false;
		}
	}
	
	/**
	 * @param string $table
	 * @param string $database
	 * @return array
	 */
	public function getColumns( $table, $database = '' ) {
		if ( empty($database) ) {
			$database = $this->_database;
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
	
	/**
	 * @param array $insertList
	 * @return array
	 */
	public function buildInserts( array $insertList ) {
		$escapedKeyValuePairs = $this->escapeKeyValuePairs( $insertList );
		
		return [
			'keys' => implode( ',', $escapedKeyValuePairs['keys'] ),
			'values' => implode( ',', $escapedKeyValuePairs['values'] )
		];
	}
	
	/**
	 * @param array $insertList
	 * @return string
	 */
	public function buildUpdate( array $insertList ) {
		$updateList           = [];
		$escapedKeyValuePairs = $this->escapeKeyValuePairs( $insertList );
		$length               = count( $escapedKeyValuePairs['keys'] );
		
		for ( $i = 0; $i < $length; $i++ ) {
			$updateList[] = "{$escapedKeyValuePairs['keys'][$i]}={$escapedKeyValuePairs['values'][$i]}";
		}
		
		return implode( ', ', $updateList );
	}
	
	/**
	 * @param $string
	 * @return string
	 */
	public function escape( $string ) {
		return $this->db->real_escape_string( $string );
	}
	
	/**
	 * @param array $assoc_array
	 * @return array
	 */
	public function escapeKeyValuePairs( array $assoc_array ) {
		$keys   = [];
		$values = [];
		
		foreach ( $assoc_array as $key => $val ) {
			switch ( gettype( $val ) ) {
				case 'object': # handle objects
				case 'array': # and arrays the same
					$safeKey = $this->db->real_escape_string( trim( $key ) );
					$keys[]  = ("`$safeKey`");
					
					$jsonStrValue = json_encode( $val );
					$safeValue    = $this->db->real_escape_string( $jsonStrValue );
					$values[]     = "'$safeValue'";
					break;
				case 'string': # escape key & value and wrap in quotes
					$safeKey = $this->db->real_escape_string( trim( $key ) );
					$keys[]  = ("`$safeKey`");
					
					$safeValue = $this->db->real_escape_string( $val );
					$values[]  = "'$safeValue'";
					break;
				case 'boolean': # booleans
				case 'double': # doubles
				case 'integer': # & integers don't need escaping or quotations
					$safeKey = $this->db->real_escape_string( trim( $key ) );
					$keys[]  = "`$safeKey`";
					
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
