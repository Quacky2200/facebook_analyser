<?php
class DatabaseSetup extends SetupChapter{
	public function __construct(){
		parent::__construct("engine-database-chapter", "Let's Start", "These database details are required to allow the engine to store information (e.g. pages, settings, users). If you do not know these details, your hosting provider will be able to provide you with them.");
	}
	public function onLoad(){
		Engine::getConfig()->setConfigFilename(TEMP_CONFIG_FILE);
		Engine::getConfig()->open();
		//If it has been done already & we can connect, ignore.
		if($this->try_connect(Engine::getConfig()->DB_HOST, Engine::getConfig()->DB_USERNAME, Engine::getConfig()->DB_PASSWORD, Engine::getConfig()->DB_NAME) === true){
			$this->setEnabled(false);
		}
	}
	public function getElements(){
		return array(
			new Element("p", array("class"=>"input error " . $this->addName("error")), "Cannot connect to the database, make sure the details are correct."),
			new Element("h3", null, "Database Host"),
			new Element("input", array("name"=>$this->addName("host"), "placeholder"=>"localhost", "value"=>Engine::getConfig()->DB_HOST ?: "")),
			new Element("h3", null, "Database Username"),
			new Element("input", array("name"=>$this->addName("username"), "placeholder"=>"root", "value"=>Engine::getConfig()->DB_USERNAME ?: "")),
			new Element("h3", null, "Database Password"),
			new Element("input", array("name"=>$this->addName("password"), "type"=>"password", "placeholder"=>"(your database password)", "value"=>Engine::getConfig()->DB_PASSWORD ?: "")),
			new Element("h3", null, "Database Name"),
			new Element("input", array("name"=>$this->addName("dbname"), "placeholder"=>"(e.g. Engine)", "value"=>Engine::getConfig()->DB_NAME ?: ""))
		);
	}
	private function try_connect($HOST, $UNAME, $PASS, $DBNAME){
		try{
			if(strlen($HOST) > 3 && strlen($UNAME) > 2 && strlen($DBNAME) > 2){
				DBConnection::ConnectToDB($HOST, $UNAME, $PASS, $DBNAME);
				return true;
			} else {	
				return false;
			}
		} catch (Exception $e){
			return false;
		}
	}
	public function onSubmit(){
		$host = $_POST[$this->addName("host")];
		$username = $_POST[$this->addName("username")];
		$password = $_POST[$this->addName("password")];
		$name = $_POST[$this->addName("dbname")];
		if($this->try_connect($host, $username, $password, $name) === true){
			Engine::getConfig()->DB_HOST = $host;
			Engine::getConfig()->DB_USERNAME = $username;
			Engine::getConfig()->DB_PASSWORD = $password;
			Engine::getConfig()->DB_NAME = $name;
			Engine::getConfig()->setConfigFilename(TEMP_CONFIG_FILE);
			Engine::getConfig()->save(true);
		} else {
			$this->sendStatus(true, array($this->addName("error")));
		}

	}
}
?>