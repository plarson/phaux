<?php
/**
 * @package Phaux-test
 */
class WHFormTest extends WHComponent {
	protected $firstName = "";
	protected $lastName = "";
	protected $sex = "Female";
	protected $fileContents;
	
	public function firstName(){
		return $this->firstName;
	}
	public function setYourName($aString){
		$this->firstName = $aString;
		return $this;
	}
	
	public function lastName(){
		return $this->lastName;
	}
	
	public function setLastName($aString){
		$this->lastName = $aString;
		return $this;
	}
	
	public function sex(){
		return $this->sex;
	}
	
	public function setSex($aSex){
		$this->sex = $aSex;
	}
	
	public function sexes(){
		return array("Male","Female","Yea baby!");
	}
	
	public function renderSexSelectOn($html){
		return $html->select()->setItems($this->sexes())->
								callback($this,"setSex")->
								setSelectedItem($this->sex);
	}
	
	public function submitButton($buttonLabel){
		
	}
	
	public function fileHandle($aPHPFile){
		$this->fileContents = $aPHPFile;
		return $this;
	}
	
	public function renderContentOn($html){
		$cont = $html->headingLevel(1)->
				with($this->firstName." ".$this->lastName. " -- ".$this->sex);
		$cont .= $html->form()->with(
			$html->text("First Name:").
			$html->textInput()->value($this->firstName)->callback($this,'setYourName').
			$html->br().
			$html->text("Last Name:").
			$html->textInput()->value($this->lastName)->callback($this,'setLastName').
			$html->br().
			$html->text("Sex:").
			$this->renderSexSelectOn($html).
			$html->br().
			$html->submitButton()->callback($this,'submitButton')->value("Submit!")			
			);
	
		$cont .= $html->form()->enctype("multipart/form-data")->with(
				$html->bold('File Test:').
				$html->br().
				$html->fileInput()->callback($this,'fileHandle').
				$html->br().
				$html->submitButton()->callback($this,'submitButton')->value("Submit!")
				).
				$html->bold('The Contents of the File:').
				$html->br().
				$html->text(print_r($this->fileContents,TRUE));
	
		return $cont;
	}
	
}