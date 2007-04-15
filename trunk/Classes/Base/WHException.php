<?php

class WHException extends Exception {

	public function __toString(){
		/*
		** This folling chunk makes this code NON-PORTABLE
		** Fix this ...
		** It would be better to never directly call WHException in my
		** code but instead call a function that intializes
		** the wanted/needed exception class
		*/
		global $configuration; /* FIX ME PLEASE ^^^ */
		global $errorHandler;
		$errorHandler->end();
		if($configuration->isDeployed()){
			$this->deployedException();
		}else{
			$this->pretyExceptionAndDie();
		}
	}
	
	
	public function deployedException(){
		global $configuration; /* FIX ME PLEASE ^^^ */
		self::writeErrorToTempFile($this->pretyException());
	}
	
	/*
	** Function will write the error to a file
	** and return the name minus the system temp 
	** directory
	*/
	static function writeErrorToTempFile($message){
		global $configuration;
	
		$tmpDir = sys_get_temp_dir();
		$tmpFileName = tempnam($tmpDir,"PE-");
		file_put_contents($tmpFileName,$message);
		die(self::errorPage("# " .baseName($tmpFileName),$configuration->adminEmail()));
	}
	
	static function errorPage($errorNumber,$adminEmail){
		header("HTTP/1.0 500 Internal Server Error");
		return ("<h1>HTTP/1.0 500 Internal Server Error</h1>
			The server encountered an unexpected condition which prevented 
			it from fulfilling the request.<br /><br />
			<b>Error ".$errorNumber."</b><br /><br />
			Please contact the webmaster ".$adminEmail.
			"<hr /><img src='/icon.png' />");
	}
	
	public function pretyExceptionAndDie(){
		echo $this->pretyException();
		die();
	}
	public function pretyException(){
	
		$return .= "<h2> Uncaught Exception: ".
				$this->message.
				" in ".
				$this->file.
				" on line " .
				$this->line.
				"</h2>";
				
		foreach($this->getTrace() as $point){
			$return .= self::niceFromTracePoint($point,TRUE);
		}
			
		return substr($return,0,1000000);
		
	}
	
	public static function niceFromTracePoint($point,$highlight){
		$text = "In file ".$point['file']. " line ".$point['line'];
		$text .= "\n".$point['class'].'::'.$point['function']."(";
		$d = FALSE;
		foreach($point['args'] as $value){
			if($d){
				$text .= ",";
			}else{
				$d = TRUE;
			}
			$text .= $value;
		}
		$text .= ")";
		$return .= "<h4>".	
					nl2br(str_replace(" ","&nbsp;",htmlentities($text))).
					"</h4>";
		
		$return .= self::niceSourceCode($point['file'],$point['line'],$highlight);
		$return .= "<br /><hr />";
		$i++;
		if($i == 15){
			break;
		}
		return $return;
	}
	
	public static function niceSourceCode($file,$line,$highlight = TRUE){
		$fileLines = file($file);
		$start = $line - 8;
		$end = $line + 8;
		$current = $start;
		for($current = $start;$current < $end ; $current++){
			if($current == $line-1){
				$return .= "/*HERE --->*/";
			}
			$return .= $fileLines{$current};
		}
		if($highlight){
			$return = highlight_string("<?\n".$return."?>",TRUE);
		}

		return $return;
	}

}