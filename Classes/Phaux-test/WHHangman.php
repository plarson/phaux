<?php
/**
 * @package Phaux-test
 */
class WHHangman extends WHComponent {
	protected $level;
	protected $guesses = array();
	protected $word;
	
	public function __construct(){
		parent::__construct();
		$this->newGame();
	}
	
	public function level(){
		return $this->level;
	}
	public function setLevel($aLevel){
		$this->level = $aLevel;
		return $this;
	}
	
	public function correctGuesses(){
		return array_intersect($this->guesses,str_split($this->word));
	}
	public function incorrectGuesses(){
		return array_diff($this->guesses,str_split($this->word));
	}
	
	public function maxIncorrectGuesses(){
		$table = array('1'=>10,'2'=>5,'3'=>3);
		return $table[$this->level];
	}
	
	public function possibleGuesses(){
		$array = array();
		for($ord = 65;$ord<=90;$ord++){
			$array[] = chr($ord);
		}
		return $array;
	}
	
	public function word(){
		return $this->word;
	}
	
	/*
	**Only a couple options for now but 
	** reading from a dictionary file would be easy
	*/
	public function generateWord(){
		$words = array('component','callback','reuse','canvas','render');
		return strtoupper($words[array_rand($words)]);
	}
	
	public function validLevels(){
		return array(
					1=>'Easy game; you are allowed 10 misses.',
					2=>'Medium game; you are allowed 5 misses',
					3=>'Hard game; you are allowed 3 misses.');
	}
	
	public function isGameWon(){
		foreach(str_split($this->word) as $char){
			if(!in_array($char,$this->guesses))
				return false;
		}
		return true;
	}
	
	public function isGameLost(){
		if($this->maxIncorrectGuesses() == sizeof($this->incorrectGuesses())){
			return !$this->isGameWon();
		}
		return false;
	}
	
	public function guessCharacter($aChar){
		$this->guesses[] = $aChar;
		if($this->isGameWon()){
			$this->endGameWithMessage('You Won! The word was: '.$this->word);	
		}elseif($this->isGameLost()){
			$this->endGameWithMessage('You Lost! The word was:  '.$this->word);
		}
	}
	
	public function endGameWithMessage($message = ''){
		$this->callDialog(Object::construct('WHInformDialog')->
							setMessage($message)->
							onAnswerCallback($this,'newGame')
						);
		return $this;
		
	}
	
	public function giveUp(){
		$this->endGameWithMessage('The Word Was: '.$this->word);
	}
	
	public function newGameWithLevel($aLevel){
		$this->setLevel($aLevel);
		$this->guesses = array();
		$this->word = $this->generateWord();
		return $this;
	}
	
	public function newGame($forCompatibilityWithCallback = FALSE){
		$this->callDialog(Object::construct('WHChoiceDialog')->
							setMessage('Choose a difficulty level.')->
							setChoices($this->validLevels())->
							useRadio()->
							onAnswerCallback($this,'newGameWithLevel')
						);
		return $this;
	}
	
	public function renderContentOn($html){
		return $html->div()->class('hangman')->with(
				$html->headingLevel(1)->with('Please Make A Guess').
				$this->renderWordOn($html).
				$this->renderStatusOn($html).
				$this->renderGuessesOn($html).
				$this->renderOptionsOn($html)
			);
	}
	
	public function renderWordOn($html){
		$return = '';
		foreach(str_split($this->word) as $char){
			if(in_array($char,$this->guesses)){
				$return .= $char.$html->space();
			}else{
				$return .= '_'.$html->space();
			}
		}
		return $html->div()->class('hangman-word')->with($return);
	}
	
	public function renderStatusOn($html){
		return $html->div()->class('hangman-stats')->
						with('You have made '.sizeof($this->incorrectGuesses()).
							' bad guesses out of a maximum of '. 
							$this->maxIncorrectGuesses());
	}
	
	public function renderGuessesOn($html){
		$return = '';
		foreach($this->possibleGuesses() as $char){
			if(in_array($char,$this->guesses)){
				$return .= $html->span()->class('hangman-guessed')->
								with($char);
			}else{
				$return .= $html->anchor()->
									callback($this,'guessCharacter',array($char))->
									with($char);
			}
		}
		return $html->div()->class('hangman-guesses')->
						with('Guess: '.$return);
	}
	
	public function renderOptionsOn($html){
		return $html->div()->class('hangman-options')->with(
							$html->anchor()->callback($this,'giveUp')->
							with('Give Up?')
						);
	}
	
	public function style(){
		return '
			.hangman div{
				padding:5px;
			}
			.hangman-guesses span.hangman-guessed, .hangman-guesses a{
				padding:3px;
			}
		';
	}
	
}