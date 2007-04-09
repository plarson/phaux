<?php

class RETime extends REDate {
	public function reServeType(){
		return "time";
	}
	
	public function asSqlValueStringFor($aThing){
		if($aThing == NULL){
			return NULL;
		}
		return '"'.$aThing->getHour().':'.$aThing->getMinute().':'.$aThing->getSecond().'"';
	}
	
	public function fromSqlValueString($aString){
		$parts = explode(':',$aString);
		$date = new Date();
		$date->setHour($parts[0]);
		$date->setMinte($parts[1]);
		$date->setSecond($parts[2]);
		return $date;
	}
	
}