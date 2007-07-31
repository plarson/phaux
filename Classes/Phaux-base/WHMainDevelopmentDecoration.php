<?php

class WHMainDevelopmentDecoration extends WHDecoration {
	
	
	
	public function renderDecorationOn($html,$parentHtml){
		return $this->renderDecoratedComponentOn($html,$parentHtml).
				$this->renderToolbarOn($html);
	}

	public function sessionMemoryUsage(){
		$sessionSize = Object::construct('WHSessionSize')->
							addDecoration(Object::construct('WHWindowDecoration')->
							setTitle('Session Usage'));
		
		$this->session()->mainComponent()->callDialog($sessionSize);
	}
	
	public function renderToolbarOn($html){
		return $html->div()->id('toolbar')->with(
					$html->anchor()->callback($this->session(),'terminate')->with('New Session').
					$html->space().
					$html->anchor()->callback($this->session(),'toggleHalos')->with('Toggle Halos').
					$html->space().
					$html->anchor()->callback($this,'sessionMemoryUsage')->with('Session Memory')
				);
	}
	
	public function style(){
		return '
			#toolbar {position: fixed; bottom: 0; left: 0; right: 0; margin-top: 40px; padding: 3px; clear: both; background: #d3d3d3; font-size: 10pt; z-index: 20}
			#toolbar-profile {margin-top: 40px; padding: 3px; clear: both; background: #d3d3d3; font-size: 10pt}
			
		';
	}
	
}