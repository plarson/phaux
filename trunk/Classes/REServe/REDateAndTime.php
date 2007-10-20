<?php
/**
 * @package REServe
 */
class REDateAndTime extends WHDateAndTime {
	public function reServeType(){
		return "dateAndTime";
	}
	
	
	public function asSqlValueStringFor($aThing){
		if(!is_object($aThing)){
			return "NULL";
		}
		return $aThing->asSqlValueString();
	}
	

	
	public function needsReServeConnection (){
		return FALSE;
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
	
	
}