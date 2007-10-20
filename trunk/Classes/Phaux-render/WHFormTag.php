<?php
/**
 * @package Phaux-render
 */
class WHFormTag extends WHTag {
	
	function __construct(){
		global $app;
		$this->setAttribute("action",$_SESSION[$app]['session']->url());
		$this->setAttribute("method","POST");
	}
	
	
	/*
	** Enable live updates for forms
	** don't call liveUpdateFunctionWithUrl directly call
	** liveUpdateOnSubmit
	*/
	public function liveUpdateFunctionWithUrl($url){
		return "xmlLiveUpdaterForForm(document.getElementById('".
						$this->attributeAt('id')."'),'$url');";
	}
	
	/*
	** $object should be the component that you want to call the render method on
	** $function should be the render method for the ajax redraw
	*/
	public function liveUpdateOnSubmit($object,$function,$arguments = ""){
		$renderKey = $this->createCallback($object,$function,$arguments);
		if($this->attributeAt('id') == ''){
			$this->setAttribute('id','auto-ajax-form-id-'.$renderKey);
		}
		$this->setAttribute('action',
					'javascript:'.$this->liveUpdateFunction($renderKey));
		return $this;
	}
	public function tag(){
		return "form";
	}
	
	
}