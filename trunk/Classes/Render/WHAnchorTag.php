<?php

class WHAnchorTag extends WHTag {
	protected $positionKey;/*the #postion append to the end of a link */
	
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
		$this->setAttribute("href",
				$_SESSION[$app]['session']->configuration()->baseUrl().
					"/$app?SID=".$_SESSION[$app]['session']->sessionId()."&_k=".
					$this->callbackKey.
					"&_r=".$_SESSION[$app]['session']->currentRegistryKey());
		return $this;		
	}
}