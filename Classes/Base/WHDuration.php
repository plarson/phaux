<?php

/*
**A time duration
** rember we are dealing with 32b integer
*/
/**
 * @package Base
 */
class WHDuration extends Object {
	protected $seconds;

	
	public function setSeconds($aNumber){
		$this->seconds = $aNumber;
		return $this;
	}
	
	public function seconds($aNumber){
		return $this->seconds;
	}
	
	public function __toString(){
		/*
		** The below would be nice but
		** for dates less than a yea for some 
		** reason it does not work
		$sec = (int)$this->seconds;
		return date("z \d\a\y\s G:m:s",$sec);
		*/
		
		$s = $this->seconds;
	
		$h=intval($s/3600);
		
		if($h > 0){
			$s=$s-($h*3600);
		}
		
		$m=intval($s/60);
		if($m > 0){
			$s=$s-($m*60);
		}
		
		return sprintf("%d:%02d:%02d", $h, $m, $s);
			
	}
	
	
		
}