<?php

/*
** The decoration and dialgstuff need some rethinking
** The functionality is fine but the implimentation is
** a little confusing
*/
/**
 * @package Phaux-base
 */
abstract class WHComponent extends Object {
	
	/**
	 * A dialog is a subinstance of WHComponent that replaces
	 * the view for this component. 
	 */
	protected $dialog = NULL; 
	
	/**
	 * dialogCallback is the callback that a dialog component
	 * will answer on
	 */
	protected $dialogCallback = NULL;
	
	/**
	 * 	parentComponent is the parent componet of this dialog 
	 *	if it is a dialog
	 */
	protected $parentComponent = NULL;
	
	/**
	 * decorations is an array of components that can add
	 * some sort of "decoration" to this component.
	 */
	protected $decorations = array();
	
	
	/*
	** this function should return an array
	** of instances of objects that this Component
	** needs to render
	*/
	public function children (){
		return array();
	}
	
	public function session (){
		global $app;
		return $_SESSION[$app]['session'];
	}
	
	public function decorations(){
		return $this->decorations;
	}
	
	/*
	**I should of impliment the decorations 
	** in a clearner way to avoid things like this
	** ideas??
	*/
	final function renderOn($html){
		$return = '';
		if($this->thisOrDialog() !== $this){
			$return .=  $this->thisOrDialog()->renderOn($html);
		}else{
			$return .= $this->thisOrDialog()->renderContentOn($html);
		}
		foreach($this->decorations as $decoration){
			$return = $decoration->renderDecorationOn($html,$return);
		}
		return $return;
	}
	
	public function renderContentOn($html){
		$this->subclassResponsibility('renderContentOn');
	}

	
	public function callDialog($aComponent){
		$this->dialog = $aComponent;
		$this->dialog->setParentComponent($this);
		return $this;
	}
	
	public function isDialog(){
	    return is_object($this->parentComponent);
	}
	
	public function inform($aString){
		$this->callDialog(Object::Construct('WHInformDialog')->setMessage($aString));
		return $this;
	}
	
	
	public function callModel($aComponent){
		$this->callDialog(
			$aComponent->addDecoration(Object::construct('WHModelDecoration')
			));
		return $this;
	}
	
	public function inspectObject($anObject){
		$inspector = Object::construct('WHInspector')->
						setObject($anObject);
		$inspector->addDecoration(Object::construct('WHWindowDecoration')->
							setTitle('Object Inspector'));
		$this->session()->mainComponent()->callDialog(
				$inspector
			);
		return $this;
	}
	
	public function onAnswerCallback($object,$method,$arguments = ""){
	
		if($arguments == ""){
			$arguments = array();
		}
	

		$this->dialogCallback = Object::construct("WHCallback")->
										setObject($object)->
										setMethod($method)->
										setArguments($arguments);
										
		return $this;
	}
	
	public function answer($aValue){
		$this->restoreParentComponent();
		if($this->dialogCallback){
			$this->dialogCallback->runWithArgument($aValue);
		}
		return $this;
	}
	
	public function addDecoration($aWHDecoration){
		$aWHDecoration->setDecoratedComponent($this);
		$this->decorations[] = $aWHDecoration;
		return $this;
	}
	
	public function shiftDecoration($aWHDecoration){
	    $aWHDecoration->setDecoratedComponent($this);
	    array_unshift($this->decorations,$aWHDecoration);
	    return $this;
	}
	
	
	public function removeDecoration($aWHDecoration){
		foreach($this->decorations as $position => &$decoration){
			if($decoration === $aWHDecoration){
				array_splice($this->decorations,$position,1);
			}
		}
		return $this;
	}
	
	
	public function addHalo(){
	
		return $this->addDecoration(Object::construct('WHHalo'));
	}
	
	public function removeHalo(){
		foreach($this->decorations as $position => &$decoration){
			if($decoration->getClass() == 'WHHalo'){
				$this->removeDecoration($decoration);
			}
		}
		return $this;
	}
	
