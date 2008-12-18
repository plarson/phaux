<?php
/**
 * @package Phaux-render
 */
class WHCheckBoxTag extends WHFormInputTag {
	
	public function type(){
		return "checkbox";
	}
	
	public function checked(){
		$this->setAttribute("checked","true");
		return $this;
	}

	public function setChecked($aBool) {
		if ($aBool) {
			$this->checked();
		}
		return $this;
	}

}
