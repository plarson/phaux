<?php
/**
 * @package Phaux-test
 */
class WHDialogTest extends WHComponent {
	protected $yesNoAnswer = FALSE;

	
	public function showDialog(){
		$this->callDialog(Object::construct("WHInformDialog")->
								setMessage("This is an inform dialog."));
	}
	
	public function setYesNoAnswer($aBool){
		$this->yesNoAnswer = $aBool;
		return $this;
	}
	
	public function yesNoAnswer(){
		return $this->yesNoAnswer;
	}
	
	public function yesNoComponent(){
		return Object::construct("WHYesNoDialog")->
								setMessage("This is a yes no dialog.")->
								onAnswerCallback($this,"setYesNoAnswer");
	}
	
	public function yesNoDialog(){
		$this->callDialog($this->yesNoComponent());
	}
	
	public function yesNoModel(){
		$this->callModel($this->yesNoComponent());
		return $this;
	}
	
	public function renderContentOn($html){
		return $html->anchor()->
						callback($this,"showDialog")->
						with("Show dialog").
				$html->br().
				$html->anchor()->
						callback($this,"yesNoDialog")->
						with("Yes no dialog - ".$this->yesNoAnswer).
				$html->br().
				$html->anchor()->
						callback($this,'yesNoModel')->
						with('Yes No model - '.$this->yesNoAnswer);
	}
}