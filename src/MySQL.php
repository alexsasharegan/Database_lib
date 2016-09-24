<?php

namespace Database;

use Database\Exceptions\BadQuery;
use Database\Utils\Timer;

/**
 * Class MySQL
 * @package Database
 */
class MySQL {
	
	/**
	 *
	 */
	const CONFIG = './database.json';
	
	/**
	 * @var \mysqli|null
	 */
	public $db = NULL;
	/**
	 * @var
	 */
	public $_where;
	/**
	 * @var \mysqli_result|null
	 */
	public $queryResult = NULL;
	/**
	 * @var array
	 */
	public $columns = [];
	
	/**
	 * @var string
	 */
	private $_host;
	/**
	 * @var string
	 */
	private $_database;
	/**
	 * @var string
	 */
	private $_username;
	/**
	 * @var string
	 */
	private $_password;
	
	/**
	 * @var string
	 */
	private $_query = '';
	/**
	 * @var array
	 */
	private $_logs = [];
	
	/**
	 * @var array
	 */
	private $createdTables = [];
	
	/**
	 * @var array
	 */
	private $_ids = [];
	
	/**
	 * @param string $configFile
	 * @param array  $options
	 *
	 * @return \mysqli
	 */
	public static function connect( $configFile = './database.json', array $options = [] )
	{
		
		if ( empty($configFile) )
		{
			$host     = $options['host'];
			$database = $options['database'];
			$username = $options['username'];
			$password = $options['password'];
		}
		elseif ( ! empty($configFile) && empty($options) )
		{
			$config   = json_decode( file_get_contents( $configFile ) );
			$host     = $config->host;
			$database = $config->database;
			$username = $config->username;
			$password = $config->password;
		}
		elseif ( ! empty($configFile) && ! empty($options) )
		{
			$config   = json_decode( file_get_contents( $configFile ) );
			$host     = ! empty($options['host']) ? $options['host'] : $config->host;
			$database = ! empty($options['database']) ? $options['database'] : $config->database;
			$username = ! empty($options['username']) ? $options['username'] : $config->username;
			$password = ! empty($options['password']) ? $options['password'] : $config->password;
		}
		
		$mysqli = new \mysqli( $host, $username, $password, $database );
		
		# Check for errors
		if ( mysqli_connect_errno() )
		{
			exit(mysqli_connect_error());
		}
		
		if ( ! $mysqli->set_charset( 'utf8' ) )
		{
			exit("Error setting character set = utf-8 for database $database: %s\n" . $mysqli->error);
		}
		
		return $mysqli;
	}
	
	/**
	 * @param string $SQLDate
	 * @param string $timezone
	 *
	 * @return string
	 */
	public static function SQLDateToPath( $SQLDate, $timezone = "America/Phoenix" )
	{
		date_default_timezone_set( $timezone );
		$timeStamp = strtotime( $SQLDate );
		
		return implode( DIRECTORY_SEPARATOR, [
			date( 'Y', $timeStamp ),
			date( 'm', $timeStamp ),
			date( 'd', $timeStamp ),
		] );
	}
	
	/**
	 * Get the current time formatted for MySQL
	 *
	 * @param string $timezone
	 *
	 * @return false|string
	 */
	public static function getSQLDate( $timezone = "America/Phoenix" )
	{
		date_default_timezone_set( $timezone );
		
		return date( 'Y-m-d H:i:s' );
	}
	
	/**
	 * Get the current time formatted for MySQL
	 *
	 * @param string $timezone
	 *
	 * @return false|string
	 */
	public static function now( $timezone = "America/Phoenix" )
	{
		return self::getSQLDate( $timezone );
	}
	
	/**
	 * @var string
	 */
	public static $lc_alpha = 'abcdefghijklmnopqrstuvwxyz';
	
	/**
	 * @var string
	 */
	public static $numeric = '0123456789';
	
	/**
	 * @var string
	 */
	public static $hex = '0123456789abcdef';
	
