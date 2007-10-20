<?php
/**
 * @package Phaux-base
 */
abstract class WHFormDialog extends WHDialog {
	
	
	
	public function renderDialogOn($html){
		foreach($this->fields() as $label => $callback){
			$rows .= $this->renderRowForOn($html,$label,$callback);
		}
		return $rows;
	}
	
	public function renderRowForOn($html,$label,$callback){
		return $html->div()->class('row')->with(
					$html->span()->class('label')->with($label).
					$html->span()->class('value')->
							with($this->renderFieldForCallbackOn($html,$callback))
					);
	}
	
	public function renderFieldForCallbackOn($html,$callback){
		$methodName = 'renderFieldFor'.$callback.'On';
		if($this->hasMethod($methodName)){
			return $this->$methodName($html);
		}
		return $this->renderTextFieldOn($html,$callback);
	}
	
	/*
	** Return an array of text fields in the format of ...
	** $array['labelName'] = 'keyPath';
	*/
	public function fields(){
		$this->subclassResponsibility('fields');
	}
	
	public function renderLabelOn($html,$label){
		return $html->span()->class('label')->with($label);
	}
	
	public function renderTextFieldOn($html,$callback){
		return $html->textInput()->
					callback($this,'set'.$callback)->
					value($this->$callback());
	}
}