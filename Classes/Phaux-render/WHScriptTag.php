<?php

class WHScriptTag extends WHTag{
	public function tag(){
		return 'script';
	}
	
	public function with($contents){
		$this->contents = '/*<![CDATA[/* */'.$contents.'/* ]]> */';
		return $this;
	}
}