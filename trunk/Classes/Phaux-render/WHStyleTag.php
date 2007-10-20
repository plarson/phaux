<?php
/**
 * @package Phaux-render
 */
class WHStyleTag extends WHTag{
	public function tag(){
		return 'style';
	}
	
	public function with($contents){
		$this->setAttribute("type","text/css");
		return parent::with($contents);
	}
}