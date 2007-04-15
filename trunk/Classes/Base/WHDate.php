<?php

class WHDate extends Object {
	protected $year;
	protected $month;
	protected $day;
	
	public function __construct(){
		$today = time();
		$this->year = date("Y",$today);
		$this->month = date("m",$today);
		$this->day = date("j",$today);
	}
	
	public function year(){
		return $this->year;
	}
	public function setYear($anInteger){
		$this->year = $anInteger;
		return $this;
	}
	public function month(){
		return $this->month;
	}
	public function setMonth($anInteger){
		if($anInteger > 12 || $anInteger < 1){
			throw new WHException("$anInteger is not an valid month range");
		}
		$this->month = $anInteger;
		return $this;
	}
	
	public function day(){
		return $this->day;
	}
	
	/*
	**Checking for a day can be a complex process
	** for now just check if it's between 1 and 31
	*/
	public function setDay($anInteger){
		if($anInteger > 32 || $anInteger < 1){
			throw new WHException("$anInteger could not possibly be a valid day");
		}
		$this->day = $anInteger;
		return $this;
	}
	
	public function fromSqlValueString($aString){
		$parts = explode('-',$aString);
		$this->setYear($parts[0]);
		$this->setMonth($parts[1]);
		$this->setDay($parts[2]);
		return $this;
	}
	
	
	public function toSqlString(){
		return sprintf("%04d-%02d-%02d",$this->year,$this->month,$this->day);
	}
	
	public function __toString(){
		return $this->toSqlString();
	}
}