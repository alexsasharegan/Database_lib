<?php
/*
class Exception {
    protected $message = 'Unknown exception';   // exception message
    private   $string;                          // __toString cache
    protected $code = 0;                        // user defined exception code
    protected $file;                            // source filename of exception
    protected $line;                            // source line of exception
    private   $trace;                           // backtrace
    private   $previous;                        // previous exception if nested exception

    public function __construct($message = null, $code = 0, Exception $previous = null);

    final private function __clone();           // Inhibits cloning of exceptions.

    final public  function getMessage();        // message of exception
    final public  function getCode();           // code of exception
    final public  function getFile();           // source filename
    final public  function getLine();           // source line
    final public  function getTrace();          // an array of the backtrace()
    final public  function getPrevious();       // previous exception
    final public  function getTraceAsString();  // formatted string of trace

    // Overrideable
    public function __toString();               // formatted string for display
}
*/


class BadQuery extends Exception implements JsonSerializable {

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
