<?

class WHFormLabelTag extends WHTag {

	protected $label;
	protected $input;
	protected $reverse = false;

	public function tag() {
		return 'label';
	}

	public function input() {
		return $this->input;
	}

	public function reverse() {
		$this->reverse = true;
		$this->class('reverse');
		$this->input = Object::construct('WHGenericTag')->setTag('span')->class('reverse')->with($this->input());
		return $this;
	}

	public function setInput($aWHTag) {
		$this->input = $aWHTag;
		$this->input->ensure_id();
		$this->setAttribute('for', $this->input->attributeAt('id'));
		return $this;
	}

	public function __toString() {
		return (!$this->reverse ? parent::__toString() : '').$this->input().($this->reverse ? parent::__toString() : '');
	}

}