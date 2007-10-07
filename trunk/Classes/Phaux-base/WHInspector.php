<?php
class WHInspector extends WHComponent {
	protected $workspace;
	protected $path;
	
	public function __construct(){
		parent::__construct();
		$this->path = Object::construct('WHPath');
		$this->workspace = Object::construct('WHWorkspace')->setContext($this);
	}
	public function object(){
		return $this->path->currentSegment();
	}
	public function setObject($anObject){
		$this->path->pushSegmentWithName($anObject,$anObject->__toString());
		$this->workspace->setContext($anObject);
		return $this;
	}

	public function renderMemberValueOn($html,$memberValue){
		
		/*
		**use var_dump
		*
		ob_start();
		var_dump($memberValue);
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
		*/
		$return ='';
		if(is_array($memberValue)){
		
			if(TRUE || sizeof($memberValue) < 10){
				foreach($memberValue as $key=>$value){
					$return .= $key.
							" => " .
							$this->renderMemberValueOn($html,$value). 
							$html->br();
				}
				return 'array('.$html->br().$return.')';
			}
		}
		if(is_object($memberValue)){
			return $html->anchor()->
					callback($this,'setObject',array($memberValue))->
					with((string)$memberValue);
		}
		return $memberValue;
		
	}
	
	public function children(){
		return array($this->path,$this->workspace);
	}

	/*
	**This method is to long and should be cleaned up
	*/
	public function renderMembersOn($html){
		$return = '';
		foreach($this->object()->objectVars() as $var => $value){
			$return .= $html->tableData()->with($var);
			$return .= $html->tableData()->with($this->renderMemberValueOn($html,$value));
			$return = $html->tableRow()->with($return);
		}
		return $html->table()->with(
					$html->tableRow()->with(
						$html->tableHeading()->with('Object Member').
						$html->tableHeading()->with('Member Value')
					).
					$return
				);
	}
	
	public function renderWorkspaceOn($html){
		$hostAllow = $this->session()->configuration()->allowedWorkspaceIpAddr();
		
		if($hostAllow == '' || $hostAllow == $_SERVER['REMOTE_ADDR']){
			return $html->render($this->workspace);
		}
	}
	
	public function renderContentOn($html){
		return $html->render($this->path).
				$html->headingLevel(1)->with($this->path->currentSegment()->__toString()).
				$this->renderMembersOn($html).
				$this->renderWorkspaceOn($html);
			
	}
}