	/**
	 * Generate a variable-length random string of alpha characters. Defaults to lower case.
	 *
	 * Set $caseSensitive = TRUE to return both upper & lower case alpha characters.
	 *
	 * @param      $length
	 * @param bool $caseSensitive
	 *
	 * @return string
	 */
	public static function randomString( $length, $caseSensitive = FALSE )
	{
		if ( $caseSensitive )
		{
			$characters = self::$lc_alpha . strtoupper( self::$lc_alpha );
		}
		else
		{
			$characters = self::$lc_alpha;
		}
		
		return self::_randomString( $characters, $length );
	}
	
	/**
	 * Generate a variable-length random string of alpha-numeric characters. Defaults to lower case.
	 *
	 * Set $caseSensitive = TRUE to return both upper & lower case alpha-numeric characters.
	 *
	 * @param      $length
	 * @param bool $caseSensitive
	 *
	 * @return string
	 */
	public static function randomAlphaNumeric( $length, $caseSensitive = FALSE )
	{
		if ( $caseSensitive )
		{
			$characters = self::$numeric . self::$lc_alpha . strtoupper( self::$lc_alpha );
		}
		else
		{
			$characters = self::$numeric . self::$lc_alpha;
		}
		
		return self::_randomString( $characters, $length );
	}
	
	/**
	 * Generate a variable-length random string of hexidecimal characters. Defaults to lower case.
	 *
	 * @param      $length
	 *
	 * @return string
	 */
	public static function randomHex( $length )
	{
		$characters = self::$hex;
		
		return self::_randomString( $characters, $length );
	}
	
	/**
	 * @param $characterSet
	 * @param $length
	 *
	 * @return string
	 */
	private static function _randomString( $characterSet, $length )
	{
		$charLastIndex = (strlen( $characterSet ) - 1);
		$string        = '';
		
		for ( $i = 0; $i < $length; $i++ )
		{
			$string .= $characterSet[ mt_rand( 0, $charLastIndex ) ];
		}
		
		return $string;
	}
	
	/**
	 * MySQL constructor. Pass in the path to a json config file.
	 *
	 * Recommended to always use __DIR__ to qualify the path.
	 *
	 * @param string $configFile
	 * @param array  $options
	 */
	function __construct( $configFile = './database.json', array $options = [] )
	{
		
		if ( empty($configFile) )
		{
			$this->_host     = $options['host'];
			$this->_database = $options['database'];
			$this->_username = $options['username'];
			$this->_password = $options['password'];
		}
		elseif ( ! empty($configFile) && empty($options) )
		{
			$config          = json_decode( file_get_contents( $configFile ) );
			$this->_host     = $config->host;
			$this->_database = $config->database;
			$this->_username = $config->username;
			$this->_password = $config->password;
		}
		elseif ( ! empty($configFile) && ! empty($options) )
		{
			$config          = json_decode( file_get_contents( $configFile ) );
			$this->_host     = ! empty($options['host']) ? $options['host'] : $config->host;
			$this->_database = ! empty($options['database']) ? $options['database'] : $config->database;
			$this->_username = ! empty($options['username']) ? $options['username'] : $config->username;
			$this->_password = ! empty($options['password']) ? $options['password'] : $config->password;
		}
		
		$mysqli = new \mysqli( $this->_host, $this->_username, $this->_password, $this->_database );
		
		# Check for errors
		if ( mysqli_connect_errno() )
		{
			exit(mysqli_connect_error());
		}
		if ( ! $mysqli->set_charset( 'utf8' ) )
		{
			exit("Error loading character set utf8 for db {$this->_database}: %s\n" . $mysqli->error);
		}
		
		$this->db = $mysqli;
	}
	
	/**
	 * Switch a database by passing the database name.
	 * Automatically closes the previous connection.
	 * (uses current connection settings)
	 *
	 * Returns the instance for chaining.
	 *
	 * @param $database
	 *
	 * @return $this
	 */
	public function switchDatabase( $database )
	{
		$this->db->close();
		
		$db = self::connect( NULL, [
			'host'     => $this->_host,
			'database' => $database,
			'username' => $this->_username,
			'password' => $this->_password,
		] );
		
		$this->db = $db;
		
		return $this;
	}
	
	/**
	 * Automatically closes the connection to mysql
	 */
	function __destruct()
	{
		if ( ! is_null( $this->db ) )
		{
			$this->db->close();
		}
	}
	
