<?php
/**
 * @package Phaux-extras
 */
class WHPlot extends WHComponent {
	protected $id;
	protected $type = 'bar';
	protected $width = '100%';
	protected $height = '200px';
	protected $data = array();
	protected $options = array();
	protected $labels = array();
	
	public function __construct(){
	
		parent::__construct();
		if(!is_int($this->classVarNamed('id'))){
			$this->setClassVarNamed('id',0);
		}else{
			$this->setClassVarNamed('id',$this->classVarNamed('id')+1);
		}
		$this->id = $this->classVarNamed('id');
	}
	
	
	/*
	** dataName is a unique name for this data
	** anArrayOfData is in the format of array(x=>y,...)
	** where x and y are both numbers
	*/
	public function setDataNamed($dataName,$anArrayOfData){
		$this->data[$dataName] = $anArrayOfData;
		return $this;
	}
	
	public function getDataNamed($dataName){
		return $this->data[$dataName];
	}
	
	public function removeDataNamed($dataName){
		unset($this->data[$dataName]);
		return $this;
	}
	
	public function setLabelForTick($aNumber,$aString){
		$this->labels[$aNumber] = $aString;
		$this->createOptionForLabels();
		return $this;
	}
	
	public function labelForTick($aNumber){
		return $this->labels[$aNumber];
	}
	public function resetLabels(){
		$this->labels = array();
	}
	
	public function createOptionForLabels(){
		$option = '[';
		$first = true;
		foreach($this->labels as $tick => $value){
			if($first){
				$first = FALSE;
			}else{
				$option .= ' , ';
			}
			$option .= "{v:$tick, label:\"$value\"}";
		}
		$this->setOptionNamed('xTicks',$option.']');
		return $this;
	}
	
	public function setOptionNamed($optionName,$optionValue){
		$this->options['"'.$optionName.'"'] = $optionValue;
		return $this;
	}
	
	public function optionNamed($optionName){
		return $this->options['"'.$optionName.'"'];
	}
	
	public function width(){
		return $this->width;
	}
	public function setWidth($aNumber){
		$this->width = $aNumber;
		return $this;
	}
	
	public function height(){
		return $this->height;
	}
	public function setHeight($aNumber){
		$this->height = $aNumber;
		return $this;
	}
	
	public function validTypes(){
		return array('pie','line','bar');
	}
	
	public function type(){
		return $this->type;
	}
	public function setType($aValidType){
		if(!in_array($aValidType,$this->validTypes())){
			$this->error($aValidType.' is not a valid type. 
							Valid types are '.implode(', ',$this->validTtpes()));
		}
		$this->type = $aValidType;
		return $this;
	}
	
	public function renderCanvasOn($html){
		$id = 'whplot-'.$this->id;
		return $html->div()->id('parent-'.$id)->
					width($this->width)->
					height($this->height)->
					with(
					$html->div()->class('whplot-plot')->
							width($this->width)->
							height($this->height)->
							id($id)
				);
	}

	public function renderScriptOn($html){
		$jscript = 'var hasCanvas = CanvasRenderer.isSupported();';
		$jscript .= 'var opts = {'.
						Object::implodeArrayWithKey(',',':',$this->options).
						'};'."\n";
						
		$dataNames = array();
		foreach($this->data as $dataName => $dataArray){
			$jscript .= 'var '.$dataName.' = [['.
					Object::implodeArrayWithKey('], [',', ',$dataArray).
					']];'."\n";
			$dataNames[] = $dataName;
		}
		
		$dataStringToPass = '['.implode(',',$dataNames).']';
		$jscript .= 'if (hasCanvas) {';
		$jscript .= 'var plot'.$this->id.' = new EasyPlot("'.$this->type.'",opts,$("whplot-'.$this->id.'"),'.$dataStringToPass.');}';
		//$jscript .= 'addLoadEvent( function (){'.$jscript.'});';
		
		$jscript = '
			function resizePlot'.$this->id.' (){
				plotParent = elementDimensions("parent-whplot-'.$this->id.'");
				plotDiv = $("whplot-'.$this->id.'");
				plotDiv.setAttribute("width",plotParent.w+"px");
				$("whplot-'.$this->id.'").innerHTML = "";
				'.$jscript.'
			}
			addToCallStack(window,"onresize",resizePlot'.$this->id.');
			addLoadEvent(resizePlot'.$this->id.');
		';
		
		
		return $html->script()->with($jscript);
	}
	
	public function renderContentOn($html){
		return $this->renderCanvasOn($html).$this->renderScriptOn($html);
	}	
	
	public function updateRoot($anHtmlRoot){
		parent::updateRoot($anHtmlRoot);
		$anHtmlRoot->needsScript('mochikit/MochiKit.js');
		$anHtmlRoot->needsScript('plotkit/excanvas.js');
		$anHtmlRoot->needsScript('plotkit/PlotKit_Packed.js');
	}
	
}