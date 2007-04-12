<?php

class WHLiveNavigation extends WHNavigation {
	protected $id;
	
	public function __construct(){
		global $WHLiveNavigation_ID;
		++$WHLiveNavigation_ID;
		$this->id = $WHLiveNavigation_ID;
		
	}

	public function renderSelectionOn($html){
		
		return $html->div()->id("WHLiveNavigation-".$this->id)->with(
					$html->render($this->selection()));
	}
	
	public function renderComponentLabelOn($html,$component){
		$link = $html->anchor()->
						liveUpdateWithCallbackOn("onClick",
							$this,"renderSelectionOn",array(),
							$this,"setSelection",array($component))->
						with($this->labelForComponent($component));
	
		
	
		return $link;
						
	}
	
	
}