	/**
	 * Perform a raw SQL query. Does not return the result object,
	 * but instead returns the MySQL instance for chaining.
	 *
	 * @see MySQL::getResult() for getting the raw result object.
	 *
	 * @param string $query
	 *
	 * @return MySQL $this
	 * @throws BadQuery
	 */
	public function query( $query )
	{
		$this->setQuery( $query );
		
		$time = new Timer();
		
		if ( ! $this->queryResult = $this->db->query( $this->_query ) )
		{
			throw new BadQuery( $this->_query, $this->db->error, $this->getLogs() );
		}
		
		$this->_logs[] = [ 'query' => array_pop( $this->_logs ), 'time' => $time->stop(), ];
		
		return $this;
	}
	
	/**
	 * Select a record set by passing in a table,
	 * an array of fields to select,
	 * and [optional] a where clause.
	 * Returns MySQL instance for chaining
	 *
	 * Where clauses can be:
	 * - a string (the raw statement: "WHERE `field`=value"; You must escape values)
	 * - an array: [ 'key' => value ] @see http://medoo.in/api/where since their api is similar
	 * - an integer: this will yield a "WHERE `id`=$intValue" where clause for convenience
	 *
	 * @param        $table
	 * @param array  $columns
	 * @param string $where
	 *
	 * @return $this
	 */
	public function select( $table, $columns = [ '*' ], $where = '' )
	{
		if ( func_num_args() === 2 && is_integer( func_get_arg( 1 ) ) )
		{
			$where   = func_get_arg( 1 );
			$columns = [ '*' ];
		}
		
		$columns = implode( ',', $this->escapeColumnNames( $columns ) );
		
		if ( is_integer( $where ) )
		{
			$WHERE = "WHERE `id`={$where}";
		}
		elseif ( is_array( $where ) )
		{
			$WHERE = $this->getWhereClause( $where );
		}
		else
		{
			$WHERE = $where;
		}
		
		$this->query( "SELECT {$columns} FROM `{$table}` $WHERE" );
		
		return $this;
	}
	
