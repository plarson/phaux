<?php

class WHBrowser extends WHComponent {
	public $currentClass = '';
	public $currentMethod = '';
	public $currentCategory = '';

	public function setCurrentClass($aString){
		$this->currentMethod = '';
		$this->currentClass = $aString;
		$this->currentCategory = $this->currentClassReflected()->getCategory();
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
	
	public function setCurrentCategory($aString){
		$this->currentCategory = $aString;
		$this->currentClass = '';
		$this->currentMethod = '';
		return $this;
	}
	
	public function currentCategory(){
		return $this->currentCategory();
	}
	
	public function classList(){
		$classes = get_declared_classes();
		asort($classes);
		return $classes;
	}
	
	public function currentClassList(){
		$classInCat = array();
		foreach($this->classList() as $class){
			if(Object::construct('WHReflectionClass',$class)->getCategory() 
					== $this->currentCategory){
				
				$classInCat[] = $class;
			}
		}
		return $classInCat;
	}
	public function classCategories(){
		$keyedCats = array();
		$keyedCats['system'] = TRUE;
		foreach($this->classList() as $class){
			$keyedCats[Object::construct('WHReflectionClass',$class)->getCategory()] = TRUE;
		}
		return array_keys($keyedCats);
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
			
			if($method->getDeclaringClass()->getName() == $this->currentClassReflected()->getName()){
				$methodNames[] = $method->getName();
			}
		}
		asort($methodNames);
		return $methodNames;
	}
	
	/**
	**Ick! All this having to do reflection
	** in this class makes me feel ill
	** We should be able to ask the class about 
	** it's self
	*/
	public function methodAsDefined($aMethodName){
		$method = new ReflectionMethod($this->currentClass,$aMethodName);
		$return = '';
		if($method->isStatic()){
			$return .= 'static ';
		}
		if($method->isFinal()){
			$return .= 'final ';
		}
		if($method->isAbstract()){
			$return .= 'abstract ';
		}
		if($method->isPublic()){
			$return .= 'public ';
		}
		if($method->isPrivate()){
			$return .= 'private ';
		}
		if($method->isProtected()){
			$return .= 'protected ';
		}
		if($method->returnsReference()){
			$return .= ' &';
		}
		$return .= $aMethodName.'('.$method->getNumberOfParameters().')';
		return $return;
		
	}
	
	public function methodListWithLabel(){
		$methodAndLabel = array();
		foreach($this->methodList() as $method){
			$methodAndLabel[$method] = $this->methodAsDefined($method);
		}
		
		return $methodAndLabel;
	}
	

	
	public function currentClassReflected(){
		if($this->currentClass == ''){
			$this->error('You need to set the class string with setCurrentClass first');
		}
	
		return Object::construct('WHReflectionClass',$this->currentClass);
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
		if(!is_file($this->currentMethodReflected()->getFileName())){
			return '//UNKNOWN SOURCE';
		}
		$classSource = file($this->currentMethodReflected()->getFileName());
		
		return $this->currentMethodReflected()->getDocComment(). " \n".
				implode('',array_slice($classSource,$this->currentMethodReflected()->getStartLine()-1,
										$this->currentMethodReflected()->getEndLine() - 
											$this->currentMethodReflected()->getStartLine()+1));
		 
	}
	
	public function renderClassSelectionOn($html){
		return 
				$html->form()->with(
					$html->select()->setItems($this->classCategories())->
									size(20)->
									submitFormOnChange()->
									callback($this,'setCurrentCategory')->
									setSelectedItem($this->currentCategory)
				).
				$html->form()->with(
					$html->select()->setItems($this->currentClassList())->
									size(20)->
									submitFormOnChange()->
									callback($this,'setCurrentClass')->
									setSelectedItem($this->currentClass)
					).
				$html->form()->with(
					$html->select()->itemsAndLabels($this->methodListWithLabel())->
									size(20)->
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
					font-size: 12px;
				}
				.whbrowser select{width:200px;}
			';
	}
	
}