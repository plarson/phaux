<?php

class WHException extends Exception {

	public function __toString(){
		$return .= "<h1> Uncaught Exception: ".
				$this->message.
				" in ".
				$this->file.
				" on line " .
				$this->line.
				"</h1>";
			
		foreach($this->getTrace() as $point){
			//ob_start();
			//var_dump($point);
			
			//$text .= substr(ob_get_clean(),0,2000);
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
			$return .= nl2br(str_replace(" ","&nbsp;",htmlentities($text)));
			$return .= "<br /><hr />";
			$i++;
			if($i == 15){
				break;
			}
		}
		echo substr($return,0,1000000);
		die("");
		//return substr($return,0,1000);
	}

}