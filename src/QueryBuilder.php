<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 4/1/17
 * Time: 10:32 AM
 */

namespace Database;

use Database\Exceptions\InvalidWhereOperator;

class QueryBuilder {
	
	const SELECT = 'SELECT';
	const INSERT = 'INSERT';
	const UPDATE = 'UPDATE';
	const DELETE = 'DELETE';
	const CREATE = 'CREATE';
	const DROP   = 'DROP';
	
	const METHODS = [ self::SELECT, self::INSERT, self::UPDATE, self::DELETE ];
	
	const IS_NULL  = 'IS NULL';
	const NOT_NULL = 'NOT NULL';
	
	const WHERE_CONJUNCTIONS = [ 'AND', 'OR' ];
	const WHERE_OPS          = [ '=', '<>', '!=', '>', '<', '>=', '<=', 'LIKE', 'IN', self::IS_NULL, self::NOT_NULL, ];
	const WHERE_OPS_SPECIAL  = [ self::IS_NULL, self::NOT_NULL ];
	
	/**
	 * @var array
	 */
	protected $selectFields;
	/**
	 * @var array
	 */
	protected $dmlFields;
	/**
	 * @var array
	 */
	protected $allowedFields;
	/**
	 * @var array
	 */
	protected $whereClauses = [];
	/**
	 * @var array
	 */
	protected $boundParams = [];
	/**
	 * @var string
	 */
	protected $table;
	/**
	 * @var string
	 */
	protected $method;
	/**
	 * @var MySQL
	 */
	protected $mySQL;
	
	public function __construct( MySQL $mySQL, $allowedFields = [] )
	{
		$this->mySQL         = $mySQL;
		$this->allowedFields = $allowedFields;
	}
	
	/**
	 * @param $method
	 *
	 * @return $this
	 */
	public function setMethod( $method )
	{
		$method = strtoupper( $method );
		
		if ( $this->isValidMethod( $method ) )
		{
			$this->method = $method;
		}
		
		return $this;
	}
	
	/**
	 * @param array $fields
	 *
	 * @return $this
	 */
	public function setSelectFields( array $fields = [] )
	{
		if ( empty( $fields ) ) $fields[] = '*';
		else $fields = $this->filterFields( $fields );
		
		$this->selectFields = $fields;
		
		return $this;
	}
	
	/**
	 * @param $table
	 *
	 * @return $this
	 */
	public function from( $table )
	{
		return $this->table( $table );
	}
	
	/**
	 * @param $table
	 *
	 * @return $this
	 */
	public function into( $table )
	{
		return $this->table( $table );
	}
	
	/**
	 * @param $table
	 *
	 * @return $this
	 */
	public function table( $table )
	{
		$this->table = $this->escapeTable( $table );
		
		return $this;
	}
	
	/**
	 * @param        $key
	 * @param        $value
	 * @param string $operator
	 * @param null   $conjunction
	 *
	 * @return static
	 */
	public function where( $key, $value, $operator = '=', $conjunction = NULL )
	{
		if ( $this->isValidField( $key ) )
		{
			$args     = func_get_args();
			$operator = strtoupper( $operator );
			if ( count( $this->whereClauses ) && is_null( $conjunction ) ) $conjunction = self::WHERE_CONJUNCTIONS[0];
			
			if ( count( $args ) == 2 && in_array( $args[1], self::WHERE_OPS_SPECIAL ) )
			{
				$operator             = $value;
				$this->whereClauses[] = [ [ $key => NULL ], $operator, $conjunction ];
			}
			else
			{
				if ( $this->isValidWhereOperator( $operator ) )
				{
					$this->boundParams[ $key ] = $value;
					$this->whereClauses[]      = [ [ $key => $value ], $operator, $conjunction ];
				}
			}
		}
		
		return $this;
	}
	
	/**
	 * @param        $key
	 * @param        $value
	 * @param string $operator
	 *
	 * @return static
	 */
	public function andWhere( $key, $value, $operator = '=' )
	{
		return $this->where( $key, $value, $operator, self::WHERE_CONJUNCTIONS[0] );
	}
	
