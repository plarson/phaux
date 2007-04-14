<?php
$WHERROR_TYPES = array (
                E_ERROR              => 'Error',
                E_WARNING            => 'Warning',
                E_PARSE              => 'Parsing Error',
                E_NOTICE             => 'Notice',
                E_CORE_ERROR         => 'Core Error',
                E_CORE_WARNING       => 'Core Warning',
                E_COMPILE_ERROR      => 'Compile Error',
                E_COMPILE_WARNING    => 'Compile Warning',
                E_USER_ERROR         => 'User Error',
                E_USER_WARNING       => 'User Warning',
                E_USER_NOTICE        => 'User Notice',
                E_STRICT             => 'Runtime Notice',
                E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
                );


$WHERROR_FATAL = array(
				E_ERROR,
				E_PARSE,
				E_CORE_ERROR,
				E_COMPILE_ERROR,
				E_USER_ERROR,
				E_RECOVERABLE_ERROR	
				);

class WHError extends Object {

	
	static function registerErrorHandler(){
		error_reporting(0);
		set_error_handler(array("WHError","errorHandler"));

	}

	static function errorHandler($errno,$errstr,$errfile,$errline){
		global $WHERROR_FATAL;
		global $WHERROR_TYPES;
		global $DEBUG_ERRORS;
		if(in_array($errno,$WHERROR_FATAL)){
			throw new WHException($WHERROR_TYPES[$errno], $errno);
		}else{
			$DEBUG_ERRORS .= "\n<!--\n$errstr in $errfile on $errline\n-->\n";
		}
		
	}
	

	
	
}