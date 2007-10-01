<?php

/*
** The decoration and dialgstuff need some rethinking
** The functionality is fine but the implimentation is
** a little confusing
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
		$this->subclassResponsibility("renderContentOn");
		return $this;
	}

	
	public function callDialog($aComponent){
		$this->dialog = $aComponent;
		$this->dialog->setParentComponent($this);
		return $this;
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
		$this->parentComponent->restoreSelf();
	}
	
	public function restoreSelf(){
		$this->dialog = NULL;
		$this->dialogCallback = NULL;
	}
	
	public function thisOrDialog(){
		if($this->dialog){
			return $this->dialog;
		}
		return $this;
	}
	
	public function parentComponent(){
		return $this->parentComponet;
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
	
	public function styleOfThisAndChildren(){
		$return = $this->thisOrDialog()->style();
		foreach($this->thisOrDialog()->children() as $child){
			$return .= $child->thisOrDialog()->styles();
		}
		return $return;
	}
	
	public function styleOfDecorations(){
		if($this->thisOrDialog() !== $this){
			$return .= $this->thisOrDialog()->styleOfDecorations();
		}
		foreach($this->decorations as $decoration){
			$return .= $decoration->style();
		}
		return $return;
	}
	
	public function styles(){
		return $this->styleOfThisAndChildren() .
				$this->styleOfDecorations();
		
	}
	
	public function updateRoot($anHtmlRoot){
		if($anHtmlRoot->title() == ""){
			$anHtmlRoot->setTitle("Phaux");
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

	public function script(){
		return "";
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
	
	public function scriptOfThisAndChildren(){
		$return = $this->thisOrDialog()->script();
		foreach($this->thisOrDialog()->children() as $child){
			$return .= $child->thisOrDialog()->scripts();
		}
		
		return $return;
	}
	
	public function scriptOfDecorations(){
		if($this->thisOrDialog() !== $this){
			$return .= $this->thisOrDialog()->scriptOfDecorations();
		}
		foreach($this->thisOrDialog()->decorations() as $decoration){
			$return .= $decoration->script();
		}
		return $return;
	}
	
	public function scripts(){
		return $this->scriptOfThisAndChildren() .
				$this->scriptOfDecorations();
		
	}
	
}