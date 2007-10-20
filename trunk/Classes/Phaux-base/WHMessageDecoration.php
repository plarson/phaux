<?php
/**
 * @package Phaux-base
 */
class WHMessageDecoration extends WHDecoration {
	protected $message;
	
	public function message(){
		return $this->message;
	}
	
	public function setMessage($aString){
		$this->message = $aString;
		return $this;
	}
	
	public function renderDecorationOn($html,$parentHtml){
		return $html->headingLevel(1)->class('message')->with($this->message).
					$this->renderDecoratedComponentOn($html,$parentHtml);
				
	}
}