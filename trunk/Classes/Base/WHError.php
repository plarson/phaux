<?php


class WHError extends Object {
	protected static $email_addr = ""; 
	protected static $remote_dbg = ""; 
	protected static $log_file = "";
 	protected static $email = false; 
	protected static $stdlog = true;
	protected static $remote = false; 
	protected static $display = true; 
	protected static $notify = true; 
	protected static $halt_script = true;
	
	static function registerErrorHandler(){
		set_error_handler(array("WHError","errorHandler"));

	}
	
	/**
	 * Taken from http://www.zend.com/zend/spotlight/error.php
	 * This could be made a little cleaner
	 */
	static function errorHandler($errno,$errstr,$errfile,$errline){
		$error_msg = " $errstr occured in $errfile on $errline at ".date("D M j G:i:s T Y");
		$display  = WHError::$display;
		$halt_script = WHError::$halt_script;
		// die(var_dump($errno));
		switch($errno) { 
			
			case E_USER_NOTICE: 
			case E_NOTICE: 
				$halt_script = false;         
				$type = "Notice"; 
				$display = FALSE;
				break; 
	   		case E_USER_WARNING: 
	   		case E_COMPILE_WARNING: 
	   		case E_CORE_WARNING: 
	   		case E_WARNING: 
				$halt_script = false;        
	       		$type = "Warning"; 
	       		break;
	   		case E_USER_ERROR: 
	       	case E_COMPILE_ERROR: 
	   		case E_CORE_ERROR: 
	   		case E_ERROR: 
				$type = "Fatal Error"; 
	       		break; 
	   		case E_PARSE: 
				$type = "Parse Error"; 
	       		break; 
	   		default: 
				$type = "Unknown Error"; 
	       		break; 
	  	}
	
		if(WHError::$notify){ 
	       $error_msg = $type . $error_msg; 
	       if(WHError::$email) error_log($error_msg, 1, $email_addr); 
	       if(WHError::$remote) error_log($error_msg ,2, $remote_dbg); 
	       if($display) echo $error_msg."<br/>"; 
	       if(WHError::$stdlog) { 
	          if($log_file = "") { 
	             error_log($error_msg, 0); 
	          } else { 
	             error_log($error_msg, 3, $log_file); 
	          } 
	       } 
	   } 
	   if($halt_script){
			die("Done.");
		}
		
		return TRUE;
		
	}
	
	
	
	
}