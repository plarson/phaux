<?php

class WHREServeContactModel extends REServe {
	protected $name;
	protected $phoneNumber;
	protected $email;
	protected $niceness;
	protected $dateOfMeeting;
	
	public function name(){
		return $this->name;
	}
	public function setName($aString){
		$this->name = $aString;
		return $this;
	}
	
	public function setNiceness($anInteger){
		$this->niceness = $anInteger;
		return $this;
	}
	public function niceness(){
		return $this->niceness;
	}
	
	
	public function phoneNumber (){
		return $this->phoneNumber;
	}
	public function setPhoneNumber($aString){
		$this->phoneNumber = $aString;
		return $this;
	}
	
	public function email(){
		return $this->email;
	}
	public function setEmail($aString){
		$this->email = $aString;
		return $this;
	}
	
	public function dayOfMeeting(){
		/*
		** Lazy initialization
		*/
		
		if($this->dayOfMeeting == NULL){
			$this->dayOfMeeting = Object::construct("WHDate");
		}
		return $this->dayOfMeeting;
	}
	
	public function setDayOfMeeting($aDate){
		$this->dayOfMeeting = $aDate;
		return $this;
	}
	
	public function tableDefinition(){
		return parent::tableDefinition()->
				column("name",'REString')->
				column("phoneNumber",'REString')->
				column("email",'REString')->
				column("niceness",'REInteger')->
				column("dayOfMeeting",'REDate');
	}
	
	public function __toString(){
		return $this->name();
	}
	
}