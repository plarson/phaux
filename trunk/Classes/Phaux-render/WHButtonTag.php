<?php
/**
 * @package Phaux-render
 */
class WHButtonTag extends WHFormInputTag {


	public function tag(){
		return "button";
	}

	public function type() {
		return "submit";
	}

	public function value($aString){
		$this->with($aString);
		return $this;
	}

	public function with($contents){
		$this->contents = $contents;
		return $this;
	}
	

}