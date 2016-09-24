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
	 * @var int
	 */
	private $precision;
	
	/**
	 * @return float
	 */
	private function now()
	{
		return microtime( TRUE );
	}
	
	/**
	 * Timer constructor.
	 *
	 * @param int $precision
	 */
	function __construct( $precision = 3 )
	{
		$this->start     = $this->now();
		$this->precision = $precision;
	}
	
	/**
	 * @return null
	 */
	public function stop()
	{
		$this->end = $this->now();
		$this->setTime( $this->calcTime( $this->end, $this->start ) );
		
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
			return $this->calcTime( $this->now(), $this->start );
		endif;
	}
	
	/**
	 * @return float
	 */
	public function getCurrentTime()
	{
		return $this->calcTime( $this->now(), $this->start );
	}
	
	public function lap()
	{
		return $this->getCurrentTime();
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
	
	private function calcTime( $end, $start )
	{
		return round( ($end - $start), $this->precision );
	}
}