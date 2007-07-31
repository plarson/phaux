<?php

class WHSessionSize extends WHComponent {
	protected $summaryTable = array();
	protected $processedObjects = array();
	
	public function __construct(){
		parent::__construct();
		$this->processObject($this->session());
	}
	
	public function serializedSize(){
		return strlen(serialize($this->session()));
	
	}
	
	public function calculatedTotal(){
		$total = 0;
		foreach($this->summaryTable as $object => $totals){
			$total += $totals['total'];
		}
		return $total;
	}
	
	public function calculatedSize(){
		$total = 0;
		foreach($this->summaryTable as $object => $totals){
			$total += $totals['size'];
		}
		return $total;
	}
	
	public function sizeForVar($aVar){
		if(is_null($aVar)){
			return 0;
		}
		return strlen(strval($aVar));
	}
	
	/*
	**Not fast but the only way we can
	** prevent recursion
	*/
	public function hasBeenProcessed($object){
		foreach($this->processedObjects as &$value){
			if($value === $object){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	
	
	
	public function processObject($object){
		if(!is_object($object)){
			$this->error('Can not process a non-object');
		}
		
		if($this->hasBeenProcessed($object)){
			return $this;
		}
		
		$this->processedObjects[] = $object;
		
		if(!isset($this->summaryTable[$object->getClass()])){
			$this->summaryTable[$object->getClass()] = array();
		}
	
		++$this->summaryTable[$object->getClass()]['total'];
			
		foreach($object->objectVars() as $var => $value){
			
			if(is_object($value)){
				$this->processObject($value);
			}elseif(is_array($value)){
				$this->processArrayForObject($value,$object);
			}elseif(!is_resource($value) && !is_null($value)){
				$this->summaryTable[$object->getClass()]['size'] += $this->sizeForVar($value);
			}			
		}
	
		return $this;
	}
	
	public function processArrayForObject($array,$object){
		foreach($array as $var => &$value){
			$this->summaryTable[$object->getClass()]['size'] += $this->sizeForVar($var);
			if(is_array($value)){
				$this->processArrayForObject($value,$object);
			}elseif(is_object($value)){
				$this->processObject($value);
			}elseif(!is_resource($value)){
				$this->summaryTable[$object->getClass()]['size'] += $this->sizeForVar($value);
			}
		}
	}
	
	public function renderExplanationOn($html){
		return $html->div()->class('dialog-validation')->with($this->explanation());
	}
	
	public function renderUsageTableOn($html){
	//	die(var_dump($this->summaryTable));
		return $html->table()->with(
					$html->tableRow()->with(
						$html->tableHeading()->with('Class').
						$html->tableHeading()->with('Instances').
						$html->tableHeading()->with('Bytes')
					).
					$this->renderTableBodyOn($html).
					$this->renderTotalsOn($html)
				);
	}
	
	public function renderTableBodyOn($html){
		$return = '';
	
		foreach($this->summaryTable as $var => $value){
			
			$return .= $html->tableRow()->with(
							$html->tableData()->with($var).
							$html->tableData()->with($value['total']).
							$html->tableData()->with($value['size'])
						);
		}
		return $return;
	}
	
	public function renderTotalsOn($html){
		 return $html->tableRow()->with(
					$html->tableData()->with('Totals').
					$html->tableData()->with($this->calculatedTotal()).
					$html->tableData()->with($this->calculatedSize())
				);
	}
	
	public function renderSerializedSizeOn($html){
		return $html->headingLevel('1')->with($this->serializedSize(). ' bytes when serialized');
	}
	
	public function renderContentOn($html){
		return $this->renderSerializedSizeOn($html).
				$this->renderExplanationOn($html).
				$this->renderUsageTableOn($html);
	}
	
	public function explanation(){
		return 'WHSessionSize displays the memory usage of the basic types that are
				contained in your objects that are reachable from your session.
				This can be useful for dertermining where you might be able
				to cut down on session size. WHSessionSize does not idenitfy 
				actual memory use of a Phaux application. It gives you an of idea how much
				space a session will take up when it is serialized.';
	}
	
}