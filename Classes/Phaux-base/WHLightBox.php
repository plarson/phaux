<?php

/*
**Nothing Yet
*/

class WHLightBox extends WHDecoration {
	public function renderDecorationOn($html,$parentHtml){
		return $html->div()->class('no-decoration')->with(
					$this->renderDecoratedComponentOn($html,$parentHtml)
				);
	}
	
	public function style(){
		return '
			html { overflow: hidden; }
			body { overflow: hidden; }
			div#overlay {
			z-index: 9998;
			background-color: black;
			filter: alpha(opacity=40);
			-moz-opacity: 0.4;
			opacity: 0.4;
			}
			div#overlay[id] { position: fixed; }
			div#lightbox { z-index: 9999; }';
		
	}
}