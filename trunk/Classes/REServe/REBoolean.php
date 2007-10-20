<?php
/**
 * @package REServe
 */
class REBoolean extends REInteger {
	public function asSqlValueStringFor($aThing){
		if($aThing){
			return '1';
		}else{
			return '0';
		}
	}
	public function fromSqlValueString($aString){
		return (boolean) $aString;
	}
	public function reServeType(){
		return "boolean";
	}
}