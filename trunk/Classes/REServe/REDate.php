<?php

class REDate extends WHDate {
	public function needsReServeConnection (){
		return FALSE;
	}
	
	public function asSqlValueStringFor($aThing){
		if($aThing == NULL){
			return NULL;
		}
		return $aThing->asSqlValueString();
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