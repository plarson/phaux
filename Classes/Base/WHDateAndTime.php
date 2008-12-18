<?php
/**
 * @package Base
 */
class WHDateAndTime extends WHMultipleInheritance {
	protected $classes = array("WHDate","WHTime");
	
	
	
	
	public function fromSqlValueString($aString){
		if($aString == ""){
			return NULL;
		}
		$parts = explode(' ',$aString);
		$fparts = $parts[0];
		$lparts = $parts[1];
		$fparts = explode("-",$fparts);
		$lparts = explode(":",$lparts);
		
		
		$date = $this->thisForClass("WHDate");
		$date->setYear($fparts[0]);
		$date->setMonth($fparts[1]);
		$date->setDay($fparts[2]);
		
		$time = $this->thisForClass("WHTime");
		$time->setHour($lparts[0]);
		$time->setMinute($lparts[1]);
		$time->setSecond($lparts[2]);
	
		return $this;
	}
	
		
	public function fromString($aString){
		if(strtotime($aString) === FALSE){
			return FALSE;
		}			
		$this->fromUnixTimestamp(strtotime($aString));
		return $this;
	}
	
	public function fromUnixTimestamp($aTimeStamp) {
	    $this->thisForClass("WHDate")->fromUnixTimestamp($aTimeStamp);
		$this->thisForClass("WHTime")->fromUnixTimestamp($aTimeStamp);
		return $this;
	}

	protected function asSqlValueString(){
		$val = $this->thisForClass("WHDate")->asSqlValueString() 
				." ".
				$this->thisForClass("WHTime")->asSqlValueString();
				
		$val = str_replace("'","",$val);
		return "'".$val."'";
	}
	
	/*
	**Returns a string that is nicly formatted for 
	** humans
	*/
	public function asNiceString(){
		return $this->thisForClass("WHDate")->asNiceString().', '.$this->thisForClass("WHTime")->asNiceString() ;
	}
	
	public function __toString(){
		return $this->asNiceString();
	}
}