	/**
	 * @param        $key
	 * @param        $value
	 * @param string $operator
	 *
	 * @return static
	 */
	public function orWhere( $key, $value, $operator = '=' )
	{
		return $this->where( $key, $value, $operator, self::WHERE_CONJUNCTIONS[1] );
	}
	
	/**
	 * @param MySQL $mySQL
	 * @param array $fields
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public static function select( MySQL $mySQL, array $fields = [], array $allowedFields = [] )
	{
		$builder = new self( $mySQL, $allowedFields );
		
		$builder->setMethod( self::SELECT )
		        ->setSelectFields( $fields );
		
		return $builder;
	}
	
	/**
	 * @param MySQL $mySQL
	 * @param array $data
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public static function insert( MySQL $mySQL, array $data = [], array $allowedFields = [] )
	{
		$builder = new self( $mySQL, $allowedFields );
		
		$builder->setMethod( self::INSERT )
		        ->setData( $data );
		
		return $builder;
	}
	
	/**
	 * @param MySQL $mySQL
	 * @param array $data
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public static function update( MySQL $mySQL, array $data = [], array $allowedFields = [] )
	{
		$builder = new self( $mySQL, $allowedFields );
		
		$builder->setMethod( self::UPDATE )
		        ->setData( $data );
		
		return $builder;
	}
	
	/**
	 * @param MySQL $mySQL
	 * @param array $allowedFields
	 *
	 * @return QueryBuilder
	 */
	public static function delete( MySQL $mySQL, array $allowedFields = [] )
	{
		$builder = new self( $mySQL, $allowedFields );
		
		$builder->setMethod( self::DELETE );
		
		return $builder;
	}
	
	public function limit( $limit )
	{
	
	}
	
	public function offset( $offset )
	{
	
	}
	
	public function groupBy( $groupBy )
	{
	
	}
	
	public function orderBy( $orderBy )
	{
	
	}
	
	/**
	 * @return string
	 */
	public function renderQuery()
	{
		switch ( $this->method )
		{
			case self::SELECT:
				return $this->renderSelect();
			case self::INSERT:
				return $this->renderInsert();
			case self::UPDATE:
				return $this->renderUpdate();
			case self::DELETE:
				return $this->renderDelete();
			default:
				throw new \InvalidArgumentException( 'Missing database method!' );
		}
	}
	
	/**
	 * @return string
	 */
	protected function renderSelect()
	{
		$baseStmt = [
			$this->method,
			implode( ',', $this->selectFields ),
			'FROM',
			$this->table,
		];
		
		if ( count( $this->whereClauses ) )
		{
			$baseStmt[] = "WHERE";
			$baseStmt[] = $this->renderWhereClause();
		}
		
		return implode( ' ', $baseStmt );
	}
	
	/**
	 * @return string
	 */
	public function renderInsert()
	{
		$baseStmt = [
			$this->method,
			'INTO',
			$this->table,
			sprintf( '(%s)', implode( ', ', $this->dmlFields ) ),
			'VALUES',
			sprintf( '(%s)', implode( ', ', $this->mySQL->getNamedParams( $this->dmlFields ) ) ),
		];
		
		if ( count( $this->whereClauses ) )
		{
			$baseStmt[] = "WHERE";
			$baseStmt[] = $this->renderWhereClause();
		}
		
		return implode( ' ', $baseStmt );
	}
	
	/**
	 * @return string
	 */
	public function renderUpdate()
	{
		$baseStmt = [
			$this->method,
			$this->table,
			'SET',
			$this->renderUpdatePairs( $this->dmlFields ),
		];
		
		if ( count( $this->whereClauses ) )
		{
			$baseStmt[] = "WHERE";
			$baseStmt[] = $this->renderWhereClause();
		}
		
		return implode( ' ', $baseStmt );
	}
	
	/**
	 * @return string
	 */
	public function renderDelete()
	{
		$baseStmt = [
			$this->method,
			'FROM',
			$this->table,
		];
		
		if ( count( $this->whereClauses ) )
		{
			$baseStmt[] = "WHERE";
			$baseStmt[] = $this->renderWhereClause();
		}
		
		return implode( ' ', $baseStmt );
	}
	
