<?php
/**
 * @package REServe
 */
class REDecimal extends REServeBasicType {
	protected $precision = 2;
	
	public function setPrecision($aNumber){
		$this->percision = $aNumber;
		return $this;
	}
	
	public function reServeType(){
		return 'decimal';
	}

	static function withPrecision($aNumber){
		return Object::construct('REDecimal')->setPrecision($aNumber);
	}

}