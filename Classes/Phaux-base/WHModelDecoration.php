<?php

/*
**The easyist way to use this decoration
** is to call callModel on your component
** callModel is used just like callDialog
*/

class WHModelDecoration extends WHDecoration {

	public function children(){
		return array($this->decoratedComponent->parentComponent);
	}
	
	public function renderChildComponentOn($html){
		/*
		**Ok kids don't try any of this at home. 
		** I am what you call an expert
		**
		** You should "never" call renderContentOn directly except 
		** in this case :-)
		** Calling $html->render($this->decoratedComponent->parentComponent)
		** would cause an infinite loop trying to render the decorations
		*/
		return $this->decoratedComponent->parentComponent->renderContentOn($html);
	}
	
	public function renderDecorationOn($html,$parentHtml){
		return $html->div()->id('model-overlay').
				$html->div()->id('model-window')->with(
						$parentHtml
					).
				$html->script()->with($this->loadScript()).
				$this->renderChildComponentOn($html);
	}
	
	public function updateRoot($anHtmlRoot){
		$anHtmlRoot->needsScript('mochikit/MochiKit.js');
		return $this;
	}
	
	
	
	public function loadScript(){
		return '
			function updateModelBox(){
				fullscreen("model-overlay");
				visualCenter("model-window");
				$("model-window").style.visibility="visible";
			}
			addToCallStack(window, "onresize", updateModelBox);
			addLoadEvent(updateModelBox);
		';
	}
	
}