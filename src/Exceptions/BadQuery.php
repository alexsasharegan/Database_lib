<?php

namespace Database\Exceptions;

class BadQuery extends \Exception implements \JsonSerializable {
	
	public function __construct( $query, $mysqliErrorMsg, array $logs, $code = 0, Exception $previous = NULL )
	{
		$this->query = $query;
		$message     = $mysqliErrorMsg;
		$this->logs  = $logs;
		parent::__construct( $message, $code, $previous );
	}
	
	public function __toString()
	{
		$logs = print_r( $this->logs, TRUE );
		
		return __CLASS__ . ". [query: \"{$this->query}\"] {$this->message}\nQuery Logs: $logs";
	}
	
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
