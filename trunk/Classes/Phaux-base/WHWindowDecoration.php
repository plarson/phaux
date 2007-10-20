<?php
/**
 * @package Phaux-base
 */
class WHWindowDecoration extends WHDecoration {
	
	protected $title = ' ';
	
	public function title(){
		return $this->title;
	}
	
	public function setTitle($aString){
		$this->title = $aString;
		return $this;
	}
	
	public function renderCloseButtonOn($html){
		return $html->anchor()->
				callback($this,'closeWindow')->
				with('close');
	}
	
	public function closeWindow(){
		$this->decoratedComponent->answer(FALSE);
		return $this;
	}
	
	public function renderTitleOn($html){
		return $html->text($this->title);
	}
	
	public function renderDecorationOn($html,$parentHTML){

		return $html->div()->class('window-titlebar')->with(
				$html->span()->class('window-close')->with(
					$this->renderCloseButtonOn($html)
				).
				$html->span()->class('window-title')->with(
					$this->renderTitleOn($html).
					$html->space()
				)
			).
			$html->div()->class('window-content')->with(
				$this->renderDecoratedComponentOn($html,$parentHTML)
			);
	}
	
}