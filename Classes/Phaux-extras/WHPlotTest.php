<?php
/**
 * @package Phaux-extras
 */
class WHPlotTest extends WHComponent{
	protected $plots;
	
	public function __construct(){
		$this->plots['Bar Graph'] = Object::construct('WHPlot')->
						setType('bar')->
						setDataNamed('foo1',array(0=>5,1=>4,2=>6))->
						setDataNamed('foo2',array(0=>4,1=>3,2=>5))->
						setDataNamed('foo3',array(0=>1,1=>2,2=>4))->
						setLabelForTick('0','One')->
						setLabelForTick('1','Two')->
						setLabelForTick('2','Three');
		$this->plots['Line Graph'] = Object::construct('WHPlot')->
						setType('line')->
						setDataNamed('foo1',array(0=>5,1=>4,2=>6))->
						setDataNamed('foo2',array(0=>4,1=>3,2=>5))->
						setDataNamed('foo3',array(0=>1,1=>2,2=>4))->
						setLabelForTick('0','One')->
						setLabelForTick('1','Two')->
						setLabelForTick('2','Three');
		$this->plots['Pie Chart'] = Object::construct('WHPlot')->
						setType('pie')->
						setDataNamed('foo1',array(0=>5,1=>4,2=>6))->
						setWidth('200px')->
						setHeight('200px');
				
	}
	
	public function renderContentOn($html){
		$return = '';
		foreach($this->plots as $heading => $plot){
			$return .= $html->headingLevel(1)->with($heading).
						$html->render($plot); 
		}
		return $return;
	}
	
	public function children(){
		return $this->plots;
	}
	
	
	
}