	/**
	 * Insert a single row by passing the table,
	 * an associative array of fields => values to insert,
	 * and [optional] a boolean whether or not to update on duplicate with the same data set
	 * Returns the insert id or false
	 *
	 * @param string $table
	 * @param array  $insertPairs
	 * @param bool   $update
	 *
	 * @return bool|integer
	 */
	public function insert( $table, array $insertPairs, $update = FALSE )
	{
		if ( empty($insertPairs) )
		{
			return FALSE;
		}
		
		$escapedInserts = $this->buildInserts( $insertPairs );
		
		if ( $update )
		{
			$updateStatement = $this->buildUpdate( $insertPairs );
			$this->query( "INSERT INTO `{$table}` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']}) ON DUPLICATE KEY UPDATE {$updateStatement};" );
		}
		else
		{
			$this->query( "INSERT INTO `{$table}` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']});" );
		}
		
		if ( $this->getResult() === TRUE )
		{
			$this->pushId( $this->insertId() );
			
			return $this->insertId();
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Insert a single row by passing the table,
	 * an associative array of fields => values to insert,
	 * and an associative array of fields => values to update on duplicate
	 * Returns the insert id or false
	 *
	 * @param string $table
	 * @param array  $insertPairs
	 * @param array  $updatePairs
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @return bool|integer
	 */
	public function insertOnUpdate( $table, array $insertPairs, array $updatePairs )
	{
		if ( empty($insertPairs) || empty($updatePairs) )
		{
			throw new \InvalidArgumentException( "Insert data cannot be empty." );
		}
		
		$escapedInserts  = $this->buildInserts( $insertPairs );
		$updateStatement = $this->buildUpdate( $updatePairs );
		
		$this->query( "INSERT INTO `{$table}` ({$escapedInserts['keys']}) VALUES ({$escapedInserts['values']}) ON DUPLICATE KEY UPDATE {$updateStatement};" );
		
		if ( $this->getResult() === TRUE )
		{
			$this->pushId( $this->insertId() );
			
			return $this->insertId();
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Update records by passing the table,
	 * an associative array of fields => values to update,
	 * and [optional] a where clause
	 * Returns the number of rows affected by the query
	 *
	 * Where clauses can be:
	 * - a string (the raw statement: "WHERE `field`=value"; You must escape values)
	 * - an array: [ 'key' => value ] @see http://medoo.in/api/where since their api is similar
	 * - an integer: this will yield a "WHERE `id`=$intValue" where clause for convenience
	 *
	 * @param        $table
	 * @param        $data
	 * @param string $where
	 *
	 * @return int
	 */
	public function update( $table, $data, $where = '' )
	{
		$escapedData = $this->buildUpdate( $data );
		
		if ( is_integer( $where ) )
		{
			$WHERE = "WHERE `id`={$where}";
		}
		elseif ( is_array( $where ) )
		{
			$WHERE = $this->getWhereClause( $where );
		}
		else
		{
			$WHERE = $where;
		}
		
		$this->query( "UPDATE `{$table}` SET {$escapedData} {$WHERE}" );
		
		return $this->affectedRows();
	}
	
	/**
	 * Delete records by passing the table
	 * and [optional] a where clause
	 * Returns the number of rows affected by the query
	 *
	 * Where clauses can be:
	 * - a string (the raw statement: "WHERE `field`=value"; You must escape values)
	 * - an array: [ 'key' => value ] @see http://medoo.in/api/where since their api is similar
	 * - an integer: this will yield a "WHERE `id`=$intValue" where clause for convenience
	 *
	 * @param        $table
	 * @param string $where
	 *
	 * @return int|null
	 */
	public function delete( $table, $where = '' )
	{
		if ( is_integer( $where ) )
		{
			$WHERE = "WHERE `id`={$where}";
		}
		elseif ( is_array( $where ) )
		{
			$WHERE = $this->getWhereClause( $where );
		}
		else
		{
			$WHERE = $where;
		}
		
		$this->query( "DELETE FROM `{$table}` {$WHERE}" );
		
		return $this->affectedRows();
	}
	
	/**
	 * Create a table
	 *
	 * @param          $tableName
	 * @param \Closure $schemaFunction
	 * @param bool     $tableShouldBeUnique
	 *
	 * @return \mysqli_result|null
	 */
	public function createTable( $tableName, \Closure $schemaFunction, $tableShouldBeUnique = FALSE )
	{
		if ( $tableShouldBeUnique )
		{
			$tableName = uniqid( $tableName );
		}
		
		$tableBuilder = new TableBuilder( $tableName );
		
		$schemaFunction( $tableBuilder );
		
		$createStatement = $tableBuilder->render();
		
		$this->query( $createStatement );
		
		$this->createdTables[] = $tableBuilder->getTableName();
		
		return $this->getResult();
	}
	
	/**
	 * Drop a table
	 *
	 * @param $table
	 *
	 * @return \mysqli_result|null
	 */
	public function dropTable( $table )
	{
		$statement = "DROP TABLE {$table}";
		$this->query( $statement );
		
		return $this->getResult();
	}
	
	/**
	 * @param string $query
	 */
	protected function setQuery( $query )
	{
		$this->_query  = $query;
		$this->_logs[] = $query;
	}
	
	/**
	 * Returns the last query executed
	 *
	 * @return null|string
	 */
	public function getLastQuery()
	{
		if ( isset($this->_query) )
		{
			return $this->_query;
		}
		
		return NULL;
	}
	
	/**
	 * Takes an iterator (closure) to process each row of returned data.
	 *
	 * @param callable $cb
	 *
	 * @return MySQL $this
	 */
	public function iterateResult( callable $cb )
	{
		$args = array_slice( func_get_args(), 1 );
		
		if ( is_callable( $cb ) )
		{
			while ( $record = $this->queryResult->fetch_assoc() )
			{
				$params = array_merge( [ $record ], $args );
				
				call_user_func_array( $cb, $params );
			}
		}
		
		return $this;
	}
	
	/**
	 * Applies a function against an accumulator ($carry) and each row of the last returned mysqli result object to
	 * reduce it to a single value.
	 *
	 * @param \Closure $cb
	 * @param string   $carry
	 *
	 * @return mixed|string
	 */
	public function reduceResult( \Closure $cb, $carry = '' )
	{
		while ( $record = $this->queryResult->fetch_assoc() )
		{
			$carry = $cb( $carry, $record );
		}
		
		return $carry;
	}
	
	/**
	 * Creates a new array with the results of calling the provided function on each row of the last returned mysqli
	 * result object.
	 *
	 * @param \Closure $cb
	 *
	 * @return array
	 */
	public function mapResult( \Closure $cb )
	{
		$newArray = [];
		
		while ( $record = $this->queryResult->fetch_assoc() )
		{
			$newArray[] = $cb( $record );
		}
		
		return $newArray;
	}
	
	/**
	 * Returns the last query's result object
	 *
	 * @return null|\mysqli_result
	 */
	public function getResult()
	{
		if ( isset($this->queryResult) )
		{
			return $this->queryResult;
		}
		
		return NULL;
	}
	
	/**
	 * Get the last error from mysqli
	 *
	 * @return string
	 */
	public function getError()
	{
		return $this->db->error;
	}
	
	/**
	 * Returns the number of rows from the last query
	 * (must be done before iterating over the result)
	 *
	 * @return null|integer
	 */
	public function numRows()
	{
		if ( isset($this->queryResult) )
		{
			return $this->queryResult->num_rows;
		}
		
		return NULL;
	}
	
	/**
	 * Returns the number of rows affected by the last query
	 * (inserts, updates, deletes)
	 *
	 * @return null|integer
	 */
	public function affectedRows()
	{
		return $this->db->affected_rows;
	}
	
	/**
	 * Returns the last inserted id from mysqli
	 *
	 * @return null|integer
	 */
	public function insertId()
	{
		$id = $this->db->insert_id;
		
		if ( $id > 0 )
		{
			return $id;
		}
		
		return NULL;
	}
	
	/**
	 * Returns the last inserted id
	 * Alias of MySQL::insertId
	 *
	 * @see MySQL::insertId()
	 *
	 * @return int|null
	 */
	public function lastId()
	{
		return $this->insertId();
	}
	
	/**
	 * Returns the last inserted id
	 * Alias of MySQL::insertId
	 *
	 * @see MySQL::insertId()
	 *
	 * @return int|null
	 */
	public function id()
	{
		return $this->insertId();
	}
	
	/**
	 * Inserts performed using the insert methods
	 * save the inserted ids to an array.
	 *
	 * @return array
	 */
	public function getInsertedIds()
	{
		return $this->_ids;
	}
	
	/**
	 * ALias for getInsertedIds
	 *
	 * @see MySQL::getInsertedIds()
	 *
	 * @return array
	 */
	public function getIds()
	{
		return $this->getInsertedIds();
	}
	
	/**
	 * @param $id
	 *
	 * @return int
	 */
	private function pushId( $id )
	{
		if ( $id > 0 )
		{
			$this->_ids[] = $id;
		}
		
		return count( $this->_ids );
	}
	
	/**
	 * Gets an array of all the queries performed by the instance
	 *
	 * @return array
	 */
	public function getLogs()
	{
		return $this->_logs;
	}
	
	/**
	 * @param $whereData
	 *
	 * @return Where
	 */
	protected function getWhereClause( $whereData )
	{
		$where = new Where( $this->db );
		$where->parseClause( $whereData );
		
		return $where;
	}
	
	/**
	 * Get an array of all the columns in a given table [, and a given database ]
	 *
	 * @param string $table
	 * @param string $database
	 *
	 * @return array
	 */
	public function getColumns( $table, $database = '' )
	{
		if ( empty($database) )
		{
			$database = $this->_database;
		}
		
		$this->query(
			"SELECT column_name FROM information_schema.columns WHERE table_name = '$table' AND table_schema = '$database'"
		);
		
		$this->iterateResult(
			function ( $row ) use ( $table )
			{
				foreach ( $row as $index => $columnName )
				{
					$this->columns[ $table ][] = $columnName;
				}
			}
		);
		
		return $this->columns[ $table ];
	}
	
	/**
	 * Escapes an associative array of [ keys => values ]
	 * and returns an array with the escaped data
	 * at $returnArray['keys'] & $returnArray['values']
	 *
	 * @param array $insertList
	 *
	 * @return array
	 */
	public function buildInserts( array $insertList )
	{
		$escapedKeyValuePairs = $this->escapeKeyValuePairs( $insertList );
		
		return [
			'keys'   => implode( ',', $escapedKeyValuePairs['keys'] ),
			'values' => implode( ',', $escapedKeyValuePairs['values'] ),
		];
	}
	
	/**
	 * Escapes an associative array of [ keys => values ]
	 * and returns an Update Statement string with the escaped data
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function buildUpdate( array $data )
	{
		$updateList           = [];
		$escapedKeyValuePairs = $this->escapeKeyValuePairs( $data );
		$length               = count( $escapedKeyValuePairs['keys'] );
		
		for ( $i = 0; $i < $length; $i++ )
		{
			$updateList[] = "{$escapedKeyValuePairs['keys'][$i]}={$escapedKeyValuePairs['values'][$i]}";
		}
		
		return implode( ', ', $updateList );
	}
	
	/**
	 * Escapes a value
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function escape( $string )
	{
		settype( $string, 'string' );
		
		return $this->db->real_escape_string( $string );
	}
	
	/**
	 * Wraps a column name in backticks
	 *
	 * @param $columnName
	 *
	 * @return string
	 */
	public function escapeColumnName( $columnName )
	{
		if ( $columnName === '*' )
		{
			return $columnName;
		}
		
		return '`' . $this->escape( $columnName ) . '`';
	}
	
	/**
	 * Wraps an array of columns in backticks
	 * and returns the escaped array
	 *
	 * @param array $colList
	 *
	 * @return array
	 */
	public function escapeColumnNames( array $colList )
	{
		$escapedList = [];
		foreach ( $colList as $col )
		{
			$escapedList[] = $this->escapeColumnName( $col );
		}
		
		return $escapedList;
	}
	
	/**
	 * @param array $assoc_array
	 *
	 * @return array
	 */
	public function escapeKeyValuePairs( array $assoc_array )
	{
		$keys   = [];
		$values = [];
		
		foreach ( $assoc_array as $key => $val )
		{
			switch ( gettype( $val ) )
			{
				case 'object': # handle objects
				case 'array': # and arrays the same
					$safeKey = $this->escape( trim( $key ) );
					$keys[]  = ("`$safeKey`");
					
					$jsonStrValue = json_encode( $val );
					$safeValue    = $this->escape( $jsonStrValue );
					$values[]     = "'$safeValue'";
					break;
				case 'string': # escape key & value and wrap in quotes
					$safeKey = $this->escape( trim( $key ) );
					$keys[]  = ("`$safeKey`");
					
					$safeValue = $this->escape( $val );
					$values[]  = "'$safeValue'";
					break;
				case 'boolean': # booleans
					$safeKey = $this->escape( trim( $key ) );
					$keys[]  = "`$safeKey`";
					
					$values[] = $val ? 1 : 0;
					break;
				case 'NULL': # NULL
				case 'double': # doubles
				case 'integer': # & integers don't need escaping or quotations
					$safeKey = $this->escape( trim( $key ) );
					$keys[]  = "`$safeKey`";
					
					$values[] = $val;
					break;
				default: # if the value doesn't match these types,
					break; # skip inclusion
			}
		}
		
		return [
			'keys'   => $keys,
			'values' => $values,
		];
	}
	
	/**
	 * Checks if an array is associative by asserting against numeric indices
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public function isAssoc( array $array )
	{
		return array_keys( $array ) !== range( 0, count( $array ) - 1 );
	}
	
	/**
	 * Returns an array of any tables created by this instance
	 *
	 * @return array
	 */
	public function getCreatedTables()
	{
		return $this->createdTables;
	}
	
	
	/**
	 * Returns the [string] name of the last created table
	 *
	 * @return string|null
	 */
	public function getLastCreatedTable()
	{
		return $this->createdTables[ (count( $this->createdTables ) - 1) ];
	}
	
	/**
	 * Drop all tables created by this instance.
	 *
	 * @throws BadQuery if drop table is unsuccessful
	 */
	public function dropAllCreatedTables()
	{
		array_map(
			function ( $tableName )
			{
				if ( ! $this->dropTable( $tableName ) )
				{
					throw new BadQuery( $this->getLastQuery(), $this->getError(), $this->getLogs() );
				}
			}
			, $this->getCreatedTables()
		);
	}
	
}
