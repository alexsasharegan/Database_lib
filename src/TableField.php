<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 9/22/16
 * Time: 6:07 AM
 */

namespace Database;

class TableField {
	
	private $integer = 11;
	
	private $collation = 'utf8_unicode_ci';
	
	private $fieldName;
	
	private $type;
	
	private $modifiers = [];
	
	private $_hasForeignKey = FALSE;
	
	private $foreignKeyTable;
	
	private $foreignKeyField = 'id';
	
	/**
	 * @return string
	 */
	public function getForeignKeyTable()
	{
		return $this->foreignKeyTable;
	}
	
	/**
	 * @return string
	 */
	public function getForeignKeyField()
	{
		return $this->foreignKeyField;
	}
	
	public function __construct( $fieldName )
	{
		$this->fieldName = $fieldName;
	}
	
	public function isType( $type, $modifier = NULL )
	{
		switch ( strtolower( $type ) )
		{
			case 'integer':
			case 'int':
				if ( ! $modifier )
				{
					$modifier = $this->integer;
				}
				
				$this->modifiers[] = "int({$modifier})";;
				break;
			case stristr( strtolower( $type ), 'text' ) . stristr( strtolower( $type ), 'text', TRUE ):
				if ( ! $modifier )
				{
					$modifier = "COLLATE " . $this->collation;
				}
				
				$this->modifiers[] = "{$type} {$modifier}";
				break;
			case strtolower( 'bool' ):
			case strtolower( 'boolean' ):
				$this->modifiers[] = "tinyint (1) NOT NULL";
				break;
			case strtolower( 'float' ):
				$this->modifiers[] = "float";
				break;
			case strtolower( 'dec' ):
			case strtolower( 'decimal' ):
				if ( ! $modifier )
				{
					$modifier = 0;
				}
				$this->modifiers[] = "decimal(10,{$modifier})";
				break;
			default:
				break;
		}
		
		return $this;
	}
	
	public function autoIncrement()
	{
		$this->modifiers[] = 'AUTO_INCREMENT';
		
		return $this;
	}
	
	public function unsigned()
	{
		$this->modifiers[] = 'unsigned';
		
		return $this;
	}
	
	public function timestamp()
	{
		$this->modifiers[] = 'timestamp';
		
		return $this;
	}
	
	public function defaultsTo( $value )
	{
		$this->modifiers[] = "DEFAULT '{$value}'";
		
		return $this;
	}
	
	public function notNull()
	{
		$this->modifiers[] = 'NOT NULL';
		
		return $this;
	}
	
	public function foreignKey( $referenceTable )
	{
		$this->_hasForeignKey = TRUE;
		
		$this->foreignKeyTable = strval( $referenceTable );
		
		return $this;
	}
	
	public function hasForeignKey()
	{
		return $this->_hasForeignKey;
	}
	
	public function __toString()
	{
		$modifiers = implode( ' ', $this->modifiers );
		
		return "`{$this->fieldName}` {$this->type} {$modifiers}";
	}
	
}