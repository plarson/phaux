<?php

/*
** Modeled off of Seaside's (http://seaside.st/) WACanvas
*/
/**
 * @package Phaux-render
 */
class WHHtmlCanvas extends WHCanvas {
	protected $baseTag;
	protected $docType;
	protected $mimeType;
	
	
	public function __construct(){
		$this->docType = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" '.
				'"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$this->mimeType = "text/html";
				
	}
	
	public function setMimeType($aString){
		$this->mimeType = $aString;
	}
	public function mimeType(){
		return $this->mimeType;
	}
	
	public function docType(){
		return $this->docType;
	}
	public function setDocType($aString){
		$this->docType = $aString;
	}
	
	/*
	** Tags
	*/ 
	
	public function html(){
		$this->baseTag = $this->constructTagWithClass("WHGenericTag")->setTag("html");
		return $this->baseTag;
	}
	
	public function headingLevel($level){
		return $this->constructTagWithClass("WHGenericTag")->setTag("h".$level);
	}
	
	/*
	**Just return a generic tag for anything you don't 
	** understand
	*/
	function __call($method,$arguments){
		return $this->constructTagWithClass("WHGenericTag")->setTag($method);
	}
	
	public function anchor(){
		return $this->constructTagWithClass("WHAnchorTag");	
	}
	
	public function form(){
		return $this->constructTagWithClass("WHFormTag");
	}
	public function textInput(){
		return $this->constructTagWithClass("WHTextInputTag");
	}
	public function textArea(){
		return $this->constructTagWithClass("WHTextAreaTag");
	}
	
	public function passwordInput(){
		return $this->constructTagWithClass("WHPasswordInputTag");
	}
	public function fileInput(){
		return $this->constructTagWithClass('WHFileInputTag');
	}
	
	public function submitButton(){
		return $this->constructTagWithClass("WHSubmitButtonTag");
	}
	
	public function resetButton(){
		return $this->constructTagWithClass("WHResetButtonTag");
	}
	
	public function hiddenInput(){
		return $this->constructTagWithClass("WHHiddenInputTag");
	}

	public function table(){
		return $this->constructTagWithClass("WHTableTag");
	}

	public function tableData($value = ''){
		return $this->constructTagWithClass("WHTableDataTag")->with($value);
	}
	public function tableRow(){
		return $this->constructTagWithClass("WHTableRowTag");
	}
	
	public function tableHeading (){
		return Object::construct("WHTableHeadingTag");
	}
	
	public function tableBody(){
		return $this->constructTagWithClass("WHGenericTag")->setTag('tbody');
	}
	public function tableHead(){
		return $this->constructTagWithClass("WHGenericTag")->setTag('thead');
	}
	public function tableFoot(){
		return $this->constructTagWithClass("WHGenericTag")->setTag('tfoot');
	}
	
	public function listItem (){
		return $this->constructTagWithClass("WHListItemTag");
	}
	
	public function orderedList(){
		return $this->constructTagWithClass("WHListTag")->beOrdered();
	}
	
	public function unorderedList(){
		return $this->constructTagWithClass("WHListTag")->beUnordered();
	}
	
	public function select(){
		return $this->constructTagWithClass("WHSelectTag");
	}
	
	public function radioButtonGroup(){
		return Object::construct('WHRadioButtonGroup');
	}
	
	public function radioButton(){
		return $this->constructTagWithClass("WHRadioButtonTag");
	}
	
	public function option(){
		return $this->constructTagWithClass("WHOptionTag");
	}
	
	public function text($aString){
		return $this->constructTagWithClass("WHHtmlText")->with(htmlspecialchars($aString));
	}
	
	public function bold($aString){
		return $this->constructTagWithClass("WHGenericTag")->setTag('b')->with(htmlspecialchars($aString));
	}
	
	
	public function pre($aString){
		return $this->constructTagWithClass("WHHtmlText")->with(htmlspecialchars($aString));
	}
	
	public function paragraph($aString){
		return $this->constructTagWithClass("WHGenericTag")->setTag('p')->with(htmlspecialchars($aString));
		
	}
	
	public function div(){
		return $this->constructTagWithClass("WHDivTag");
	}
	
	public function content($content){
		return $this->constructTagWithClass("WHHtmlText")->with($content);
	}
	
	public function link(){
		return $this->constructTagWithClass("WHLinkTag");
	}
	
	public function space(){
		return $this->constructTagWithClass("WHHtmlText")->with('&nbsp;');
	}
	
	public function script(){
		return $this->constructTagWithClass("WHScriptTag");
	}
	
	public function style(){
		return $this->constructTagWithClass("WHStyleTag");
	}
	
	public function render($component){
		$return = $this->constructTagWithClass("WHHtmlText")
				 		->with($component->renderOn($this));
		if(is_object($return)){
			return $return->__toString();
		}else{
			return $return;
		}
	}
	
	public function constructTagWithClass($aTagName){
		return Object::construct($aTagName)->setHtmlCanvas($this);
	}
	
	public function makeLiveResponce(){
		$this->setDocType('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
		<!DOCTYPE xsl:stylesheet [ <!ENTITY nbsp "&#160;"> ]>');
		$this->setMimeType("text/xml");
		return $this;
	}
	
	public function document(){
		header("Content-type: ".$this->mimeType());
		return $this->docType.
				$this->baseTag->__toString();	
	}
	
}
