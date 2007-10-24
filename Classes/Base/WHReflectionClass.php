<?php

class WHReflectionClass extends ReflectionClass {

	public function getCategory(){
		if(!$this->isUserDefined()){
			return 'system';
		}
		$parts = explode('/',$this->getFileName());
		if(sizeof($parts) == 0){
			//Don't have to worry about Mac : anymore
			$parts = explode('\\',$this->getFileName());
		}
		return $parts[sizeof($parts)-2];
	}
	
	public function getMethods(){
		return parent::getMethods();
	}
	
}