	/**
	 * @return string
	 */
	protected function renderWhereClause()
	{
		$clause = [];
		
		foreach ( $this->whereClauses as $whereClause )
		{
			// FORMAT: [ [ $key => $value ], $operator, NULL ]
			$base = [];
			list( $kvPair, $operator, $conjunction ) = $whereClause;
			list( $placeholder ) = $this->mySQL->getNamedParamsFromAssoc( $kvPair, $this->allowedFields );
			$key   = array_keys( $kvPair )[0];
			$value = $kvPair[ $key ];
			
			if ( $conjunction ) $base[] = $conjunction;
			
			if ( is_null( $value ) && in_array( $operator, self::WHERE_OPS_SPECIAL ) )
			{
				$base[] = $key;
				$base[] = $operator;
			}
			elseif ( $operator === 'IN' && is_array( $value ) )
			{
				$base[] = $key;
				$base[] = $operator;
				unset( $this->boundParams[ $key ] );
				$placeholders = [];
				
				foreach ( $value as $i => $item )
				{
					$tmpKey         = sprintf( '%s%d', $key, $i );
					$placeholders[] = sprintf( ':%s', $tmpKey );
					
					$this->boundParams[ $tmpKey ] = $item;
				}
				
				$base[] = sprintf( '(%s)', implode( ', ', $placeholders ) );
			}
			else
			{
				$base[] = $key;
				$base[] = $operator;
				$base[] = $placeholder;
			}
			
			$clause[] = implode( ' ', $base );
		}
		
		return implode( ' ', $clause );
	}
	
	/**
	 * @param array $dmlFields
	 *
	 * @return string
	 */
	protected function renderUpdatePairs( array $dmlFields )
	{
		$updatePairs = array_combine( $dmlFields, $this->mySQL->getNamedParams( $dmlFields ) );
		$updateStmt  = [];
		
		foreach ( $updatePairs as $key => $placeholder )
		{
			$updateStmt[] = sprintf( '%s = %s', $key, $placeholder );
		}
		
		return implode( ', ', $updateStmt );
	}
	
	/**
	 * @param $method
	 *
	 * @return bool
	 */
	protected function isValidMethod( $method )
	{
		return in_array( $method, self::METHODS );
	}
	
	/**
	 * @param $field
	 *
	 * @return bool
	 */
	protected function isValidField( $field )
	{
		if ( empty( $this->allowedFields ) )
		{
			return TRUE;
		}
		else
		{
			return in_array( $field, $this->allowedFields );
		}
	}
	
	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	protected function filterFields( array $fields )
	{
		if ( empty( $this->allowedFields ) ) return $fields;
		
		return array_filter( $fields, function ( $field )
		{
			return in_array( $field, $this->allowedFields );
		} );
	}
	
	/**
	 * @param array $data
	 *
	 * @return array
	 */
	protected function filterAllowedData( array $data )
	{
		if ( empty( $this->allowedFields ) ) return $data;
		
		$filtered = [];
		
		foreach ( $data as $key => $value )
		{
			if ( in_array( $key, $this->allowedFields ) )
			{
				$filtered[ $key ] = $value;
			}
		}
		
		return $filtered;
	}
	
	/**
	 * @param $op
	 *
	 * @return bool
	 * @throws InvalidWhereOperator
	 */
	protected function isValidWhereOperator( $op )
	{
		if ( ! in_array( $op, self::WHERE_OPS ) )
		{
			throw new InvalidWhereOperator(
				sprintf( 'The operator `%s` is not a valid WHERE operator.', $op )
			);
		}
		
		return TRUE;
	}
	
	/**
	 * @param $table
	 *
	 * @return string
	 */
	protected function escapeTable( $table )
	{
		return sprintf(
			'`%s`',
			str_replace( '`', '', $table )
		);
	}
	
	/**
	 * @return array
	 */
	public function getBoundParams()
	{
		return $this->boundParams;
	}
	
	/**
	 * @return MySQL
	 */
	public function execute()
	{
		return $this->mySQL->query( $this );
	}
	
	/**
	 * @param array $data
	 *
	 * @return $this
	 */
	public function setData( array $data )
	{
		$allowedData     = $this->filterAllowedData( $data );
		$this->dmlFields = array_keys( $allowedData );
		
		$this->boundParams = array_merge( $this->boundParams, $allowedData );
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->renderQuery();
	}
}