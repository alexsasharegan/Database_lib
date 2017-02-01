<?php

namespace Database\Exceptions;

/**
 * Class BadQuery
 * @package Database\Exceptions
 */
class BadQuery extends \Exception implements \JsonSerializable {
	
	/**
	 * @var string
	 */
	private $query;
	
	/**
	 * @var array
	 */
	private $logs;
	
	/**
	 * BadQuery constructor.
	 *
	 * @param string          $query
	 * @param int             $mysqliErrorMsg
	 * @param array           $logs
	 * @param int             $code
	 * @param \Exception|NULL $previous
	 */
	public function __construct( $query, $mysqliErrorMsg, array $logs, $code = 0, \Exception $previous = NULL )
	{
		$this->query = $query;
		$message     = $mysqliErrorMsg;
		$this->logs  = $logs;
		parent::__construct( $message, $code, $previous );
	}
	
	/**
	 * @return string
	 */
	public function getQuery()
	{
		return $this->query;
	}
	
	/**
	 * @param string $query
	 */
	public function setQuery( $query )
	{
		$this->query = $query;
	}
	
	/**
	 * @return array
	 */
	public function getLogs()
	{
		return $this->logs;
	}
	
	/**
	 * @param array $logs
	 */
	public function setLogs( $logs )
	{
		$this->logs = $logs;
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$logs = print_r( $this->logs, TRUE );
		
		return __CLASS__ . ". [query: \"{$this->query}\"] {$this->message}\nQuery Logs: $logs";
	}
	
	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		return [
			'type'    => __CLASS__,
			'message' => $this->message,
			'query'   => $this->query,
			'logs'    => $this->logs,
		];
	}
	
}
