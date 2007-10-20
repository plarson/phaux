<?php
/**
 * @package Phaux-base
 */
class WHWorkspace extends WHComponent {
	protected $code = '';
	protected $results = '';
	protected $context;
	
	public function evalCode($phpCode){
		$this->code = $phpCode;
		ob_start();
		self::evalWithThis($this->context,$phpCode);
		$this->results = ob_get_flush();
		return $this;
	}
	/*
	**used so we can redifine this
	*/
	static function evalWithThis($__this,$phpCode){
		$phpCode = str_replace('$this','$__this',$phpCode);
		return eval($phpCode);
	}
	
	public function setContext($anObject){
		$this->context = Object::construct('WHScopeBreak')->__setObject($anObject);
		return $this;
	}
	
	public function context(){
		return $this->context;
	}
	
	public function renderResultsOn($html){
		if($this->results != ''){
			return $html->div()->class('whinspector-results')->with($this->results);
		}
		return '';
	}
	
	public function renderContentOn($html){
		return $html->div()->class('whinspector')->with(
				$this->renderResultsOn($html).
				$html->form()->with(
					$html->textArea()->with($this->code)->
					callback($this,'evalCode').$html->br().
					$html->submitButton()->value('Evaluate Code!')
				));
	}
	
	public function style(){
		return '
			.whinspector textarea{
				width:600px;
				height:200px;
			} 
		';
	}
	
}

