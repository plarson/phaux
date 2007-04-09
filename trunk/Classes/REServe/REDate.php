<?php

class REDate extends Date {
	public function needsReServeConnection (){
		return FALSE;
	}
	
	public function asSqlValueStringFor($aThing){
		if($aThing == NULL){
			return NULL;
		}
		return '"'.$aThing->getYear().'-'.$aThing->getMonth().'-'.$aThing->getDay().'"';
	}
	
	public function fromSqlValueString($aString){
		$parts = explode('-',$aString);
		$date = new Date();
		$date->setYear($parts[0]);
		$date->setMonth($parts[1]);
		$date->setDay($parts[2]);
		return $date;
	}
	
	public function isCollectionModel(){
		return FALSE;
	}
	
	public function isBasic(){
		return TRUE;
	}
	
	public function reServeValueStoredWithObject(){
		return TRUE;
	}
	
	public function shouldEdit(){
		return TRUE;
	}
	
	public function reServeType(){
		return "date";
	}
	
}