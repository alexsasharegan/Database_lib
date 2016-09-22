<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 9/22/16
 * Time: 5:49 AM
 */

namespace Database;

/**
 * Class TableBuilder
 * @package Database
 */
class TableBuilder {
	
	/**
	 * @var string
	 */
	private $tableName;
	
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}
	
	/**
	 * @var string
	 */
	private $collation = 'utf8_unicode_ci';
	
	/**
	 * @var array
	 */
	private $fields = [];
	
	/**
	 * @var string
	 */
	private $primaryKey = 'id';
	
	/**
	 * @var string
	 */
	private $engine = 'InnoDB';
	
	/**
	 * TableBuilder constructor.
	 *
	 * @param $tableName
	 */
	public function __construct( $tableName )
	{
		$this->tableName = $tableName;
		
		$this->addField( $this->primaryKey )->isType( 'int' )->unsigned()->notNull()->autoIncrement();
	}
	
	/**
	 * @param $fieldName
	 *
	 * @return TableField
	 */
	public function addField( $fieldName )
	{
		$tableField     = new TableField( $fieldName );
		$this->fields[] = $tableField;
		
		return $tableField;
	}
	
	/**
	 * @return string
	 */
	public function render()
	{
		$fields         = implode( ',', $this->fields );
		$keyConstraints = $this->getKeyConstraints();
		
		$createStatement =
			"CREATE TABLE {$this->tableName} (
			$fields,
			$keyConstraints
		) ENGINE={$this->engine} CHARSET=utf8 COLLATE={$this->collation};";
		
		return $createStatement;
	}
	
	/**
	 * @return string
	 */
	private function getKeyConstraints()
	{
		$primaryKeyConstraint = "PRIMARY KEY ({$this->primaryKey})";
		
		$foreignKeyFields = $this->getForeignKeyFields();
		
		if ( $foreignKeyFields )
		{
			$tableName            = $this->tableName;
			$fk                   = $foreignKeyFields[0]->getForeignKeyField();
			$refTable             = $foreignKeyFields[0]->getForeignKeyTable();
			$foreignKeyConstraint = ", KEY `{$fk}` (`{$fk}`), CONSTRAINT `{$tableName}_{$refTable}_fk_{$fk}` FOREIGN KEY (`{$fk}`) REFERENCES `{$refTable}` (`id`)";
		}
		else
		{
			$foreignKeyConstraint = '';
		}
		
		
		return $primaryKeyConstraint . $foreignKeyConstraint;
	}
	
	/**
	 * @return array|null
	 */
	private function getForeignKeyFields()
	{
		$foreignKeyFields = array_filter( $this->fields, function ( TableField $field )
		{
			return $field->hasForeignKey();
		} );
		
		if ( $foreignKeyFields )
		{
			return $foreignKeyFields;
		}
		else
		{
			return NULL;
		}
	}
	
}