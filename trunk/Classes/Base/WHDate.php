<?php

/*
** This class is US centric 
** Localization would be very nice but is is a fair
** amount of effort 
*/
/**
 * @package Base
 */
class WHDate extends Object {
	protected $year;
	protected $month;
	protected $day;
	
	public function __construct(){
		$this->fromUnixTimestamp(time());
	}


	public function yesterday(){
		$this->fromString('yesterday');
		return $this;
	}
	public function tomorrow(){
		$this->fromString('tomorrow');
		return $this;
	}
	
	public function fromString($aString){
		if(strtotime($aString) === FALSE){
			return FALSE;
		}			
		$this->fromUnixTimestamp(strtotime($aString));
		return $this;
	}
	
	public function fromUnixTimestamp($aTimeStamp){
			$this->year = date("Y",$aTimeStamp);
			$this->month = (int)date("m",$aTimeStamp);
			$this->day = date("j",$aTimeStamp);
			return $this;
	}
	
	public function year(){
		return $this->year;
	}
	public function setYear($anInteger){
		if($anInteger < 1582){
			throw new WHException("This class does not handle pre Gregorian dates ");
		}
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
		/*
		** Handle MySQL sillyness
		*/
		if($aString == '0000-00-00'){
			return NULL;
		}
		if($aString == NULL){
			return NULL;
		}
		$parts = explode('-',$aString);
		$this->setYear($parts[0]);
		if($parts[1] == '00'){
			return NULL;
		}
		$this->setMonth($parts[1]);
		if($parts[2] == '00'){
			return NULL;
		}
		$this->setDay($parts[2]);
		return $this;
	}
	
	/*
	** returns the number of days in the 
	** set month
	*/
	public function daysInMonth(){
		if($this->isLeapYear()){
			$feb = 29;
		}else{
			$feb = 28;
		} 
		$days = array(1=>31,
					  2=>$feb,
					  3=>31,
					  4=>30,
					  5=>31,
					  6=>30,
					  7=>31,
					  8=>31,
					  9=>30,
					  10=>31,
					  11=>30,
					  12=>31);
		return $days[$this->month];
	}
	
	public function isLeapYear(){
		$leapYear = FALSE;
		if($this->year % 400 == 0){
			$leapYear = TRUE;
		}elseif($this->year % 4 == 0 &&
					$this->year % 100 != 0 ){
			$leapYear = TRUE;
		}
		return $leapYear;
	}
	
	/*
	** Currently I don't let the programmer put
	** bogus years and months so only check the day
	** Should that change ???
	*/
	public function isValid(){
		if($this->days <= $this->daysInMonth()){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/* 
	** This is english centric
	** Offering localazations would be nice
	*/
	static function monthNameForInt($anInt){
		$monthNames = self::months();
		//Make sure you cast to int when you are 
		// dealing with a PHP ordered array
		// if not it looks for the value as 
		// if it was a string finding nothing. 
		return $monthNames[(int)$anInt];			
	}
	
	static function months(){
		return array(1=>"January",
						2=>"February",
						3=>"March",
						4=>"April",
						5=>"May",
						6=>"June",
						7=>"July",
						8=>"August",
						9=>"September",
						10=>"October",
						11=>"November",
						12=>"December");
	}
	
	static function ordinalSuffixForDay($anInt){
		return date("S",strtotime("January $anInt, 1980"));		
	}
	
	public function asSqlValueString(){
		return sprintf("'%04d-%02d-%02d'",$this->year,$this->month,$this->day);
	}
	public function asAmericanString(){
		return self::monthNameForInt($this->month).
				" ".
				(int)$this->day.
				self::ordinalSuffixForDay($this->day).
				", ".
				$this->year;
	}
	
	public function asSwedishString(){
		return sprintf("%04d-%02d-%02d",$this->year,$this->month,$this->day);
	}
	
	public function asNiceString(){
		if(self::equal($this, Object::construct('WHDate'))){
			return 'Today';
		}
		if(self::equal($this, Object::construct('WHDate')->yesterday())){
			return 'Yesterday';
		}
		if(self::equal($this, Object::construct('WHDate')->tomorrow())){
			return 'Tomorrow';
		}
		
		return $this->asAmericanString();
	}
	
	public function __toString(){
		return $this->asAmericanString();
	}
	
	public static function equal($date1, $date2) {
	    return $date1 && $date2 && $date1->month() == $date2->month() && $date1->day() == $date2->day() && $date1->year() == $date2->year();
	}

}