<?php
/**
 * @package Phaux-render
 */
class WHTag extends Object {
	protected $attributes = array();
	protected $contents = "";
	protected $htmlCanvas;
	protected $callbackKey;/*the key of the callback to exec when clicked */
	protected $doesNotNeedClose = FALSE;

	protected $ensure_id;
	protected static $ensure_id_counter = 0;

	public function tag (){
		/*
		** the subclass should define this tag
		*/
		$this->subclassResponsibility("tag");
		return $this;
	}
	
	public function doesNotNeedClose($aBool){
		$this->doesNotNeedClose = $aBool;
		return $this;
	}

	
	public function htmlCanvas(){
		return $this->htmlCanvas;
	}
	public function setHtmlCanvas($html){
		$this->htmlCanvas = $html;
		return $this;
	}
	
	public function contents(){
		return $this->contents;
	}
	
	public function with($contents){
		$this->contents = $contents;
		return $this;
	}
	
	public function setAttribute($attribId,$valueString){
	
		$this->attributes[$attribId] = htmlentities($valueString,ENT_COMPAT);
		return $this;
	}
		
	public function attributeAt($attribID){
		return $this->attributes[$attribID];
	}
		
	public function __call($method,$arguments){
		if(count($arguments) != 1){
			throw new WHException("Generic attribute can only have one argument");
		}
		$this->setAttribute($method,$arguments[0]);
		return $this;
	}
		
	public function liveUpdateFunctionWithUrl($url){
		return "xmlLiveUpdaterUri('$url');";
	}
	
	public function liveUpdateFunction($renderKey,$callbackKey = ""){
		global $app;
	
		if($callbackKey != ""){
			$url = $_SESSION[$app]['session']->urlWithCallbackKey($callbackKey);
		}else{
			$url = $_SESSION[$app]['session']->url();
		}
		$url .= "&_lu=$renderKey";
		return $this->liveUpdateFunctionWithUrl($url);
		
	}
		
	public function liveUpdateOn($jsEvent,$object,$function,$arguments = ""){
		$renderKey = $this->createCallback($object,$function,$arguments);
		$this->setAttribute($jsEvent,
			$this->liveUpdateFunction($renderKey,$this->callbackKey));
		return $this;
	}
		
	
	public function liveUpdateWithCallbackOn(	$jsEvent,
												$updateObject,
												$updateFunction,
												$updateArguments,
												$callbackObject,
												$callbackFunction,
												$callbackArguments){
													
		$renderKey = $this->createCallback($updateObject,$updateFunction,$updateArguments);
		$callbackKey = $this->createCallback($callbackObject,$callbackFunction,$callbackArguments);
		$this->setAttribute($jsEvent,
			$this->liveUpdateFunction($renderKey,$callbackKey));
		return $this;
	}
	
		
	public function registerCallback($object,$function,$arguments = ""){
		$this->callbackKey = $this->createCallback($object,$function,$arguments);		
	}

	/*
	** Returns the new callback key
	*/
	public function createCallback($object,$function,$arguments = ""){
		global $app;
		if(!is_array($arguments)){
			$arguments = array();
		}
		$key = $_SESSION[$app]['session']->registerCallback(
												$object,$function,$arguments
												)->key();
		return $key;
	}
		
	
	public function registerCollectionCallback($object,$function,$arguments,$array){
		global $app;
		$this->callbackKey = $_SESSION[$app]['session']->registerCollectionCallback(
											$object,$function,$arguments,$array
											)->key();
		return $this;		
	}
		
	public function __toString(){
		$return = "<".$this->tag();
		foreach($this->attributes as $var => $value){
			$return .= " ".$var."=\"".$value."\"";
		}
		$contents = $this->contents();
		if($this->doesNotNeedClose && $contents == ""){
			$return .= " />";
			
		}else{
			$return .= ">";
			$return .= $contents;//sprintf("%s",$contents);
			$return .= "</".$this->tag().">";
		}
		return $return;
	}
	
	public function ensure_id() {
		if (!$this->ensure_id && !$this->attributeAt('id')) {
			$this->ensure_id = ++self::$ensure_id_counter;
			$this->setAttribute('id', 'id' . $this->ensure_id);
		}
	}

	
}