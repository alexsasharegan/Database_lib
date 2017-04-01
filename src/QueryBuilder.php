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
	
	const METHODS = [ self::SELECT, self::INSERT, self::UPDATE, self::DELETE ];
	
	const IS_NULL  = 'IS NULL';
	const NOT_NULL = 'NOT NULL';
	
	const WHERE_CONJUNCTIONS = [ 'AND', 'OR' ];
	const WHERE_OPS          = [ '=', '<>', '!=', '>', '<', '>=', '<=', 'LIKE', 'IN', self::IS_NULL, self::NOT_NULL, ];
	const WHERE_OPS_SPECIAL  = [ self::IS_NULL, self::NOT_NULL ];
	
	/**
	 * @var array
	 */
	protected $fields;
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
	public function setFields( array $fields = [] )
	{
		if ( empty( $fields ) )
		{
			$fields[] = '*';
		}
		else
		{
			$fields = $this->filterFields( $fields );
		}
		
		$this->fields = $fields;
		
		return $this;
	}
	
	/**
	 * @param $table
	 *
	 * @return $this
	 */
	public function from( $table )
	{
		$this->table = $table;
		
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
			$args = func_get_args();
			
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
	 * @return static
	 */
	public static function select( MySQL $mySQL, array $fields = [], array $allowedFields = [] )
	{
		$builder = new static( $mySQL, $allowedFields );
		
		$builder->setMethod( self::SELECT )
		        ->setFields( $fields );
		
		return $builder;
	}
	
	/**
	 * @param MySQL $mySQL
	 * @param array $data
	 * @param array $allowedFields
	 *
	 * @return static
	 */
	public static function insert( MySQL $mySQL, array $data = [], array $allowedFields = [] )
	{
		$builder = new static( $mySQL, $allowedFields );
		
		$builder->setMethod( self::INSERT )
		        ->setData( $data );
		
		return $builder;
	}
	
	/**
	 * @param MySQL $mySQL
	 * @param array $data
	 * @param array $allowedFields
	 *
	 * @return static
	 */
	public static function update( MySQL $mySQL, array $data = [], array $allowedFields = [] )
	{
		$builder = new static( $mySQL, $allowedFields );
		
		$builder->setMethod( self::UPDATE )
		        ->setData( $data );
		
		return $builder;
	}
	
	/**
	 * @param MySQL $mySQL
	 * @param array $allowedFields
	 *
	 * @return static
	 */
	public static function delete( MySQL $mySQL, array $allowedFields = [] )
	{
		$builder = new static( $mySQL, $allowedFields );
		
		$builder->setMethod( self::DELETE );
		
		return $builder;
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
			// case self::INSERT:
			// 	return $this->renderInsert();
			// case self::UPDATE:
			// 	return $this->renderUpdate();
			// case self::DELETE:
			// 	return $this->renderDelete();
			default:
				break;
		}
	}
	
	/**
	 * @return string
	 */
	protected function renderSelect()
	{
		$baseStmt = [
			$this->method,
			implode( ',', $this->fields ),
			'FROM',
			sprintf( '`%s`', trim( $this->table, '`' ) ),
		];
		
		if ( count( $this->whereClauses ) )
		{
			$baseStmt[] = "WHERE";
			$baseStmt[] = $this->renderWhereClause();
		}
		
		return implode( ' ', $baseStmt );
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
			
			if ( is_null( $value ) )
			{
				$base[] = $key;
				$base[] = $operator;
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
	
	protected function setData( array $data ) { }
}