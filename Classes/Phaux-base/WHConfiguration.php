<?php

class WHConfiguration extends Object {
	protected $configValues; /*Stored as a multi deminsional array 
							 ** 1st deminsion is the category
							 ** 2nd deminsion is the key name
							  */
	protected $applicationName;
							
	public function serverName(){
		global $SERVER_NAME;
		return $SERVER_NAME;
	}
	
	public function scriptName(){
		return "recall.php";
	}
	
	public function setScriptName($aString){
		$this->scriptName = $aString;
		return $this;
	}
	
	public function baseUrl(){
		//die(var_dump($url));
		return $_SERVER['SCRIPT_NAME'];
		
	}
	public function basePath(){
	
		return "/".substr($this->baseUrl(),0,strpos($this->baseUrl(),$this->scriptName()));
	}
	
	public function applicationName(){
		return $this->applicationName;
	}
	public function setApplicationName($aString){
		$this->applicationName = $aString;
		return $this;
	}
	
	public function setConfigValues($aMDArray){
		$this->configValues = $aMDArray;
		return $this;
	}
	
	public function configValueBySubjectAndKey($subject,$key){
		return $this->configValues[$subject][$key];
	}
}