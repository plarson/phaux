<?php
/**
 * @package Phaux-test
 */
class TicTacPhaux extends WHComponent {

    private $board = array();
    private $lastMove = 'X';

    function __construct() {
       $this->newGame();
	   $this->session()->registerObjectOnKeypath($this,"board");
		$this->session()->registerObjectOnKeypath($this,"lastMove");
    }
	function newGame(){
		$this->board = array('', '', '', '', '', '', '', '', '');
		$this->lastMove = 'X';
	}
	
	public function board(){
		return $this->board;
	}
	public function setBoard($anArray){
		$this->board = $anArray;
		return $this;
	}
	public function lastMove(){
		return $this->lastMove;
	}
	public function setLastMove($xOrO){
		$this->lastMove = $xOrO;
		return $this;
	}
	
	
    function claim($pos, $token) {
        $cell = $this->peek($pos);
        if (empty($cell)) {
            $this->board[$pos] = $token;
			if ($this->win()){
				$this->showWinDialog();
			}
			if($this->isDraw()){
				$this->showDrawDialog();
			}
			$this->lastMove = ($this->lastMove == 'X') ? 'O' : 'X';
            return true;
        } else {
            return false;
        }
    }

	function showWinDialog(){
		$this->callDialog(
					Object::construct("WHInformDialog")->
					onAnswerCallback($this,"winDialogCallback")->
					setMessage($this->lastMove." has won this round.")	
				);
	}
	
	function showDrawDialog(){
		$this->callDialog(
					Object::construct("WHInformDialog")->
					onAnswerCallback($this,"winDialogCallback")->
					setMessage("The game ends in a draw.")	
				);
	}
	
	function winDialogCallback($aBool){
		$this->newGame();
	}

    function peek($pos) {
	    return $this->board[$pos];
    }

    function diagonalWin() {
        if ((!empty($this->board[0]) && ($this->board[0] == $this->board[4]) && ($this->board[4] == $this->board[8])) ||
            (!empty($this->board[6]) && ($this->board[6] == $this->board[4]) && ($this->board[4] == $this->board[2]))) {
            return true;
        } else {
            return false;
        }
    }

    function horizontalWin() {
        if ((!empty($this->board[0]) && ($this->board[0] == $this->board[1]) && ($this->board[1] == $this->board[2])) ||
            (!empty($this->board[3]) && ($this->board[3] == $this->board[4]) && ($this->board[4] == $this->board[5])) ||
            (!empty($this->board[6]) && ($this->board[6] == $this->board[7]) && ($this->board[7] == $this->board[8]))) {
            return true;
        } else {
            return false;
        }
    }

    function verticalWin() {
        if ((!empty($this->board[0]) && ($this->board[0] == $this->board[3]) && ($this->board[3] == $this->board[6])) ||
            (!empty($this->board[1]) && ($this->board[1] == $this->board[4]) && ($this->board[4] == $this->board[7])) ||
            (!empty($this->board[2]) && ($this->board[2] == $this->board[5]) && ($this->board[5] == $this->board[8]))) {
            return true;
        } else {
            return false;
        }
    }

    function win() {
        return $this->diagonalWin() || $this->horizontalWin() || $this->verticalWin();
    }

	public function isDraw(){
		foreach($this->board as $space){
			if($space == ''){
				return false;
			}
		}
		return true;
	}

	public function renderHeadingOn($html){
		return $html->headingLevel(1)->with($this->lastMove." make your move");
		
	}

	public function renderGameSpaceOn($html,$position){
		if($this->peek($position) == ''){
			return $html->anchor()->callback($this, "claim", array($position, $this->lastMove))->with("click");
		}else{
			return $html->text($this->peek($position));
		}
	}

	public function renderGameBoardOn($html){
		return $html->table()->class("gameboard")->
				with(
					$html->tableRow()->with(
						$html->tableData($this->renderGameSpaceOn($html,0)).
						$html->tableData($this->renderGameSpaceOn($html,1)).
						$html->tableData($this->renderGameSpaceOn($html,2))
					).
					$html->tableRow()->with(
						$html->tableData($this->renderGameSpaceOn($html,3)).
						$html->tableData($this->renderGameSpaceOn($html,4)).
						$html->tableData($this->renderGameSpaceOn($html,5))
					).
					$html->tableRow()->with(
						$html->tableData($this->renderGameSpaceOn($html,6)).
						$html->tableData($this->renderGameSpaceOn($html,7)).
						$html->tableData($this->renderGameSpaceOn($html,8))
					)
				);
	}


	public function renderContentOn($html){
       	return $this->renderHeadingOn($html) .
				$this->renderGameBoardOn($html);
	}
	
	public function updateRoot($anHtmlRoot){
		parent::updateRoot($anHtmlRoot);
		$anHtmlRoot->setTitle("Tic-Tac-Phaux");
	}
	
	public function style(){
		return "
			.gameboard{
				width:100px;
			}
			.gameboard td{
				padding:30px;
			}
		";
	}
}
