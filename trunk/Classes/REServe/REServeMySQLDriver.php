<?php
/**
 * @package REServe
 */
class REServeMySQLDriver extends REServeDriver {
	protected $connection;
	protected $debugOutputFile = NULL;
	//protected $debugOutputFile = '/tmp/reservedebug.txt';
	public function classForOid($anOid){
		$oc = $this->getFromCache($anOid);
		if($oc == NULL){
			$result = $this->executeQuery($this->queryForClassForOid($anOid));
			$array = mysql_fetch_array($result);
			$classString = $array['type'];
			return $classString;
		}
		return $oc->getClass();
	}
	
	public function close(){
		mysql_close($this->connection);
		$this->connection = NULL;
		return $this;
	}
	
	
	public function escapedColumnName($aString){
		return '`'.$aString.'`';
	}
	public function escapedValue($aString){
		return mysql_real_escape_string($aString,$this->connection);
	}
	
	public function lastOidFromTable($aTable){
		/*Mysql will only ever return the last ID
		** Postgress needs the table name
		*/
		return mysql_insert_id($this->connection);
	}
	
	
	
	public function connect($aHost,$aUser,$aPassword,$aDatabase){
		$this->connection = mysql_connect($aHost,$aUser,$aPassword);
		if($this->connection == FALSE){
			throw new WHException("Failed to connect to the MySQL databtase");
		}
		if(!mysql_select_db($aDatabase,$this->connection)){
			throw new WHException("Failed to select $aDatabase database");
		}
		return $this;
	}
	
	
	
	public function executeQuery($sql){
		if($this->debugOutputFile != NULL){
			file_put_contents($this->debugOutputFile,$sql."\n",FILE_APPEND);
		}
		if(!is_resource($this->connection)){
			$this->error('Connection is not valid');
		}
	 	$return = mysql_query($sql,$this->connection);
	
		if($return === FALSE){
			/*
			** For some reason the following on my MySQL instlation
			** returns NOTHING
			*/
			//mysql_errno($this->connection);
			/*
			** So I do the following it is more than a bit of 
			** a hack.
			** Does someone know a better way ?
			*/
			//die(mysql_error());
			//if(strpos(mysql_error(),"column")){
				throw new WHException(mysql_error(),666);
			//}else{
				
			//	throw new WHException(mysql_error(),667);
			//}
		}
		//die("HERE");
		return $return;
	}
	
	/*
	** Returns a multidimensional array
	** of results produced from $sql
	*/
	public function executeQueryFetchArray($sql){
		$result = $this->executeQuery($sql);
		$return = array();
		while($array = mysql_fetch_assoc($result)){
			$return[] = $array;
		}
		return $return;
	}
	
	/*
	** Returns a array of results from the query
	** Used by REQuery
	** DON'T USE DIRECTLY UNLESS YOU KNOW WHAT YOU ARE DOING
	*/
	public function resultsFromQueryWithClass($aQuery,$aClass){
		$arrayToReturn = array();
		//echo $aQuery;
		$results = $this->executeQuery($aQuery);
		while($ar = mysql_fetch_array($results)){
			foreach(Object::construct($aClass)->tableDefinition()->columns() as $column){
				$this->currentColumn($column);
				if($column->type()->reServeValueStoredWithObject()){
					$dRow[$column->keyPath()] = $ar[$column->name()];
				}
			}
			
			$arrayToReturn[] = $this->objectForOidWithClassFromArray(
									$dRow['oid'],
									$aClass,$dRow);

		}
		return $arrayToReturn;
	}
	
	public function connection(){
		return $this->connection;
	}
	
	public function queryToCreateTableWithObject($anObject){
		return parent::queryToCreateTableWithObject($anObject). ' ENGINE=InnoDB';
	}
	
	public function queryToCreateObjectIdTable(){
		return "CREATE TABLE pxxObjectLookup (
					objectId int(11) auto_increment  PRIMARY KEY ,
					`type` VARCHAR(254) NOT NULL,
					`root` TINYINT(1) NOT NULL DEFAULT 0 )
					ENGINE=InnoDB";
	}
	
	public function collectionWithOid($model,$anOid){
		/*
		**Copy and paste hacky
		** we need a migrate class method
		*/
		try{
			$result = $this->executeQuery($this->queryForLookupCollectionWithOid($model,$anOid));
		}catch (WHException $e){
			try{
				if($e->getCode() == 666){
					$this->updateTableForObject($model->parentObject());
					$result = $this->executeQuery($this->queryForLookupCollectionWithOid($model,$anOid));
				}else{
					throw $e;
				}
			}catch(Exception $e){
				//Woops something else is wrong throw it
				throw $e;
			}
		}
		
		$collection = array();
		while($array = mysql_fetch_array($result)){
			$row = array();
			foreach($model->tableDefinition()->columns() as $column){
				if($column->valueStored()){
					$row[$column->keyPath()] = $array[$column->name()];
				}
			}
			$collection[] = $row;
		}
		return $collection;
	}
	
	public function objectForOidWithClass($anOid,$aClass){
		if($this->getFromCache($anOid) != NULL &&
				$this->getFromCache($anOid)->object() != NULL){
			return $this->getFromCache($anOid);
		}else{
			$result = $this->executeQuery($this->queryForLookupClassWithOid($aClass,$anOid));
			$array = mysql_fetch_array($result);
			foreach(Object::construct($aClass)->tableDefinition()->columns() as $column){
				$this->currentColumn($column);
				if($column->type()->reServeValueStoredWithObject()){
					$dRow[$column->keyPath()] = $array[$column->name()];
				}
			}
			return $this->objectForOidWithClassFromArray($anOid,$aClass,$dRow);
		}
	}
	
	
	
	public function oidForRoot (){
		try{
			$result = $this->executeQuery($this->queryForRootLookup());
			
		}catch(WHException $e){
			$this->setupDatabase();
			$result = $this->executeQuery($this->queryForRootLookup());
			
		}
		$array = mysql_fetch_array($result);
	
		return $array["objectId"]; 
	}
	
	public function queryClass(){
		return "REQuery";
	}
	
	public function oidColumn(){
		return "INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT";
	}
	
	public function integer(){
		return "INT(11)";
	}
}