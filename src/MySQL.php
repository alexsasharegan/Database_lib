<?php

namespace Database;

use Database\Exceptions\BadQuery;
use Database\Utils\Timer;
use PDO;

/**
 * Class MySQL
 * @package Database
 */
class MySQL {
	
	const TIME_FORMAT = 'Y-m-d H:i:s';
	
	const DSN_FORMAT = 'mysql:host=%s;dbname=%s;port=%s;charset=%s';
	/**
	 * @var array
	 */
	protected $conDefaults = [
		'DB_HOST'     => '127.0.0.1',
		'DB_NAME'     => 'test',
		'DB_PORT'     => '3306',
		'DB_CHARSET'  => 'utf8',
		'DB_USERNAME' => 'admin',
		'DB_PASSWORD' => 'admin',
	];
	/**
	 * @var array
	 */
	protected $con = [];
	/**
	 * @var array
	 */
	protected $optDefaults = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => FALSE,
	];
	/**
	 * @var array
	 */
	protected $opt = [];
	/**
	 * @var PDO
	 */
	protected $pdo;
	/**
	 * @var \PDOStatement
	 */
	protected $stmt;
	/**
	 * @var array
	 */
	protected $columns = [];
	/**
	 * @var string
	 */
	protected $query = '';
	/**
	 * @var array
	 */
	protected $logs = [];
	/**
	 * @var Timer
	 */
	protected $timer = NULL;
	/**
	 * @var array
	 */
	protected $createdTables = [];
	
	/**
	 * @param $SQLDate
	 *
	 * @return null|string
	 */
	public static function SQLDateToPath( $SQLDate )
	{
		if ( empty( $SQLDate ) ) return NULL;
		
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
	 * @return false|string
	 */
	public static function now()
	{
		return date( self::TIME_FORMAT );
	}
	
	/**
	 * MySQL constructor.
	 *
	 * @param array $connectionParams
	 * @param array $options
	 */
	public function __construct( array $connectionParams = [], array $options = [] )
	{
		$this->con = array_merge( $this->conDefaults, $connectionParams );
		$this->opt = array_merge( $this->optDefaults, $options );
		
		$dsn = sprintf(
			self::DSN_FORMAT,
			$this->con['DB_HOST'], $this->con['DB_NAME'], $this->con['DB_PORT'], $this->con['DB_CHARSET']
		);
		
		$this->pdo = new PDO( $dsn, $this->con['DB_USERNAME'], $this->con['DB_PASSWORD'], $this->opt );
	}
	
	public function close()
	{
		$this->__destruct();
		
		return $this;
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
		$this->pdo = NULL;
		
		$dsn = sprintf(
			self::DSN_FORMAT,
			$this->con['DB_HOST'], $database, $this->con['DB_PORT'], $this->con['DB_CHARSET']
		);
		
		$this->pdo = new PDO( $dsn, $this->con['DB_USERNAME'], $this->con['DB_PASSWORD'], $this->opt );
		
		return $this;
	}
	
	/**
	 * Automatically closes the connection to mysql
	 */
	public function __destruct()
	{
		$this->pdo = NULL;
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
	 */
	public function query( $query )
	{
		if ( $query instanceof QueryBuilder )
		{
			$rawQuery = $query->renderQuery();
			$params   = $query->getBoundParams();
			
			$this->setQuery( $rawQuery )
			     ->addQueryToLog( $rawQuery, $params )
			     ->startTimer();
			
			$this->prepare( $rawQuery );
			$this->execute( $params );
		}
		else
		{
			$this->setQuery( $query )
			     ->addQueryToLog( $query )
			     ->startTimer();
			
			$this->stmt = $this->pdo->query( $this->query );
		}
		
		$this->setTimeOnLastLog( $this->stopTimer() );
		
		return $this;
	}
	
	public function prepare( $statement )
	{
		$this->stmt = $this->pdo->prepare( $statement );
		
		return $this;
	}
	
	public function execute( array $inputParams )
	{
		$this->stmt->execute( $inputParams );
		
		return $this;
	}
	
	public function getStatement()
	{
		return $this->stmt;
	}
	
	/**
	 * @param int $fetchStyle
	 *
	 * @return array
	 */
	public function fetchAll( $fetchStyle = PDO::FETCH_ASSOC )
	{
		return $this->stmt->fetchAll( $fetchStyle );
	}
	
	/**
	 * @param     $fn
	 * @param int $fetchStyle
	 *
	 * @return $this
	 */
	public function each( $fn, $fetchStyle = PDO::FETCH_ASSOC )
	{
		while ( $row = $this->stmt->fetch( $fetchStyle ) )
		{
			call_user_func( $fn, $row );
		}
		
		return $this;
	}
	
	/**
	 * @param null $mapFn
	 * @param int  $fetchStyle
	 *
	 * @return array
	 */
	public function map( $mapFn = NULL, $fetchStyle = PDO::FETCH_ASSOC )
	{
		$mapped = [];
		
		if ( is_null( $mapFn ) ) $mapFn = [ $this, 'identity' ];
		
		while ( $row = $this->stmt->fetch( $fetchStyle ) )
		{
			$mapped[] = call_user_func( $mapFn, $row );
		}
		
		return $mapped;
	}
	
	/**
	 * @param      $reduceFn
	 * @param null $initial
	 * @param int  $fetchStyle
	 *
	 * @return mixed|null
	 */
	public function reduce( $reduceFn, $initial = NULL, $fetchStyle = PDO::FETCH_ASSOC )
	{
		$carry = $initial;
		
		while ( $row = $this->stmt->fetch( $fetchStyle ) )
		{
			$carry = call_user_func( $reduceFn, $carry, $row );
		}
		
		return $carry;
	}
	
	/**
	 * @param     $filterFn
	 * @param int $fetchStyle
	 *
	 * @return array
	 */
	public function filter( $filterFn, $fetchStyle = PDO::FETCH_ASSOC )
	{
		$filtered = [];
		
		while ( $row = $this->stmt->fetch( $fetchStyle ) )
		{
			if ( call_user_func( $filterFn, $row ) ) $filtered[] = $row;
		}
		
		return $filtered;
	}
	
	/**
	 * @param     $rejectFn
	 * @param int $fetchStyle
	 *
	 * @return array
	 */
	public function reject( $rejectFn, $fetchStyle = PDO::FETCH_ASSOC )
	{
		$filtered = [];
		
		while ( $row = $this->stmt->fetch( $fetchStyle ) )
		{
			if ( ! call_user_func( $rejectFn, $row ) ) $filtered[] = $row;
		}
		
		return $filtered;
	}
	
	/**
	 * @param array $params
	 * @param array $allowedParams
	 *
	 * @return array
	 */
	public function getNamedParamsFromAssoc( array $params, array $allowedParams = [] )
	{
		$keys = array_keys( $params );
		
		$mapPlaceholders = function ( $key ) { return sprintf( ":%s", $key ); };
		
		if ( count( $allowedParams ) )
		{
			$keys = array_filter( $keys, function ( $key ) use ( $allowedParams )
			{
				return in_array( $key, $allowedParams );
			} );
		}
		
		return array_map( $mapPlaceholders, $keys );
	}
	
	/**
	 * @param array $params
	 * @param array $allowedParams
	 *
	 * @return array
	 */
	public function getNamedParams( array $params, array $allowedParams = [] )
	{
		$keys = $params;
		
		$mapPlaceholders = function ( $key ) { return sprintf( ":%s", $key ); };
		
		if ( count( $allowedParams ) )
		{
			$keys = array_filter( $keys, function ( $key ) use ( $allowedParams )
			{
				return in_array( $key, $allowedParams );
			} );
		}
		
		return array_map( $mapPlaceholders, $keys );
	}
	
	/**
	 * @param $query
	 *
	 * @return $this
	 */
	protected function setQuery( $query )
	{
		$this->query = $query;
		
		return $this;
	}
	
	/**
	 * Gets an array of all the queries performed by the instance
	 *
	 * @return array
	 */
	public function getLogs()
	{
		return $this->logs;
	}
	
	/**
	 * @param      $log
	 * @param null $boundParams
	 *
	 * @return $this
	 */
	protected function addQueryToLog( $log, $boundParams = NULL )
	{
		$log = [ 'query' => $log, ];
		if ( ! is_null( $boundParams ) ) $log['params'] = $boundParams;
		$this->logs[] = $log;
		
		return $this;
	}
	
	protected function setTimeOnLastLog( $time )
	{
		$lastIndex = count( $this->logs ) - 1;
		
		$this->logs[ $lastIndex ]['time'] = $time;
		
		return $this;
	}
	
	/**
	 * @return $this
	 */
	protected function startTimer()
	{
		$this->timer = new Timer;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	protected function stopTimer()
	{
		return $this->timer->stop();
	}
	
	/**
	 * @param array $fields
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public function select( array $fields = [], array $allowedFields = [] )
	{
		return QueryBuilder::select( $this, $fields, $allowedFields );
	}
	
	/**
	 * @param array $data
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public function insert( array $data = [], array $allowedFields = [] )
	{
		return QueryBuilder::insert( $this, $data, $allowedFields );
	}
	
	/**
	 * @param array $data
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public function update( array $data = [], array $allowedFields = [] )
	{
		return QueryBuilder::update( $this, $data, $allowedFields );
	}
	
	/**
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public function delete( array $allowedFields = [] )
	{
		return QueryBuilder::delete( $this, $allowedFields );
	}
	
	/**
	 * Returns the last inserted id from mysqli
	 *
	 * @return int
	 */
	public function lastInsertId()
	{
		return (int) $this->pdo->lastInsertId();
	}
	
	/**
	 * Insert an array of models (associative arrays) into the given table in one query.
	 * Returns true on success.
	 *
	 * @param       $table
	 * @param array $dataSet
	 *
	 * @return \mysqli_result|null
	 */
	public function bulkInsert( $table, array $dataSet )
	{
		if ( ! $this->isNumericArray( $dataSet ) )
		{
			throw new \InvalidArgumentException( 'Array must be a numerically indexed array of associative arrays.' );
		}
		
		$escapedFields = $this->buildInserts( $dataSet[0] )['keys'];
		
		$escapedDataList = implode( ',',
			array_map(
				function ( $data )
				{
					return '(' . $this->buildInserts( $data )['values'] . ')';
				}
				, $dataSet
			)
		);
		
		$this->query( "INSERT INTO `{$table}` ({$escapedFields}) VALUES {$escapedDataList};" );
		
		return $this->getResult();
	}
	
	/**
	 * Returns the number of rows affected by the last query
	 * (inserts, updates, deletes)
	 *
	 * @return int
	 */
	public function rowCount()
	{
		return $this->stmt->rowCount();
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
	 * Get an array of all the columns in a given table [, and a given database ]
	 * Database defaults to the current connected database.
	 *
	 * @param string $table
	 * @param string $database
	 *
	 * @return array
	 */
	public function getColumns( $table, $database = '' )
	{
		if ( empty( $database ) ) $database = $this->con['DB_NAME'];
		
		$selectColumns = "SELECT column_name FROM information_schema.columns WHERE table_name=? AND table_schema=?";
		
		return $this->prepare( $selectColumns )
		            ->execute( [ $table, $database ] )
		            ->map( function ( array $row ) { return $row['column_name']; }, PDO::FETCH_ASSOC );
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
	 * Returns the last query executed
	 *
	 * @return null|string
	 */
	public function getLastQuery()
	{
		if ( isset( $this->query ) )
		{
			return $this->query;
		}
		
		return NULL;
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
	
	public function noop()
	{
	
	}
	
	public function identity( $x = NULL )
	{
		return $x;
	}
	
}
