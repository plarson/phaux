<?php

class WHTag extends Object {
	protected $attributes = array();
	protected $contents = "";
	protected $htmlCanvas;
	protected $callbackKey;/*the key of the callback to exec when clicked */
	protected $doesNotNeedClose = FALSE;
	
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
		$this->attributes[$attribId] = $valueString;
		return $this;
	}
		
	public function __call($method,$arguments){
		if(count($arguments) != 1){
			throw new WHException("Generic attribute can only have one argument");
		}
		$this->setAttribute($method,$arguments[0]);
		return $this;
	}
		
		
	public function registerCallback($object,$function,$arguments = ""){
		global $app;
		if(!is_array($arguments)){
			$arguments = array();
		}
		$this->callbackKey = $_SESSION[$app]['session']->registerCallback(
												$object,$function,$arguments
												)->key();
		return $this;		
		
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
			$return .= sprintf("%s",$contents);
			$return .= "</".$this->tag().">";
		}
		return $return;
	}
	
	

	
}