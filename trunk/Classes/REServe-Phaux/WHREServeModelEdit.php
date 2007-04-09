<?php

/*
**This is the start of a generic way of
** editing reservable objects
** EVERYTHING is subject to RADICAL change
*/

class WHREServeModelEdit extends WHREServeDisplay {

	
	public function renderLabelOn($html,$keyPath){
		return $this->nameForKeyPath($keyPath); 
	}
	
	public function renderValueOn($html,$keyPath){
		if($this->shouldEditKeyPath($keyPath)){
			$checkMethod = "renderValue".ucfirst($keyPath)."On";
			if(method_exists($this,$checkMethod)){
				return $this->$methodName($html);
			}
			$column = $this->reserveable->columnForKeyPath($keyPath);
			$methodName = "renderValueType".$column->typeName()."On";
			if(!method_exists($this,$methodName)){
				$this->error(get_class($this)." does not yet handle ".$column->typeName());
			}else{
				return $this->$methodName($html,$column);
			}
		}
	}
	
	public function renderValueTypeREStringOn($html,$column){
		return $html->
				textInput()->
				value(
					$this->reserveable->getValueForKeyPath($column->keyPath())
				)->callback(
					$this->reserveable,
					"putValueForKeyPath",
					array($column->keyPath())
				);
	}
	
	public function renderValueTypeREIntegerOn($html,$column){
		return $this->renderValueTypeREStringOn($html,$column);
	}
	
	public function renderValueTypeREDateOn($html,$column){
		return $this->renderValueTypeREStringOn($html,$column);
	}
	
	
	public function renderRowOn($html,$keyPath){
		return 	$html->div()->class("row")->with(
					$html->span()->class("label")->with(
						$this->renderLabelOn($html,$keyPath)
					).
					$html->span()->class("value")->with(
						$this->renderValueOn($html,$keyPath)
					)
			);
	}
	
	public function renderButtonsOn($html){
			return $html->div()->class("buttons")->with(
						$html->span()->class("label")->with(
							$html->submitButton()->value("Cancel Button ????!(not yet)")
						).
						$html->span()->class("value")->with(
							$html->submitButton()->value("Update")
						)
				);
	}
	
	public function renderContentOn($html){
		foreach($this->keyPathsToRender() as $keyPath){
			$return .= $this->renderRowOn($html,$keyPath);
		}
		
		return $html->form()->with(
					$return.
					$this->renderButtonsOn($html)
				);
		
	}
	
	public function updateRoot($htmlRoot){
		parent::updateRoot($htmlRoot);
		$htmlRoot->addUrlArg($this->reserveable->tableName()."Id",$this->reserveable->oid());
		return $this;
	}
}