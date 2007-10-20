<?php
/**
 * @package Phaux-base
 */
class WHChoiceDialog extends WHDialog {
	protected $choices = array();
	protected $radioOrSelect = 'select';
	protected $message = 'Please select from the following options';
	
	public function choices(){
		return $this->choices;
	}
	
	public function message(){
		return $this->message;
	}
	public function setMessage($aString){
		$this->message = $aString;
		return $this;
	}
	
	/*
	** The array should be in the  format of
	** $array['valuePassedToCallback'] = 'labelValue';
	*/
	public function setChoices($anArray){
		$this->choices = $anArray;
		return $this;
	}
	
	public function selectedValue(){
		return $this->selectedValue;
	}

	public function setSelectedValue($anArrayIndex){
		$this->selectedValue = $anArrayIndex;
		return $this;
	}
	
	public function useSelect(){
		$this->radioOrSelect = 'select';
		return $this;
	}
	
	public function useRadio(){
		$this->radioOrSelect = 'radio';
		return $this;
	}
	
	public function ok(){
		$this->addValidationError('You must select an option!');
		return $this;
	}
	
	public function renderRadioOn($html){
		$radioGroup = $html->radioButtonGroup()->
								callback($this,'answer');
		$buttons .= "";
		foreach($this->choices as $var => $label){
			$buttons .= $html->div()->with(
							$html->radioButton()->
								ofGroup($radioGroup)->
								value($var).
							$html->text($label)
							);
		}
		return $buttons;
		
	}
	
	public function renderSelectOn($html){
		return $html->select()->
						setItems($this->choices)->
						callback($this,'answer');
	}
	
	public function renderDialogOn($html){
		if($this->radioOrSelect == 'radio'){
			$return = $this->renderRadioOn($html);
		}else{
			$return = $this->renderSelectOn($html);
		}
		return $html->headingLevel(1)->with($this->message).$return;
	}
}