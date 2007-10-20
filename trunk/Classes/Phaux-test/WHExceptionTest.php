<?php
/**
 * @package Phaux-test
 */
class WHExceptionTest extends WHComponent {
	
	public function throwException(){
		throw new WHException("Generic excption");
	}
	
	public function triggerError(){
		trigger_error("Generic error",E_USER_ERROR);
	}
	public function triggerUserWarning(){
		trigger_error("Generic warning",E_USER_WARNING);
	}
	public function parseError(){
		include("BogusFile.php");
	}
	public function methodOnNonObject(){
		$foo->foobar();
	}
	public function includeError(){
		include("thisFileDoesNotExist.php");
	}
	public function undefinedMethod(){
		$this->foobar();
	}
	public function renderContentOn($html){
		return $html->div()->id('whexceptiontest')->with(
				$html->anchor()->callback($this,"throwException")->with("Throw Exception").
				$html->br().
				$html->anchor()->callback($this,"triggerError")->with("Trigger Error").
				$html->br().
				$html->anchor()->callback($this,"triggerUserWarning")->
									with("Trigger Warning (will show up in development console)").
				$html->br().
				$html->anchor()->callback($this,"parseError")
					->with("Trigger Parse Error (I can't catch this)").
				$html->br().
				$html->anchor()->callback($this,"methodOnNonObject")
						->with("Method on non-object").
				$html->br().
				$html->anchor()->callback($this,"includeError")
						->with("Include Error").
				$html->br().
				$html->anchor()->callback($this,"undefinedMethod")
						->with("Undefined method").
				$html->br().
				$html->anchor()->callback($this,'thorwException')->
						liveUpdateOn("onClick",$this,'renderContentOn')->
						with('Live Exception Test')
			);
				
	}
	
	
}