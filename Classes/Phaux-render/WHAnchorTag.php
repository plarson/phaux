<?php

class WHAnchorTag extends WHTag {
	protected $positionKey;/*the #postion append to the end of a link */
	protected $liveUpdaterKey = NULL;
	protected $runCallbackWithLiveUpdate = FALSE;
	
	public function tag(){
		return 'a';
	}
	
	public function position(){
		return $this->positionKey;
	}
	public function setPosition($aString){
		$this->positionKey = $aString;
		return $this;
	}
	
	public function callback($object,$function,$arguments = ""){
		global $app;
		$this->registerCallback($object,$function,$arguments);
		$url = $_SESSION[$app]['session']->configuration()->baseUrl().
			"/$app?SID=".$_SESSION[$app]['session']->sessionId()."&_k=".
			$this->callbackKey.
			"&_r=".$_SESSION[$app]['session']->currentRegistryKey();
		
		$this->setAttribute("href",$url);
		return $this;		
	}

	/*
	** Creats a live update on clicking of the anchor
	** Also allowing the callback to be run as well
	*/
	public function liveUpdate($object,$function,$arguments = ""){
		$this->registerLiveUpdateOn("onClick",$object,$function,$arguments);
		return $this;
	}

	
}