<?php

class WHExceptionTest extends WHComponent {
	
	public function throwException(){
		throw new WHException("Generic excption");
	}
	
	public function triggerError(){
		trigger_error("Generic error",E_USER_ERROR);
	}
	
	public function renderContentOn($html){
		return $html->anchor()->callback($this,"throwException")->with("Throw Exception").
				$html->br().
				$html->anchor()->callback($this,"triggerError")->with("Trigger Error");
	}
	
	
}