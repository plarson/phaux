<?php
/**
 * @package REServe-Phaux
 */
class WHREServeTest extends WHComponent {
	protected $currentContactComponent;
	protected $contactList;
	public function __construct(){
		if($this->session()->db()->root() == NULL){
			$rootObject = Object::construct("WHREServeRootModel");
			$this->session()->db()->setRoot($rootObject);
		}
		$this->contactList = Object::construct("WHREServeList")->
								setReObjects($this->session()->db()->root()->contacts());
		$this->contactList->callbackForSelect($this,"setCurrentContact");
		$this->contactList->callbackForRemove($this,"removeContact");						
								
	}
	
	public function addContact(){
		$newContact = Object::construct("WHREServeContactModel")->
						setName("William Harford")->
						setEmail("foo@bar.com")->
						setPhonenumber("416.666.6666")->
						setNiceness(5);
		$this->session()->db()->root()->addContact($newContact);
		//Update the contact list
		$this->contactList->setReObjects($this->session()->db()->root()->contacts());
		$this->contactList->setCurrentObject($newContact);
		return $this;
	}
	
	public function removeContact($aContact){
		$this->session()->db()->root()->removeContact($aContact);
		$this->contactList->setReObjects($this->session()->db()->root()->contacts());
		$this->session()->db()->deleteObject($aContact);
	}
	
	public function setCurrentContact($aContact){
		$this->currentContactComponent = Object::construct("WHREServeModelEdit")->
						setReserveable($aContact);
		return $this;
	}
	
	public function flushTest(){
		$this->addContact();
		$contacts = $this->session()->db()->root()->contacts();
		$contact = $contacts[sizeof($this->session()->db()->root()->contacts()) - 1];
		$contact->setName('Flush Test');
		$this->session()->db()->commitAndStart();
		$this->session()->db()->flush();
		$contactAgain = $contacts[sizeof($this->session()->db()->root()->contacts()) - 1];
		if($contact !== $contactAgain){
			$this->error((string)$contact. ' is not the same as '.(string)$contactAgain);
		}
		if($contact->name() != 'Flush Test'){
			$this->error($contact->name() . ' should be "Flush Test"');
		}
		/*
		**Try to commit after a flush
		*/
		$this->session()->db()->commitAndStart();
	}
	
	public function renderFlushTestOn($html){
		return $html->anchor()->callback($this,'flushTest')->with('Flush Test');
	}
	
	public function renderContentOn($html){
		$return .= $this->renderFlushTestOn($html);
		$return .= $html->render($this->contactList);
		$return .= $html->anchor()->callback($this,"addContact")->with("Add New Contact");
		if(is_object($this->currentContactComponent)){
			$return .= $html->render($this->currentContactComponent);
		}
		
		return $return;
		
	}
	
	public function children(){
		$ra = array();
		$ra[] = $this->contactList;
		if($this->currentContactComponent != NULL){
			$ra[] = $this->currentContactComponent;
		}
		return $ra;
	}
	
}