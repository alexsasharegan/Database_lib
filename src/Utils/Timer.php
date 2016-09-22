<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 9/22/16
 * Time: 1:23 PM
 */

namespace Database\Utils;

/**
 * Class Timer
 * @package Database\Utils
 */
class Timer {
	
	/**
	 * @var float
	 */
	private $start;
	
	/**
	 * @var
	 */
	private $end;
	
	/**
	 * @var null
	 */
	public $time = NULL;
	
	/**
	 * @return float
	 */
	private function now()
	{
		list($usec, $sec) = explode( " ", microtime() );
		
		return ((float) $usec + (float) $sec);
	}
	
	/**
	 * Timer constructor.
	 */
	function __construct()
	{
		$this->start = $this->now();
	}
	
	/**
	 * @return null
	 */
	public function stop()
	{
		$this->end = $this->now();
		$this->setTime( round( $this->end - $this->start, 3 ) );
		
		return $this->time;
	}
	
	/**
	 * @return float|null
	 */
	public function getTime()
	{
		if ( ! is_null( $this->time ) ):
			return $this->time;
		else:
			return round( $this->now() - $this->start, 3 );
		endif;
	}
	
	/**
	 * @return float
	 */
	public function getCurrentTime()
	{
		return round( $this->now() - $this->start, 3 );
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		if ( isset($this->time) ):
			return (string) $this->time;
		else:
			return (string) $this->getTime();
		endif;
	}
	
	/**
	 * @param null $time
	 *
	 * @return Timer
	 */
	private function setTime( $time )
	{
		$this->time = $time;
		
		return $this;
	}
}