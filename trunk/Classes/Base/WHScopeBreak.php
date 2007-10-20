<?php
/**
 * @package Base
 */
class WHScopeBreak extends WHProxyObjectLogCall{
	protected $__loggingEnabled = FALSE;
	public function __get($nm){
		return $this->__object->getIvarNamed($nm);
	}
	
	public function __set($nm,$value){
		//	die(var_dump($this));
		return $this->__object->setIvarNamed($nm,$value);
	}

}