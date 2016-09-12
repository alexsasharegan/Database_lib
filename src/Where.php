<?php

namespace Database;

/**
 * Class Where
 * @package Database
 */
class Where {
	
	/**
	 * @var array
	 */
	private $_clauses = [];
	
	/**
	 * @var \mysqli
	 */
	private $_db;
	
	/**
	 * Where constructor.
	 * @param \mysqli $mysqli
	 */
	public function __construct( \mysqli $mysqli ) {
		$this->_db = $mysqli;
	}
	
	/**
	 * @param string $type
	 * @return $this
	 */
	public function addClause( $type = 'AND' ) {
		$clause           = new Where\Clause( $this->_db, $type );
		$this->_clauses[] = $clause;
		
		return $clause;
	}
	
	/**
	 * @param array $whereClauses
	 * @return string
	 */
	public function parseClause( $whereClauses = [] ) {
		if ( empty($whereClauses) ) {
			return strval( $this );
		}
		
		$walkRecursive = function ( $value, $key, $clause = null ) use ( &$walkRecursive ) {
			if ( Where\Clause::isValidType( $key ) && is_array( $value ) && is_null( $clause ) ) {
				$clause = $this->addClause( $key );
				foreach ( $value as $index => $data ) {
					$walkRecursive( $data, $index, $clause );
				}
			} elseif ( !Where\Clause::isValidType( $key ) && !is_array( $value ) && !is_null( $clause ) ) {
				$clause->add( $key, $value );
			} elseif ( !Where\Clause::isValidType( $key ) && !is_array( $value ) && is_null( $clause ) ) {
				$this->addClause()->add( $key, $value );
			}
		};
		
		array_walk( $whereClauses, $walkRecursive );
		
		return strval( $this );
	}
	
	/**
	 * @return string
	 */
	public function __toString() {
		if ( empty($this->_clauses) ) {
			return '';
		}
		
		$list = [];
		foreach ( $this->_clauses as $clause ) {
			$list[] = strval( $clause );
		}
		
		return 'WHERE ' . implode( ' ', $list );
	}
	
}