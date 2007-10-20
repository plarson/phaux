<?php

/*
**This class extends WHTag to make use of the register callback
** methods. It is not intended to be return from a render method.
** WHRadioButtonGroup can not be rendered
*/
/**
 * @package Phaux-render
 */
class WHRadioButtonGroup extends WHTag {
	protected $groupName;
	
	public function callback($object,$function,$args = ""){
		$this->registerCallback($object,$function,$args);
		$this->groupName = "_i[".$this->callbackKey."]";
		return $this;
	}
	
	public function groupName(){
		return $this->groupName;
	}
	
	public function contents(){
		$this->error(__CLASS__. 'is not intended to be rendered');
	}
	

}