	public function hasHalo(){
		foreach($this->decorations as $position => &$decoration){
			if($decoration->getClass() == 'WHHalo'){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function putHaloLast(){
		foreach($this->decorations as $position => &$decoration){
			if($decoration->getClass() == 'WHHalo'){
				$this->removeDecoration($decoration);
				$this->addDecoration($decoration);
			}
		}
		return $this;
	}
	
	public function setupHalo(){
		if($this->hasHalo()){
			$this->putHaloLast();
		}else{
			$this->addHalo();
		}
		return $this;
		
	}
	
	public function setupHaloForAll($remove = FALSE){
		if($this->dialog != NULL){
			$this->dialog->setupHaloForAll($remove);
		}
		foreach($this->children() as $child){
			if(!is_object($child)){
				$this->error('Your component should return an array of children from children()');
			}
			$child->setupHaloForAll($remove);
		}
		if($remove){
			$this->removeHalo();
		}else{
			$this->setupHalo();
		}
		return $this;
	}
	

	public function restoreParentComponent(){
		if(is_object($this->parentComponent)){
			$this->parentComponent->restoreSelf();
		}
		return $this;
	}
	
	public function restoreSelf(){
		$this->dialog = NULL;
		/*
		**This was wrong ?
		*/
		//$this->dialogCallback = NULL;
		return $this;
	}
	
	public function thisOrDialog(){
		if($this->dialog){
			return $this->dialog;
		}
		return $this;
	}
	
	public function parentComponent(){
		return $this->parentComponent;
	}
	
	public function setParentComponent($aComponent){
		$this->parentComponent = $aComponent;
		return $this;
	}
	
	/*
	** Any CSS that you want to be included on the page
	** when this component is rendered
	*/
	public function style(){
		return '';
	}
	
	
	/*
	** Brevity over clearity? You be the judge
	** I am trying desperately not to be clever while
	** managing to do something clever. 
	*/
	public function &addKeyOfThisAndChildrenToArray(&$anArray,$methodKey){
		$anArray[$this->thisOrDialog()->getClass()] = $this->thisOrDialog()->$methodKey();
		foreach($this->thisOrDialog()->children() as $child){
			$child->thisOrDialog()->addKeyToArray($anArray,$methodKey);
		}
		return $anArray;
	}
	
	public function &addKeyOfDecorationsToArray(&$anArray,$methodKey){
		if($this->thisOrDialog() !== $this){
			$this->thisOrDialog()->addKeyOfDecorationsToArray($anArray,$methodKey);
		}
		foreach($this->decorations as $decoration){
			$decoration->addKeyOfThisAndChildrenToArray($anArray,$methodKey);
		}
		return $anArray;
	}
	
	public function &addKeyToArray(&$anArray,$methodKey){
		$this->addKeyOfThisAndChildrenToArray($anArray,$methodKey);
		$this->addKeyOfDecorationsToArray($anArray,$methodKey);
		return $anArray;
	}
	
	public function styles(){
		$array = array();
		$this->addKeyToArray($array,'style');
		return implode(' ',$array);	
		
	}
	
	public function script(){
		return "";
	}
	
	public function scripts(){
		$array = array();
		$this->addKeyToArray($array,'script');
		return implode(' ',$array);
	}
	
	
	public function styleLink(){
		if($this->style() != ""){
			return "<link type=\"text/css\" ".
				"href=\"".$this->session()->configuration()->scriptName().
				"?_sfc=".get_class($this)."&_type=style".
				"&app=".$this->session()->appName()."\" rel=\"stylesheet\" /> ";
		}
	}
	
	public function scriptLink(){
		if($this->script() != ""){
			return "<script type=\"text/javascript\" ".
				"src=\"".$this->session()->configuration()->scriptName().
				"?_sfc=".get_class($this)."&_type=script\"" .
				"&app=".$this->session()->appName()."\"/> ";
		}
	}
	
	
	public function updateRoot($anHtmlRoot){
		if($anHtmlRoot->title() == ""){
			$anHtmlRoot->setTitle("Phaux");
		}
		foreach($this->decorations() as $decoration){
			$decoration->updateRoot($anHtmlRoot);
		}
		return $this;
	}


	final function updateRootWithChildren($anHtmlRoot){
		if($this->dialog != NULL){
			$this->updateRoot($anHtmlRoot);
		}
		$this->thisOrDialog()->updateRoot($anHtmlRoot);
		foreach($this->thisOrDialog()->children() as $child){
			$child->thisOrDialog()->updateRootWithChildren($anHtmlRoot);
		}

		return $this;
	}
	
}