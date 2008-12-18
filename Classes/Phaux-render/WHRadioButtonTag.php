<?php
/**
 * @package Phaux-render
 */
class WHRadioButtonTag extends WHCheckBoxTag {
	protected $group;
	
	public function type(){
		return "radio";
	}
	
	public function group(){
		return $this->group;
	}
	
	public function ofGroup($aWHRadioButtonGroup){
		$this->group = $aWHRadioButtonGroup;
		$this->setAttribute('name',$aWHRadioButtonGroup->groupName());
		return $this;
	}
	
	public function callback($object,$function,$args = ""){
		$this->error(__CLASS__.' can not have a callback associated with it.'.
					__CLASS__.' Must be part of a WHRadioButtonGroup');
		return $this;
	}

}