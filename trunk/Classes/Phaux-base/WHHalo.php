<?php

class WHHalo extends WHDecoration{
	protected $showHTML = FALSE;
	
	public function toggleShowHTML(){
		if($this->showHTML){
			$this->showHTML = FALSE;
		}else{
			$this->showHTML = TRUE;
		}
		return $this;
	}
	
	/*
	**Pulling in a global may not be the best way
	** but it is the simpleist now
	*/
	public function inspectParent(){
		global $app;
		$this->session()->toggleHalos();
		$inspector = Object::construct('WHInspector')->
						setObject($this->decoratedComponent);
						
		$inspector->addDecoration(Object::construct('WHWindowDecoration')->
							setTitle('Object Inspector'));
						
		$inspector->onAnswerCallback($this->session(),'toggleHalos');
		
		$_SESSION[$app]['mainComponent']->callDialog(
				$inspector
			);
	}
	
	public function renderSourceButtonOn($html){
		if($this->showHTML){
			$label = 'H';
		}else{
			$label = 'S';
		}
		return $html->text('[ ').
				$html->anchor()->callback($this,'toggleShowHTML')->with($label).
				$html->text(' ]');
	}
	
	public function renderInspectButtonOn($html){
		return $html->text('[ ').
				$html->anchor()->callback($this,'inspectParent')->with('I').
				$html->text(' ]');
	}
	
	public function renderHeaderOn($html){
		return $html->div()->class('halo-header')->with(
				$html->div()->class('icons')->with(
					$html->div()->class('halo-mode')->with(
							$this->renderInspectButtonOn($html).
							$this->renderSourceButtonOn($html)
						)
					).
					$this->decoratedComponent->__toString()
				);
	}	
	public function renderDecorationOn($html,$parentHTML){
		return $html->div()->class('halo')->with(
				$this->renderHeaderOn($html).
				$this->renderDecoratedComponentOn($html,$parentHTML)
				);
	}
	
	public function renderDecoratedComponentOn($html,$parentHTML){
		if($this->showHTML){
			$return = htmlentities($parentHTML);
			//An Html pretty print would be nice here
			$return = str_replace('&lt;','<br />&lt;',$return);
			return $return;
		}
		return parent::renderDecoratedComponentOn($html,$parentHTML);
	}
	
	public function style(){
		return '
			.halo {border-style: solid; border-width: 1px; margin: 4px; border-color: #aaaaaa}
			.halo-header {font-size: 10pt; background-color: #cccccc; margin-bottom: 4px}
			.halo-mode {float: right}
			.halo-icons {float: left}
			.halo-contents {clear: both} 
		';
	}
	
}