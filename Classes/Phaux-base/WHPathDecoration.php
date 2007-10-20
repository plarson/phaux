<?php
/**
 * @package Phaux-base
 */
class WHPathDecoration extends WHWindowDecoration{

	
	public function closeWindow(){
		$this->decoratedComponent->thisOrDialog()->answer(FALSE);
		return $this;
	}

	public function renderCloseButtonOn($html){
		if($this->decoratedComponent->thisOrDialog() === $this->decoratedComponent){
			return $html->bold($this->title) . $html->br();
		}else{
			return $html->anchor()->
					callback($this,'closeWindow')->
					with($this->title).
					$html->text(' > ');
		}
		
	}

	public function renderDecorationOn($html,$parentHTML){

		return $html->span()->class('path-decoration')->with(
					$this->renderCloseButtonOn($html)
				).
				$this->renderDecoratedComponentOn($html,$parentHTML);
	}
}