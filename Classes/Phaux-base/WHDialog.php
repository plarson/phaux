<?php
/**
 * @package Phaux-base
 */
abstract class WHDialog extends WHComponent{
	protected $errors = array();

	public function addValidationError($aString){
		$this->errors[] = $aString;
	}
	public function clearErrors(){
		$this->errors = array();
	}
	public function isValid(){
		if(sizeof($this->errors) > 0){
			return FALSE;
		}
		return TRUE;
	}
	public function divClass(){
		return 'dialog-'.$this->getClass();
	}

	public function ok(){
		$this->answer(TRUE);
	}

	/*
	**Returns and array of callback=>labels
	*/
	public function buttons(){
		return array('ok'=>'Okay');
	}
	public function buttonCallbackForLabel($label){
		$buttons = $this->buttons();
		return $buttons[$label];
	}
	
	/*
	**Does not work
	*/
	public function defaultCallback(){
		return NULL;
	}
	
	public function renderDialogOn($html){
		$this->subclassResponsibility('renderDialogOn');
	}
	public function renderValidationErrorsOn($html){
		if(sizeof($this->errors) > 0){
			return $html->div()->class('dialog-validation')->with(
				$html->unorderedList()->setItems($this->errors)
			);
		}else{
				return '';
				
		}
	}
	
	
		
	public function renderButtonsOn($html){
	
		if($this->defaultCallback() != NULL ){
		
		}
		foreach($this->buttons() as $callback => $label){
			$buttons .= $html->span()->class('dialog-button')->with(
					$html->submitButton()->callback($this,$callback)->with($label));
		}
		return $html->div()->class('dialog-buttons')->with($buttons);
	}
	
	public function renderContentOn($html){
		
	 	return $this->renderValidationErrorsOn($html).
					$html->form()->class('dialog-form')->with(
				$html->hiddenInput()->callback($this,'clearErrors').
					$html->div()->class($html->divClass())->with(
						$this->renderDialogOn($html)
						).	
					$this->renderButtonsOn($html)
				);			
				
	}
	
}