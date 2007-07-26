<?php

class WHTextAreaTag extends WHTag {
	
	public function tag(){
		return 'textarea';
	}
	
	public function value($aString){
		return $this->with($aString);
	}
	
	/*
	**Copied from WHFormInputTag
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
}