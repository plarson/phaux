<?php
/**
 * @package REServe-Phaux
 */
class WHREServeSession extends WHSession {
	protected $db = NULL;
	
	public function start(){
		parent::start();
		$this->db = Object::construct($this->
				configuration()->
				configValueBySubjectAndKey("REServe","type"));
		$this->db->setAutomaticTableCreation(
				$this->configuration()->
					configValueBySubjectAndKey("REServe","automatic_table_creation"));
		
		$this->connect();
		return $this;
	}
	
	public function resume(){
		$this->connect();
		return $this;
	}
	
	
	public function db(){
		if(!$this->isConnected()){
			$this->connect();
		}
		return $this->db;
	}
	
	public function connect(){
		$values = array("host","type","user","password","database","port","automatic_table_creation");
		foreach($values as $value){
			$$value = $this->
						configuration()->
						configValueBySubjectAndKey("REServe",$value);
		}										
		$host = $host.":".$port;
		
		$this->db->connect($host,$user,$password,$database);
		$this->db->startTransaction();
	}
	
	public function isConnected(){
		if($this->db->isConnected()){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/*
	**You most likely want to set the
	** Root object to something meaningfull 
	** with $this->db()->setRoot($anObject)
	*/
	public function storage(){
		return $this->db()->root();
	}
	
	/*
	** DON'T USE __sleep
	*/
	public function save(){
		$this->db()->commit();
		/*
		** Flushing after the render step causes problems
		** object in arrays (they don't get saved when they change)
		** I thing it has to do with arrays are referenced but I can't 
		** find the problem. Please FIXME
		*/
		if(!$this->isRenderStep()){
			$this->db()->flush();
		}
		$this->db()->close();
		return parent::save();
	}

	/*
	** Can't do this because we don't have 
	** the configuration yet
	** we could store the connection options 
	** like user name, password and such but 
	** instead we will just to lazy initialization
	** with db() 
	** this has the added bennifit of never opening
	** a connection with database if it is never used
	*/
	public function __wakeup(){
		//$this->db->connect();
	}
	
}