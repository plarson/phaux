<?php
/**
 * @package Phaux-render
 */
class WHFormInputTag extends WHTag {

	function __construct(){
		parent::__construct();
		$this->setAttribute("type",$this->type());
	}
	
	public function tag(){
		return "input";
	}
	
	public function type(){
		$this->subclassResponsibility("type");
	}

	public function liveUpdateFunction($renderKey,$callbackKey = ""){
		global $app;
		$url = $_SESSION[$app]['session']->url();
		if($callbackKey != ""){
			$url .= "&_i[$callbackKey]='+this.value+'";
		}		
		$url .= "&_lu=$renderKey";
		return $this->liveUpdateFunctionWithUrl($url);
		
	}

	/*
	**Register a callback for processing the user data
	** the first argument of the callback called will be
	** string the user entered into the form element
	** 
	**Callback registers a callback with no arguments
	** the RECall frameowork will supply the single 
	** argument when this callback is run
	**
	**Pass args for any extra arguments you might 
	** want processed with the callback. 
	*/
	public function callback($object,$function,$args = ""){
		$this->registerCallback($object,$function,$args);
		/*
		**registerCallback sets callbackKey
		** name this input according to the callback key
		*/
		$this->setAttribute("name","_i[".$this->callbackKey."]");
		return $this;
	}
	
	public function value($aString){
		$this->setAttribute("value",$aString);
		return $this;
	}
	
	public function with($aString){
		return $this->value($aString);
	}

}