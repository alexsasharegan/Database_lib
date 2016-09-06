<?php

namespace Database\Exceptions;

class BadQuery extends \Exception implements \JsonSerializable {

  public function __construct( $query, $mysqliErrorMsg, $code = 0, Exception $previous = null ) {
    $this->query = $query;
    $message = $mysqliErrorMsg;
    parent::__construct($message, $code, $previous);
  }

  public function __toString() {
    return __CLASS__ . ": [Query: {$this->query}]: {$this->message}\n";
  }

  public function jsonSerialize() {
    return [ 'message' => $this->message, 'query' => $this->query, ];
  }

}