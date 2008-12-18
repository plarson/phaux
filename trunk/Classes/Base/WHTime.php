<?php
/**
 * @package Base
 */
class WHTime extends Object {
	protected $hour;
	protected $minute;
	protected $second;
	
	public function __construct(){
		$now = time();
		$this->hour = (int)date("G",$now);
		$this->minute = (int)date("i",$now);
		$this->second = (int)date("s",$now);
		return $this;
	}
	
	public function hour(){
		return $this->hour;
	}
	public function setHour($anInteger){
		if($anInteger < 0 || $anInteger > 24){
			throw new WHException("$anHour is not a valid hour");
		}
		$this->hour = $anInteger;
		return $this;
	}
	
	public function minute(){
		return $this->minute;
	}
	public function setMinute($anInteger){
		if($anInteger > 59 || $anInteger < 0){
			throw new WHException("$anIntger is not valid minute");
		}
		$this->minute = $anInteger;
		return $this;
	}
	
	public function second(){
		return $this->second;
	}
	public function setSecond($anInteger){
		if($anInteger > 59 || $anInteger < 0){
			throw new WHException("$anInteger is not a valid second");
		}
		$this->second = $anInteger;
		return $this;
	}
	
	public function fromSqlValueString($aString){
		$parts = explode(':',$aString);
		$this->setHour($parts[0]);
		$this->setMinute($parts[1]);
		$this->setSecond($parts[2]);
		return $this;
	}
	
	public function fromUnixTimestamp($aTimeStamp){
		
		$this->hour = date("G",$aTimeStamp);
		$this->minute = date("i",$aTimeStamp);
		$this->second = date("s",$aTimeStamp);
		return $this;
	}
	
	
	public function asSqlValueString(){
		return sprintf("%02d:%02d:%02d",$this->hour,$this->minute,$this->second);
	}
	
	public function asNiceString(){
		return $this->asSqlValueString();
	}
	
	public function __toString(){
		return $this->asSqlValueString();
	}
	
	public function equal($time1, $time2) {
	    return $time1->second() == $time2->second() && $time1->minute() == $time2->minute() && $time1->hour() == $time2->hour();
	}

}