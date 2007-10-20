<?php
/**
 * @package Base
 */
class WHError extends Object {
	protected $number;
	protected $string;
	protected $file;
	protected $line;
	
	public function number(){
		return $this->number;
	}
	
	public function setNumber($anInteger){
		$this->number = $anInteger;
		return $this;
	}
	
	public function string(){
		return $this->string;
	} 
	public function setString($aString){
		$this->string = $aString;
		return $this;
	}
	
	public function file(){
		return $this->file;
	}
	public function setFile($aString){
		$this->file = $aString;
		return $this;
	}
	
	public function line(){
		return $this->line;
	}
	public function setLine($aNumber){
		$this->line = $aNumber;
		return $this;
	}
	
	public function isUserError(){
		return $this->number == E_USER_ERROR || 
				$this->number == E_USER_WARNING ||
				$this->number == E_USER_NOTICE;
	}
	public function isPhpError(){
		return !$this->isUserError();
	}
	
	public function __toString(){
		return $this->string. ' in '.$this->file.' on '.$this->line;
	}
	
}