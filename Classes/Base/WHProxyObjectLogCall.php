<?php
/**
 * @package Base
 */
class WHProxyObjectLogCall {
	protected $__object;
	protected $__callOutput = '/tmp/reservedebug.txt';
	protected $__loggingEnabled = TRUE;
	
	public function __object(){
		return $this->object;
	}
	public function __setObject($anObject){
		$this->__object = $anObject;
		return $this;
	}
	
	public function __call($method,$args){
		
		if(method_exists($this->__object,$method)){
			$callMethod = $method;
		}else{
			$callMethod = '__call';
		}
		
		if($this->__loggingEnabled){
			if($this->__callOutput != NULL){
				file_put_contents($this->__callOutput,
					'Call to '.get_class($this->__object)." $method\n",FILE_APPEND);
			}
		}
		
		if($this->__object === NULL){
		//	throw new WHException("Sorry no object known $method");
		}
		if(method_exists($this->__object,$method)){
			$result = call_user_func_array(array($this->__object,$method),$args);
		}else{
			$result = call_user_func_array(array($this->__object,'__call'),array($method,$args));
		}
		
		if($result === $this->__object){
			return $this;
		}else{
			return $result;
		}
		
	}
	
	public function __toString(){
		return $this->__object->__toString();
	}
}