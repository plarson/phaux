<?php

class REDateAndTime extends REDate {
	public function reServeType(){
		return "dateAndTime";
	}
	
	public function asSqlValueStringFor($aThing){
		if($aThing == NULL){
			return NULL;
		}
		return '"'.parent::asSqlValueStringFor($aThing).
				' '.$aThing->getHour().':'.$aThing->getMinute().':'.$aThing->getSecond().'"';
	}
	
	public function fromSqlValueString($aString){
		$parts = explode(' ',$aString);
		$fparts = $parts[0];
		$lparts = $parts[1];
		$fparts = explode("-",$fparts);
		$lparts = explode(":",$lparts);
		
		
		$date = new Date();
		$date->setYear($fparts[0]);
		$date->setMonth($fparts[1]);
		$date->setDay($fparts[2]);
		
		$date->setHour($lparts[0]);
		$date->setMinte($lparts[1]);
		$date->setSecond($lparts[2]);
		return $date;
	}
	
}