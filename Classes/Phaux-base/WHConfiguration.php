<?php
/**
 * @package Phaux-base
 */
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
		
		return $this->configValueBySubjectAndKey('server','script_name');
	}
	
	public function setScriptName($aString){
		$this->scriptName = $aString;
		return $this;
	}
	
	public function appUrl(){
		global $app;
		 return $_SESSION[$app]['session']->configuration()->baseUrl().
				"/$app?".$this->sessionName()."=".$_SESSION[$app]['session']->sessionId();
	}
	
	
	public function baseUrl(){
		//die(var_dump($url));
		return $_SERVER['SCRIPT_NAME'];
		
	}
	
	public function prettyUrl() {
		return $this->configValueBySubjectAndKey('server','pretty_url');
	}

	public function prettyOrBaseUrl() {
		if ($this->prettyUrl()) {
			return $this->prettyUrl();
		} else {
			return $this->baseUrl();
		}
	}

	public function basePath(){
		return substr($this->baseUrl(),0,strpos($this->baseUrl(),$this->scriptName()));
	}
	
	public function resourceUrl(){
	    return $this->basePath()."/resource.php/".$this->applicationName();
	}
	
	public function debugMode(){
		return $this->configValueBySubjectAndKey('general','debug');
	}
	public function isDeployed(){
		return !$this->debugMode();
	}
	
	public function adminEmail(){
		return $this->configValueBySubjectAndKey('general','admin_email');
	}
	
	public function allowedWorkspaceIpAddr(){
		return $this->configValueBySubjectAndKey('general','workspace_ipaddr');
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
	
	public function useCookie(){
		return $this->configValueBySubjectAndKey('general','use_cookie');
	}
	
	public function sessionName(){
	    $session_name = $this->configValueBySubjectAndKey('general','session_name');
	    if(empty($session_name)){
	        return "SID";
	    }
	    return $session_name;
	}
	
	public function sessionClass(){
		return $this->configValueBySubjectAndKey('general','session_class');
	}
	
	public function mainClass(){
		return $this->configValueBySubjectAndKey('general','main_class');
	}
	
	public function redirectAfterCallback(){
		return $this->configValueBySubjectAndKey('general','redirect_after_callback');
	}
	
	public function styles(){
		return $this->configValues['styles'];
	}
	
	public function scripts(){
		return $this->configValues['scripts'];
	}
	
	public function renderClass(){
		return $this->configValueBySubjectAndKey('general','render_class');	
	}
	
	/*
	**Supplying the path would be a good idea as well
	*/
	static function parseConfigurationFileForApp($appName){
	    
		if(eregi('^[A-Z_0-9_.]*$', $appName) && file_exists('../Configuration/'.$appName.'.ini')){
			$new_conf = parse_ini_file('../Configuration/'.$appName.'.ini',TRUE);
			
			if(isset($new_conf['general']['parent'])){
			    $parent_configuration = self::parseConfigurationFileForApp($new_conf['general']['parent']);
			}
		
			
			foreach($new_conf as $section => $values){
				if(isset($parent_configuration[$section]) && is_array($parent_configuration[$section])){
					$new_conf[$section] = array_merge($parent_configuration[$section],$values);
				}
			}
			
			if(isset($parent_configuration)){
			    foreach($parent_configuration as $section =>$values){
				    if(!isset($new_conf[$section])){
					    $new_conf[$section] = $values;
				    }
			    }
			}
			return $new_conf;
		}else{
		    throw new WHException("Configuration for $appName not found");
		}
		return NULL;
	}
	
	
	static function startUpOnAppWithIni($app,$app_configuration){
	
		ini_set("session.use_cookies",$app_configuration['general']['use_cookie']);
		session_name($configuration->sessionName());

		$configuration_class = $app_configuration['general']['configuration_class'];
		$configuration = Object::construct($configuration_class);
		$configuration->setApplicationName($app)->
											setConfigValues($app_configuration);

		return $configuration;	
	}
	
	static function currentApplicationName(){
		if(!isset($_REQUEST['app'])){
			$path_portions = explode('/', $_SERVER['PATH_INFO']);
			$app = $path_portions[1];
			$_REQUEST['app'] = $app;
		}else{
			$app = $_REQUEST['app'];
		}
		return $app;
	}
	
	static function render404(){
		header("HTTP/1.0 404 Not Found");
		echo("<h1>No such application $app </h1>");
		exit;
	}
}