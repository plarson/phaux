<?php

class WHBrowser extends WHComponent {
	public $currentClass = '';
	public $currentMethod = '';
	

	public function setCurrentClass($aString){
		$this->currentMethod = '';
		$this->currentClass = $aString;
		return $this;
	}
	public function currentClass(){
		return $this->currentClass;
	}
	
	public function setCurrentMethod($aString){
		$this->currentMethod = $aString;
		return $this;
	}
	public function currentMethod(){
		return $this->currentMethod;
	}
	
	
	public function classList(){
		$classes = get_declared_classes();
		asort($classes);
		return $classes;
	}
	
	public function methodList(){
		if($this->currentClass == ''){
			return array();
		}
		/*
		** Reflection objects can't survive serialization 
		** so we need to use the strings
		*/
	
		$methods = $this->currentClassReflected()->getMethods();
		$methodNames = array();
		foreach($methods as $method){
			$methodNames[] = $method->getName();
		}
		asort($methodNames);
		return $methodNames;
	}
	
	public function currentClassReflected(){
		if($this->currentClass == ''){
			$this->error('You need to set the class string with setCurrentClass first');
		}
	
		return Object::construct('ReflectionClass',$this->currentClass);
	}
	public function currentMethodReflected(){
		if($this->currentMethod == ''){
			$this->error('You need to set the method string with setCurrentMethod first');
		}
		
		/*
		** Shudder This just feals like procedural code
		*/
		return new ReflectionMethod($this->currentClass,$this->currentMethod);
	}
	
	
	public function currentMethodComment(){
		return $this->currentMethodReflected->getDocComment();
	}
	public function currentClassComment(){
		return $this->currentClassReflected()->getDocComment();
	}
	public function currentMethodSource(){
		if($this->currentMethod == ''){
			return '';
		}
		$classSource = file($this->currentMethodReflected()->getFileName());
		
		return $this->currentMethodReflected()->getDocComment(). " \n".
				implode('',array_slice($classSource,$this->currentMethodReflected()->getStartLine()-1,
										$this->currentMethodReflected()->getEndLine() - 
											$this->currentMethodReflected()->getStartLine()+1));
		 
	}
	
	public function renderClassSelectionOn($html){
		return $html->form()->with(
					$html->select()->setItems($this->classList())->
									size(10)->
									submitFormOnChange()->
									callback($this,'setCurrentClass')->
									setSelectedItem($this->currentClass)
				).
				$html->form()->with(
					$html->select()->setItems($this->methodList())->
									size(10)->
									submitFormOnChange()->
									callback($this,'setCurrentMethod')->
									setSelectedItem($this->currentMethod)
				);
	}
	
	public function renderMethodSourceOn($html){
		return $html->div()->class('whbrowser-source')->
						with(highlight_string("<?php\n".
									$this->currentMethodSource(),true));
	}
	
	public function renderContentOn($html){
		return $html->div()->class('whbrowser')->with(
				$this->renderClassSelectionOn($html).
				$this->renderMethodSourceOn($html)
			);
	}
	
	public function style(){
		return '
				.whbrowser form{display:inline;}
				.whbrowser-source {
					font-size: 14px;
				}
			';
	}
	
}