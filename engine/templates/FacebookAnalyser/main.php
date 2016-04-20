<?php
require("SDK.php");
require("models/User.php");
require("models/Result.php");
require("pages/Home.php");
require("pages/Account.php");
require("pages/Analyse.php");
require("pages/Results.php");
require("pages/Error404.php");
require("pages/Error500.php");

class FacebookAnalyser extends Template{
	public function getName(){
		return __CLASS__;
	}
	public function __construct(){
		parent::__construct(); 
		ErrorHandler::setErrorHTML((new Error500)->show($this));
	}
	public function configure($setup){
		try{
			$dbh = Engine::getDatabase();
			//When we create these tables, we assume they're not already created, if they are, 
			//we delete them and start anew as it's easier than just ignoring and potentially 
			//not having these relationships with the data
			//Delete all tables
			$dbh->exec("DROP TABLE IF EXISTS Result_History; DROP TABLE IF EXISTS Results;DROP TABLE IF EXISTS Users;");
			//Setup all tables
			$dbh->exec("CREATE TABLE Results (Result_ID VARCHAR(64) NOT NULL, Date datetime NOT NULL, Data text NOT NULL, PRIMARY KEY (Result_ID), KEY (Result_ID)) ENGINE=InnoDB DEFAULT CHARSET=latin1");
			$dbh->exec("CREATE TABLE Users ( User_ID VARCHAR(64) NOT NULL, Name text NOT NULL, Email text NOT NULL, PRIMARY KEY (User_ID), KEY (User_ID)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
			$dbh->exec("CREATE TABLE Result_History ( History_ID int(32) NOT NULL, User_ID VARCHAR(64) NOT NULL, Result_ID VARCHAR(64) NOT NULL, PRIMARY KEY (History_ID)) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
			$dbh->exec("ALTER TABLE Result_History ADD UNIQUE (Result_ID);");
			//create relationships between tables
			$dbh->exec("ALTER TABLE Result_History ADD CONSTRAINT FOREIGN KEY (User_ID) REFERENCES Users(User_ID) ON DELETE CASCADE;ALTER TABLE Result_History ADD CONSTRAINT FOREIGN KEY (User_ID) REFERENCES Users(User_ID) ON DELETE CASCADE");
			$dbh->exec("ALTER TABLE Result_History ADD FOREIGN KEY (Result_ID) REFERENCES Results(Result_ID) ON DELETE CASCADE ON UPDATE RESTRICT;");
			//Make sure we increment history
			$dbh->exec("ALTER TABLE Result_History MODIFY History_ID int(32) NOT NULL AUTO_INCREMENT;");
		} catch(PDOException $e){
			$setup->sendStatus(true, array($setup->addName("template-config-error"), "error_message"=>$e->getMessage()));
		}	
	}
	public function getPages(){
		return array(
			new Home(),
			new Account(),
			new Analyse(),
			new Results(),
			new Error404()
		);
	}
}
return new FacebookAnalyser();
?>