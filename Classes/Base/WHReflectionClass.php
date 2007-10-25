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
	
	/**
	** Returns an array of class names that
	** are a child of this reflected class
	*/
	public function childClassNames(){
		$childClasses = array();
		foreach(get_declared_classes() as $class){
			if(get_parent_class($class) == $this->getName()){
				$childClasses[] = $class;
			}
		}
		return $childClasses;
	}
	
	/**
	** Returns an array of classes that have not parent
	*/
	static public function rootClasses(){
		$rootClasses = array();
		foreach(get_declared_classes() as $class){
			if(!get_parent_class($class)){
				$rootClasses[] = $class;
			}
		}
		return $rootClasses;
	}
	
}