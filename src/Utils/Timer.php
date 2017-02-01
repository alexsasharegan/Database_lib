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
	 * @var null
	 */
	public $time = NULL;
	/**
	 * @var float
	 */
	private $start;
	/**
	 * @var
	 */
	private $end;
	/**
	 * @var int
	 */
	private $precision;
	
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
	 * @return float
	 */
	private function now()
	{
		return microtime( TRUE );
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
	
	public function lap()
	{
		return $this->getCurrentTime();
	}
	
	/**
	 * @return float
	 */
	public function getCurrentTime()
	{
		return $this->calcTime( $this->now(), $this->start );
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		if ( isset( $this->time ) ):
			return (string) $this->time;
		else:
			return (string) $this->getTime();
		endif;
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
}