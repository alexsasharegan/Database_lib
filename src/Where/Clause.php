<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 9/11/16
 * Time: 10:00 AM
 */

namespace Database\Where;


/**
 * Class Clause
 * @package Database\Where
 */
class Clause {
	
	/**
	 * @var \mysqli
	 */
	private $_db;
	
	/**
	 * @var array
	 */
	private $_data = [];
	
	/**
	 * @var string
	 */
	private $_type = 'AND';
	
	/**
	 * @var array
	 */
	private static $_typeList = [ 'AND', 'OR', 'NOT', ];
	
	/**
	 * @var string
	 */
	private $_regex = '/(.*)\[(.*)\]/';
	
	/**
	 * @var string
	 */
	private $_defaultOperator = '=';
	
	/**
	 * @var array
	 */
	private $_operators = [
		'='  => 'equals',
		'!=' => 'not equal',
		'<>' => 'not equal',
		'>'  => 'greater than',
		'!>' => 'not greater than',
		'>=' => 'greater than or equal',
		'<'  => 'less than',
		'!<' => 'not less than',
		'<=' => 'less than or equal',
	];
	
	/**
	 * @var array
	 */
	private $_logicalOperators = [
		// 'ALL'     => 'The ALL operator is used to compare a value to all values in another value set. AND	The AND operator allows the existence of multiple conditions in an SQL statement\'s WHERE clause.',
		// 'ANY'     => 'The ANY operator is used to compare a value to any applicable value in the list according to the condition.',
		// 'BETWEEN' => 'The BETWEEN operator is used to search for values that are within a set of values, given the minimum value and the maximum value.',
		// 'EXISTS'  => 'The EXISTS operator is used to search for the presence of a row in a specified table that meets certain criteria.',
		// 'IN'      => 'The IN operator is used to compare a value to a list of literal values that have been specified.',
		'LIKE' => 'The LIKE operator is used to compare a value to similar values using wildcard operators.',
		'NOT'  => 'The NOT operator reverses the meaning of the logical operator with which it is used. Eg: NOT EXISTS, NOT BETWEEN, NOT IN, etc. This is a negate operator.',
		'OR'   => 'The OR operator is used to combine multiple conditions in an SQL statement\'s WHERE clause.',
		// 'IS NULL' => 'The NULL operator is used to compare a value with a NULL value.',
		// 'UNIQUE'  => 'The UNIQUE operator searches every row of a specified table for uniqueness (no duplicates).',
	];
	
	/**
	 * @param $type
	 *
	 * @return bool
	 */
	public static function isValidType( $type )
	{
		return in_array( strtoupper( trim( $type ) ), self::$_typeList );
	}
	
	/**
	 * Clause constructor.
	 *
	 * @param \mysqli $mysqli
	 * @param string  $type
	 */
	public function __construct( \mysqli $mysqli, $type = 'AND' )
	{
		$this->_db = $mysqli;
		$type      = strtoupper( trim( $type ) );
		if ( in_array( $type, self::$_typeList ) )
		{
			$this->_type = $type;
		}
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$returnList = [];
		
		switch ( $this->_type )
		{
			case 'NOT':
				foreach ( $this->_data as $data )
				{
					$returnList[] = "`{$data['field']}`{$data['operator']}{$data['value']}";
				}
				
				return 'NOT (' . implode( ' OR ', $returnList ) . ')';
			default:
				foreach ( $this->_data as $data )
				{
					$returnList[] = "(`{$data['field']}`{$data['operator']}{$data['value']})";
				}
				
				return implode( " {$this->_type} ", $returnList );
		}
	}
	
	/**
	 * @param $field
	 * @param $value
	 *
	 * @return $this
	 */
	public function add( $field, $value )
	{
		$parsedField   = $this->parseField( $field );
		$this->_data[] = array_merge( $parsedField, [ 'value' => $this->escapeValue( $value ), ] );
		
		return $this;
	}
	
	/**
	 * @param array $dataList
	 *
	 * @return $this
	 */
	public function add_array( array $dataList )
	{
		foreach ( $dataList as $field => $value )
		{
			$this->add( $field, $value );
		}
		
		return $this;
	}
	
	/**
	 * @param $string
	 *
	 * @return array
	 * @throws \Exception
	 */
	private function parseField( $string )
	{
		$matches = [];
		if ( ! preg_match( $this->_regex, $string, $matches ) )
		{
			return [ 'field' => trim( $string ), 'operator' => $this->_defaultOperator, ];
		}
		
		$field    = trim( $matches[1] );
		$operator = trim( $matches[2] );
		
		if ( array_key_exists( $operator, $this->_operators )
			|| array_key_exists( strtoupper( $operator ), $this->_logicalOperators )
		)
		{
			return [ 'field' => $field, 'operator' => $operator, ];
		}
		
		throw new \Exception( "Invalid operator: $operator." );
	}
	
	/**
	 * @param $value
	 *
	 * @return string
	 * @throws \Exception
	 */
	private function escapeValue( $value )
	{
		$type = gettype( $value );
		switch ( $type )
		{
			case 'object':
			case 'array':
				$jsonStrValue = json_encode( $value );
				$safeValue    = $this->_db->real_escape_string( $jsonStrValue );
				
				return "'$safeValue'";
			case 'string':
				$safeValue = $this->_db->real_escape_string( $value );
				
				return "'$safeValue'";
			case 'boolean':
			case 'double':
			case 'integer':
				return $value;
			default:
				throw new \Exception( "Unsupported type: {$type}" );
		}
	}
	
}