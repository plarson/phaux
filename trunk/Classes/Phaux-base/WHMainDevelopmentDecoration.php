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
	
	public function inspectSession(){
		$sessionSize = Object::construct('WHInspector')->
						setObject($this->session())->
						addDecoration(Object::construct('WHWindowDecoration')->
						setTitle('Session Inspector'));
		$this->session()->mainComponent()->callDialog($sessionSize);
	}
	
	public function renderTimeIndexOn($html){
		if(!function_exists('xdebug_time_index')){
			return $html->text('Install Xdebug for time index');
		}
		//This assumes that this method will be one of the last things to
		// run on the render step (It should be)
		return $html->text(' Callback Secs: '.$this->session()->debugCallbackTime(). 
							'/Render Secs:'.xdebug_time_index());
	}
	
	public function renderToolbarOn($html){
		return $html->div()->id('toolbar')->with(
					$html->anchor()->callback($this->session(),'terminate')->with('New Session').
					$html->space().
					$html->anchor()->callback($this->session(),'toggleHalos')->with('Toggle Halos').
					$html->space().
					$html->anchor()->callback($this,'sessionMemoryUsage')->with('Session Memory').
					$html->space().
					$html->anchor()->callback($this,'inspectSession')->with('Inspect Session').
					$html->space().
					$html->anchor()->callback($this->session(),'forget')->with('Forget').
					$this->renderTimeIndexOn($html)
				);
	}
	
	public function style(){
		return '
			#toolbar {position: fixed; bottom: 0; left: 0; right: 0; margin-top: 40px; padding: 3px; clear: both; background: #d3d3d3; font-size: 10pt; z-index: 20}
			#toolbar-profile {margin-top: 40px; padding: 3px; clear: both; background: #d3d3d3; font-size: 10pt}
			
		';
	}
	
}