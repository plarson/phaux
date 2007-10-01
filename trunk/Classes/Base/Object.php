<?php

class Object {

		/*
		**Allows a more Smalltalk like syntax
		** self::construct("Object")->whatever();
		** It is also a good idea to return $this
		** when you have nothing else to return 
		** like in getter and seter methods
		** this more easly allows you to chain 
		** method calls
		** $object->setFoo("foo")->setBar("bar")
		*/
		static function construct($class = "",$arg = ""){
			if(!is_string($class)){
				throw new WHException("Can only construct a class from a string");
			}
			if(!class_exists($class)){
				throw new WHException("Class $class does not exist");
			}
			if($arg != ""){
				$object = new $class($arg);
			}else{
				$object = new $class();
			}
			/*
			if(FALSE){
				$p = new WHProxyObjectLogCall();
				$object = $p->__setObject($object);
			}
			*/
						
			return $object;
		}
		
		
		/*
		**This does not work
		** __CLASS__ returns Object and there is no 
		** way to discover the class this is being called from
		*/
		static function init(){
			return self::construct(__CLASS__);
		}
		
		public function __construct(){
			if($this->haveClassVarsBeenInitialized() == FALSE){
				$this->classVarInitialize();
			}
		}
		
		public function isProxyObject(){
			return FALSE;
		}
		
		/*
		** "Class vars"
		*/
		protected function classVarNamed($aString){
			$cv = $this->classVarStorage();
			return $cv[$this->getClass()][$aString];
		}
		protected function setClassVarNamed($aString,$value){
			$cv = $this->classVarStorage();
			$cv[$this->getClass()][$aString] = $value;
			return $this;
		}
		protected function haveClassVarsBeenInitialized(){
			$cv = $this->classVarStorage();
			if(!in_array($this->getClass(),$cv)){
				return FALSE;
			}
			return is_array($cv[$this->getClass()]);
		}

		public function classVarInitialize(){
			$cv = $this->classVarStorage();
			$cv[$this->getClass()] = array();
			return $this;
		}
		
		public function classVarStorage(){
			/*
			**Use session if it exists
			*/
			if(isset($_SESSION)){
				if(!is_array($_SESSION['classVars'])){
					$_SESSION['classVars'] = array();
				}
				return $_SESSION['classVars'];
			}else{
				global $__CLASSVARS;
				if(!is_array($__CLASSVARS)){
					$__CLASSVARS = array();
				}
				return $__CLASSVARS;
			}
			
		}
		
		/*
		** Set an instance var
		*/
		public function setIvarNamed($name,$value){
			$this->$name = $value;
			return $this;
		}
		
		/*
		** Get an instance var
		** If it's an instance var convert it
		** to an ArrayObject so the refernece is maintained
		*/
		public function &getIvarNamed($name){
			if(is_array($this->$name)){
				return Object::construct('ArrayObject',$this->$name);
				
			}
			return $this->$name;
		}
		
		public function hasIvar($name){
			return property_exists($this,$name);
		}
		
		public function hasMethod($name){
			return method_exists($this,$name);
		}
		
		public function subclassResponsibility ($methodName){
			throw new WHException("Subclass " . $this->getClass() . 
					" should Impliment $methodName");			
		}
		
		public function getByKeyPath($aStringKeyPath){
			return $this->$aStringKeyPath();
		}
		public function setByKeyPath($aStringKeyPath,$value){
			$methodName = "set".ucfirst($aStringKeyPath);
			$this->$methodName($value);
		}
		
		public function yourself(){
			return $this;
		}
		
		public function perform($aMethodName,$arguments){
		 	return call_user_func_array(array($this,$aMethodName),$arguments);
		}
		
		/**
		 * Returns an array of a string of subclass names 
		 * that are a subclass of this class
		*/
		public function subClasses(){
			$result = array();
			foreach(get_declared_classes() as $className){
				if(is_subclass_of(Object::construct($className),$this->getClass())){
					$result[] = $className;
				}
			}
			return $result;
		}
		
		public function error($errorMessage = "", $errorNumber = 0){
			throw new WHException($errorMessage,$errorNumber);
		}
		
		/**
		 * Checks if this object is aClassName or a subClass
		 */
		public function kindOf($aClassName){
			return is_a($aClassName);
		}
		
		public function __toString(){
			return "A ".$this->getClass();
		}
		public function getClass(){
			return (string)get_class($this);
		}
		
		/*
		**Copies all instance vars from anObject 
		** that this has is common
		*/
		public function copyFrom($anObject){
			foreach($this->objectVars() as $var => $value){
				$this->$var = $anObject->getIvarNamed($var);
			}
			return $this;
		}
		
		/*
		** Returns an array of this objects vars
		*/
		public function objectVars(){
			return get_object_vars($this);
		}
		
		
		/*
		** Throw an exception instead of an uncatchable error
		*/
		public function __call($method,$args){
			$this->error("Call to undefined method ".$this->getClass()."::".$method);
		}
		
		static function unCamelCase($aString){
			$i++;
			$aString = ucfirst($aString);
			$newString[0] = $aString{0};
			while($aString{$i} != NULL){
				if(ctype_upper($aString{$i})){
					$newString[] = " ";
				}
				$newString[] = $aString{$i};
				$i++;
			}	
			
			return implode("",$newString);		
		}
		
		static public function removeFromArray($val, &$arr){
	          $array_remval = $arr;
	          for($x=0;$x<count($array_remval);$x++)
	          {
	              $i=array_search($val,$array_remval,true);
	              if (is_numeric($i)) {
	                  $array_temp  = array_slice($array_remval, 0, $i );
	                $array_temp2 = array_slice($array_remval, $i+1, count($array_remval)-1 );
	                $array_remval = array_merge($array_temp, $array_temp2);
	              }
	          }
	          return $array_remval;
	    }
	
		static public function charIsWhiteSpace($aChar){
			switch ($aChar){
				case ' ':
				case '\t':
				case '\n':
				case '\r':
					return TRUE;
				default:
					return FALSE;
				
			}
		}
		
		static public function arrayWithRange($start,$end){
			$array = array();
			while($start <= $end){
				$array[] = $start;
				++$start;
			}
			return $array;
		}
		
		/*
		**Takes a string that is formatted ..
		**   <field1><TAB><field2><TAB>...<NEWLINE>
		** 			...
		**   <field1><TAB><field2><TAB>...<NEWLINE>
		** and returns a multidimensional array that
		** repersents the text
		*/
			
		static public function excelPasteToArray($aString){
			$list = trim($aString);
			if(strpos($list,"\n") !== FALSE){
				$parts = explode("\n",$list);
			}else{
				$parts = explode("\r",$list);
			}
			foreach($parts as $value){
				$fields[] = explode("\t",trim($value));
			}
			return $fields;	
			
		}
		
		static public function arrayToArrayExcelPaste($anMDArray){
			$return = '';
			foreach($anMDArray as $row){
				$return .= implode("\t",$row)."\n";
			}
			return $return;
		}
		
		static public function implodeArrayWithKey($implodeGlue,$keyValueGlue,$array){
			$first = TRUE;
			$return = '';
			foreach($array as $var => $value){
				if($first){
					$first = FALSE;
				}else{
					$return .= $implodeGlue;
				}
				$return .= $var.$keyValueGlue.$value;
			}
			return $return;
		}
		
}