<?php
/**
 * Subclass WHDecoration to create a decoration that
 * Can be placed around an arbitray component
 */
/**
 * @package Phaux-base
 */
abstract class WHDecoration extends WHComponent {
	
	protected $decoratedComponent;
	
	
	public function decoratedComponent(){
		return $this->decoratedComponent;
	}
	public function setDecoratedComponent($aComponent){
		$this->decoratedComponent = $aComponent;
		return $this;
	}
	/*
	** Child classes of WHDecoration should not define 
	** renderContentOn
	** like a regular component instead they should
	** define renderDecorationWithComponentOn($html)
	*/
	final function renderContentOn($html){
		$this->error('Should not run!');
	}
	
	/*
	** Your class should override this
	** If you want the parent component to be
	** drawn (you almost certainly do) you must
	** call renderDecoratedComponentOn($html,$parentHtml)
	*/
	public function renderDecorationOn($html,$parentHtml){
		return $html->div()->class('no-decoration')->with(
					$this->renderDecoratedComponentOn($html,$parentHtml)
				);
	}
	
	public function renderDecoratedComponentOn($html,$parentHtml){
		return $parentHtml;
	}
	
}