<?php
/**
 * @package Phaux-base
 */
class WHPath extends WHComponent {
	protected $stack = array();
	protected $currentSegment = NULL;
	public function __construct(){
		$this->session()->registerObjectOnKeypath($this,"stack");
	}
	
	public function stack(){
		return $this->stack;
	}
	public function setStack($anArray){
		$this->stack = $anArray;
		return $this;
	}
	
	public function pushSegmentWithName($anObject,$name){
		$this->stack[] = array($name,$anObject);
		$this->currentSegment = $anObject;
		return $this;
	}

	public function setAsCurrent($aSegment){
		$newStack = array();
		foreach($this->stack as $position => $association){
			$newStack[] = $association;
			if($aSegment === $association[1]){
				break;
			}
		}
		$this->currentSegment = $aSegment;
		$this->stack = $newStack;
		return $this;
	}

	public function currentSegment(){
		return $this->currentSegment;
	}

	public function currentSegmentName(){
		return $this->currentSegmentName;
	}

	public function renderContentOn($html){
		$return = '';
		foreach($this->stack as $position => $association){
			$segment = $association[1];
			$name = $association[0];
			if($this->currentSegment() === $segment){
				$return .= $html->bold($name);
			}else{
				$return .= 	
							$html->anchor()->
							callback($this,'setAsCurrent',array($segment))->
							with($name).
							$html->space().
							$html->bold('>').
							$html->space();
							
			}
			
		}
		return $html->div()->class('path')->with($return);
	

	}
	
}