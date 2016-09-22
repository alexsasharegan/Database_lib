<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 9/22/16
 * Time: 6:07 AM
 */

namespace Database;

/**
 * Class TableField
 * @package Database
 */
class TableField {
	
	/**
	 * @var int
	 */
	private $integer = 11;
	
	/**
	 * @var string
	 */
	private $collation = 'utf8_unicode_ci';
	
	/**
	 * @var
	 */
	private $fieldName;
	
	/**
	 * @var
	 */
	private $type;
	
	/**
	 * @var array
	 */
	private $modifiers = [];
	
	/**
	 * @var bool
	 */
	private $_hasForeignKey = FALSE;
	
	/**
	 * @var
	 */
	private $foreignKeyTable;
	
	/**
	 * @var string
	 */
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
	
	/**
	 * TableField constructor: takes the name of the table to be created.
	 *
	 * @param string $fieldName
	 */
	public function __construct( $fieldName )
	{
		$this->fieldName = $fieldName;
	}
	
	/**
	 * Sets the type for this field.
	 * Add a modifier option for things like
	 * - the length of an integer field
	 * - the precision of a decimal field
	 *  - the collation of a text field
	 *
	 * @param      $type
	 * @param null $modifier
	 *
	 * @return $this
	 */
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
				
				$this->modifiers[] = "int({$modifier})";
				$this->type        = 'int';
				break;
			case stristr( strtolower( $type ), 'text' ) . stristr( strtolower( $type ), 'text', TRUE ):
				if ( ! $modifier )
				{
					$modifier = "COLLATE " . $this->collation;
				}
				
				$this->modifiers[] = "{$type} {$modifier}";
				$this->type        = 'text';
				break;
			case strtolower( 'bool' ):
			case strtolower( 'boolean' ):
				$this->modifiers[] = "tinyint (1) NOT NULL";
				$this->type        = 'boolean';
				break;
			case strtolower( 'float' ):
				$this->modifiers[] = "float";
				$this->type        = 'float';
				break;
			case strtolower( 'dec' ):
			case strtolower( 'decimal' ):
				if ( ! $modifier )
				{
					$modifier = 0;
				}
				$this->modifiers[] = "decimal(10,{$modifier})";
				$this->type        = 'decimal';
				break;
			default:
				break;
		}
		
		return $this;
	}
	
	/**
	 * Sets AUTO_INCREMENT on the field
	 *
	 * @return $this
	 */
	public function autoIncrement()
	{
		$this->modifiers[] = 'AUTO_INCREMENT';
		
		return $this;
	}
	
	/**
	 * Designates an unsigned field
	 *
	 * @return $this
	 */
	public function unsigned()
	{
		$this->modifiers[] = 'unsigned';
		
		return $this;
	}
	
	/**
	 * Designates a timestamp field
	 *
	 * @return $this
	 */
	public function timestamp()
	{
		$this->modifiers[] = 'timestamp';
		
		return $this;
	}
	
	/**
	 * Sets a default value on the field
	 *
	 * @param $value
	 *
	 * @return $this
	 */
	public function defaultsTo( $value )
	{
		$this->modifiers[] = "DEFAULT '{$value}'";
		
		return $this;
	}
	
	/**
	 * Disallows NULL on a field (default for boolean fields)
	 *
	 * @return $this
	 */
	public function notNull()
	{
		if ( ! $this->type === 'boolean')
		{
			$this->modifiers[] = 'NOT NULL';
		}
		
		return $this;
	}
	
	/**
	 * Imposes a foreign key constraint on the field
	 *
	 * @param string $referenceTable
	 *
	 * @return $this
	 */
	public function foreignKey( $referenceTable )
	{
		$this->_hasForeignKey = TRUE;
		
		$this->foreignKeyTable = strval( $referenceTable );
		
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function hasForeignKey()
	{
		return $this->_hasForeignKey;
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$modifiers = implode( ' ', $this->modifiers );
		
		return "`{$this->fieldName}` {$this->type} {$modifiers}";
	}
	
}