<?php
/**
 * @package Phaux-base
 */
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
		$this->session()->toggleHalos();
		$this->inspectObject($this->decoratedComponent);
		/*
		**Yikes! This looks like a bad idea
		*/
		$this->session()->mainComponent()->thisOrDialog()->
			onAnswerCallback($this->session(),'toggleHalos');
		return $this;
	}
	
	public function browsParent(){
		$this->session()->toggleHalos();
		$this->session()->mainComponent()->callDialog(
						Object::construct('WHBrowser')->
						setCurrentClass($this->decoratedComponent->getClass())->
						addDecoration(Object::construct('WHWindowDecoration'))->
						onAnswerCallback($this->session(),'toggleHalos'));
		return $this;
	}
	
	public function renderSourceButtonOn($html){
		if($this->showHTML){
			$label = 'R';
		}else{
			$label = 'H';
		}
		return $html->text('[ ').
				$html->anchor()->callback($this,'toggleShowHTML')->with($label).
				$html->text(' ]');
	}
	
	public function renderBrowsButtonOn($html){
		return $html->text('[ ').
				$html->anchor()->callback($this,'browsParent')->with('B').
				$html->text(' ]');
	}
	
	public function renderInspectButtonOn($html){
		return $html->text('[ ').
				$html->anchor()->callback($this,'inspectParent')->with('I').
				$html->text(' ]');
	}
	
	public function renderPHPSourceButtonOn($html){
		return $html->text('[ ').
				$html->anchor()->callback($this,'inspectParent')->with('P').
				$html->text(' ]');
	}
	
	public function renderHeaderOn($html){
		return $html->div()->class('halo-header')->with(
				$html->div()->class('icons')->with(
					$html->div()->class('halo-mode')->with(
							$this->renderBrowsButtonOn($html).
							$this->renderInspectButtonOn($html).
							$this->renderSourceButtonOn($html)
						)
					).
					get_class($this->decoratedComponent)
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
			.halo {border-style: solid; border-width: 1px; margin: 4px; border-color: #ccc}
			.halo-header {font-size: 10pt; background-color: #E8EBF0; margin-bottom: 4px; border-bottom:1px #ccc solid;}
			.halo-mode {float: right}
			.halo-icons {float: left}
			.halo-contents {clear: both} 
		';
	}
	
}