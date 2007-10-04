<?php

class WHMainDevelopmentDecoration extends WHDecoration {
	protected $showErrorConsole = FALSE;	
	protected $showUserErrors = TRUE;
	protected $showPhpErrors = TRUE;
	
	
	public function renderDecorationOn($html,$parentHtml){
		return $this->renderDecoratedComponentOn($html,$parentHtml).
				$this->renderToolbarOn($html);
	}

	public function sessionMemoryUsage(){
		$sessionSize = Object::construct('WHSessionSize')->
							addDecoration(Object::construct('WHWindowDecoration')->
							setTitle('Session Usage'));
		
		$this->session()->mainComponent()->callDialog($sessionSize);
	}
	
	public function inspectSession(){
		$sessionSize = Object::construct('WHInspector')->
						setObject($this->session())->
						addDecoration(Object::construct('WHWindowDecoration')->
						setTitle('Session Inspector'));
		$this->session()->mainComponent()->callDialog($sessionSize);
	}
	
	public function toggleErrorConsole(){
		$this->showErrorConsole = !$this->showErrorConsole;
		return $this;
	}
	
	public function toggleUserErrors(){
		$this->showUserErrors = !$this->showUserErrors;
		return $this;
	}
	public function togglePhpErrors(){
		$this->showPhpErrors = !$this->showPhpErrors;
		return $this;
	}
	
	public function errorConsoleContent(){
		$showErrors = array();
		/*
		**Yikes kind of kludgy
		*/
		foreach($this->session()->debugErrors() as $error){
			if($this->showPhpErrors && $error->isPhpError()){
				$showErrors[] = $error;
			}
			if($this->showUserErrors && $error->isUserError()){
				$showErrors[] = $error;
			}
		}
		
		return implode("<br />",$showErrors);
	} 
	
	public function renderTimeIndexOn($html){
		if(!function_exists('xdebug_time_index')){
			return $html->text('Install Xdebug for time index');
		}
		//This assumes that this method will be one of the last things to
		// run on the render step (It should be)
		return $html->text(' Callback Secs: '.$this->session()->debugCallbackTime(). 
							'/Render Secs:'.xdebug_time_index());
	}
	
	
	public function renderErrorConsoleOn($html){
		if(!$this->showErrorConsole){
			return '';
		}
		return 
			
			$html->div()->id('toolbar-console')->with(
					$html->anchor()->liveUpdateWithCallbackOn('onClick',
									$this,'renderErrorConsoleOn',array(),
									$this->session(),'clearDebugErrors',array())->
									with('Clear Console').	
					$html->space().
					$html->space().
					$html->anchor()->class($this->activeIfTrue($this->showPhpErrors))->
							liveUpdateWithCallbackOn('onClick',
												$this,'renderErrorConsoleOn',array(),
												$this,'togglePhpErrors',array())->
							with('PHP Errors').
					$html->anchor()->class($this->activeIfTrue($this->showUserErrors))->
							liveUpdateWithCallbackOn('onClick',
												$this,'renderErrorConsoleOn',array(),
												$this,'toggleUserErrors',array())->
							with('User Errors').
					$html->div()->id('console-output')->with(
						$this->errorConsoleContent()
					)
				).
				$html->script()->with('
					var objDiv = document.getElementById("console-output");
					objDiv.scrollTop = objDiv.scrollHeight;
				');
				
	}
	
	public function activeIfTrue($aBool){
		if($aBool){
			return 'active';
		}
		return '';
	}
	
	public function renderToolbarOn($html){
		return $html->div()->id('toolbar')->with(
					$html->anchor()->
							callback($this->session(),'terminate')->
							with('New Session').
					$html->space().
					$html->anchor()->class($this->activeIfTrue($this->session()->isHalosOn()))->
							callback($this->session(),'toggleHalos')->
							with('Toggle Halos').
					$html->space().
					$html->anchor()->
							callback($this,'sessionMemoryUsage')->
							with('Session Memory').
					$html->space().
					$html->anchor()->
							callback($this,'inspectSession')->
							with('Inspect Session').
					$html->space().
					$html->anchor()->
							callback($this->session(),'forget')->
							with('Forget').
					$html->space().
					$html->anchor()->class($this->activeIfTrue($this->showErrorConsole))->
							liveUpdateWithCallbackOn('onClick',
									$this,'renderToolbarOn',array(),
									$this,'toggleErrorConsole',array())->
							with('Error Console').
					$this->renderTimeIndexOn($html).
					$this->renderErrorConsoleOn($html)
				);
	}
	
	public function style(){
		return '
			#toolbar {
				position: fixed; 
				bottom: 0; 
				left: 0; right: 0; 
				margin-top: 40px; 
				padding: 3px;
				padding-top:5px; 
				clear: both; 
				background: #d3d3d3; 
				font-size: 8pt; 
				z-index: 20;
				height:15px;
			}
			#toolbar a{
				border:1px outset;
				padding:2px;
				text-decoration:none;
				color:black;
				margin-right:2px;
			}
			
			#toolbar a:active{
				border:1px inset;
				background: #d0d0d0; 
			}
			#toolbar a.active{
				border:1px inset;
				background: #d0d0d0; 
			}
			
			#toolbar-console {
				position: fixed; 
				bottom: 23px; 
				right: 0; 
				width: 600px;
				height: 225px;
				margin-top: 40px;
				border-top: 5px solid #d3d3d3;
				border-left: 5px solid #d3d3d3;
				border-right: 5px solid #d3d3d3;
				overflow:hidden;
				background: #d3d3d3; 
			}
			#toolbar-console #console-output{
				position: fixed;
				background:white;
				overflow:auto;
				bottom: 23px;
				width: 600px;
				right: 5px;
				height: 208px;
			}
		';
	}
	
}