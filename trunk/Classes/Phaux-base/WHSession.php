<?php
/**
 * @package Phaux-base
 */
class WHSession extends Object {
//	protected $mainComponent;
	protected $callbacks = array();
	protected $lastCallbackKey = 0;
	protected $appName;
	protected $registries = array(); 
	protected $currentRegistry;
	protected $currentKey;
	protected $isHalosOn = FALSE;
	protected $isProfilerOn = FALSE;
	protected $debugCallbackTime; 
	protected $debugErrors = array();
	
	public function start(){
		$this->currentRegistry = Object::construct("WHStateRegistry");
		$this->registries[] = $this->currentRegistry;
		$this->currentKey = 0;
	}
	
	public function mainComponent(){
		global $app;
		return $_SESSION[$app]['mainComponent'];
	}
	
	
	public function registerCallback($object,$method,$arguments){

		$newKey = $this->newCallbackKey();
		$this->callbacks[$newKey] = Object::construct("WHCallback")->
										setKey($newKey)->
										setObject($object)->
										setMethod($method)->
										setArguments($arguments);
		return $this->callbacks[$newKey];
		
	}
	
	public function registerCollectionCallback($object,$method,$arguments,$collection){
		$newKey = $this->newCallbackKey();
		$this->callbacks[$newKey] = Object::construct("WHCollectionCallback")->
										setKey($newKey)->
										setObject($object)->
										setMethod($method)->
										setArguments($arguments)->
										setItems($collection);
										
		return $this->callbacks[$newKey];
		
	}
	
	/*
	** Forget removes all callbacks and
	** the state registry
	** Essentially disabling the user from using her back
	** button
	*/
	public function forget(){
		$this->callbacks = array();
		$this->registries = array();
	}
	
	public function toggleHalos(){
		$this->isHalosOn = !$this->isHalosOn;
		return $this;
	}
	
	public function isHalosOn(){
		return $this->isHalosOn;
	}
	
	public function toggleProfiler(){
		$this->isProfilerOn = !$this->isProfilerOn;
	}
	
	public function isProfilerOn(){
		return $this->isProfilerOn;
	}

	public function debugCallbackTime(){
		return $this->debugCallbackTime;
	}
	
	public function setDebugCallbackTime($aFloat){
		$this->debugCallbackTime = $aFloat;
		return $this;
	}
	public function debugErrors(){
		return $this->debugErrors;
	}
	
	public function clearDebugErrors(){
		$this->debugErrors = array();
		return $this;
	}
	
	public function addArrayToDebugErrors($anArray){
		if(!is_array($anArray)){
			$this->error('You must pass an array to addArrayToDebugErrors but you passed '.
							print_r($anArray));
		
		}
		/*
		**Keep the size of this down
		*/
		$ne = array_merge($this->debugErrors,$anArray);
		$this->debugErrors = array_slice($ne,-100);
		return $this;
	}
	public function addDebugError($aString){
		$this->debugErrors[] = $aString;
		return $this;
	}
	
	/*
	**Register a value of an object to follow the users back button and
	** any new windows they create
	*/
	public function registerObjectOnKeyPath($anObject,$aStringKeyPath){
		$this->currentRegistry()->registerObjectOnKeyPath($anObject,$aStringKeyPath);
	}
	

	public function nextRegistryKey (){
		return count($this->registries);
	}
	
	public function currentRegistryKey(){
		return $this->currentKey;
	}
	public function currentRegistry(){
		return $this->currentRegistry;
	}
	
	/*
	** The back button will work 25 times!
	*/
	public function maxRegistries (){
		return 25;
	}
	/*
	**How many callbacks do you have on a page at a time?
	** 500 seams like a lot but it is possibable
	*/
	public function maxCallbacks(){
		return 1000;
	}
	
	public function appName(){
		return $this->appName;
	}
	public function setAppName($aString){
		return $this->appName = $aString;
	}

	public function newCallbackKey(){
		/*
		** Try to keep the callback size down
		*/
		if($this->maxCallbacks() <= count($this->callbacks)){
			unset($this->callbacks[count($this->callbacks)- $this->maxCallbacks()]);
		}
		
		return ++$this->lastCallbackKey;
	}
	
	public function callbackByKey($key){
		return $this->callbacks[$key];
	}
	
	public function configuration(){
		return $_SESSION[$this->appName]['configuration'];
	}
	
	public function sessionId(){
		return htmlspecialchars(session_id());
	}
	
	public function startSessionOnAppWithConfiguration($appName,$configuration){
		session_start();
		
		if(!isset($_SESSION[$appName]['configuration'])){
					
			$_SESSION[$appName]['configuration'] = $configuration;
		}
	
		if(!isset($_SESSION[$appName]["session"])){
					
			$_SESSION[$appName]["session"] = $this;
			$_SESSION[$appName]["session"]->setAppName($appName);
			$_SESSION[$appName]["session"]->start();
		}else{
			$_SESSION[$appName]["session"]->resume();
		}
		
		return $_SESSION[$appName]["session"];
	}
	
	public function resume(){
		return $this;
	}
	
	public function restoreRegistry($registryKey){
		if(is_object($this->registries[$registryKey])){
			$this->registries[$registryKey]->restoreState();
			$this->currentRegistry = $this->registries[$registryKey];
			$this->currentKey = $registryKey;
		}
		
	}
	
	public function save(){
		if($this->configuration()->debugMode()){
			$this->addArrayToDebugErrors(WHErrorHandler::nonFatalErrorsThrown());
		}
		$this->saveCurrentRegistry();
		$this->renderStep = FALSE;
		//var_dump($this->registries);
	}
	
	/*
	** a flag that is set when Phaux is rendering
	** and has finished processing callbacks
	*/
	public function startingRenderStep(){
		$this->renderStep = TRUE;
	}

	public function isRenderStep(){
		return $this->renderStep;
	}
	
	public function terminate(){
		global $app;
		global $errorHandler;
		session_destroy();
		header("Location: ".$_SESSION[$app]['configuration']->prettyOrBaseUrl()."/$app");
		$errorHandler->end();
		exit;
	}
	
	public function url(){
		global $app;
		$url = $this->configuration()->prettyOrBaseUrl().
							"/".
							$this->appName.
							"?_r=".$this->currentRegistryKey();
							
		if(!$this->configuration()->useCookie()){
			$url .= "&".$this->configuration()->sessionName()."=".$this->sessionId();
		}
		return $url;
	}
	
	public function urlWithCallbackKey($aNumber){
		return $this->url()."&_k=".$aNumber;
	}
	
	public function saveCurrentRegistry (){
		if($this->maxRegistries() <= count($this->registries)){
			unset($this->registeries[count($this->registries) - $this->maxRegistries()]);
		}
		$newReg = clone $this->currentRegistry();
		$this->registries[] = $newReg;
		$this->currentKey = count($this->registries) -1;
		$this->currentRegistry = $newReg;
		
		
	}
	
}