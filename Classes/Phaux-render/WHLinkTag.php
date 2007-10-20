<?php
/**
 * @package Phaux-render
 */
class WHLinkTag extends WHTag {
	protected $doesNotNeedClose = TRUE;
	
	public function tag(){
		return "link";
	}
}