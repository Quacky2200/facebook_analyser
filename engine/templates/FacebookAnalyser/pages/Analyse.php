<?php
class Analyse extends Page{
	public $user, $forceNew, $userExists;
	public function __construct(){
		$this->user = User::instance();
	}
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/analyse/";
	}
	public function getNewAnalysisURL(){
		return $this->getURL() . "new/";
	}
	public function isMatch($URL){
		//Check if we're forcing a new URL
		$forcingNewAnalysis = $URL == $this->getNewAnalysisURL();
		//Are they matching the URL?
		$isMatchingURL = $URL == $this->getURL() || $forcingNewAnalysis;
		//Are they logged in?
		$isLoggedIn = $this->user->isLoggedIn();
		if($isMatchingURL && $isLoggedIn){
			//Only start considering if it's a match here as we need to query the database after this.
			$dbh = Engine::getDatabase();
			//If this user exists in the database, they have used our application 
			//before and an analysis would have been created on authentication
			$this->userExists = ($dbh->query( "SELECT * FROM Users WHERE User_ID=" . $this->user->id)->fetch(PDO::FETCH_ASSOC) != null);
			if(!$forcingNewAnalysis && $this->userExists){
				ob_clean();
				header("Location: " . (New Account())->getURL());
				exit();
			}
			//Otherwise, we are a new user and we don't need to force a new analysis
			return true;
		} else if ($isMatchingURL && !$isLoggedIn) {
			//Go back home as we're not authenticated.
			require('login.php');
		} else {
			//Wasn't a match at all.
			return false;
		}
	}

	public function run($template){
		require("login.php");
	}
	public function show($template){
		ob_implicit_flush(true);
		include("section/header.php");
		include("section/middle_analyse.php");
		for($k = 0; $k < 40000; $k++) echo ' ';
		//for now we are putting this in another class as 
		//there is a lot of work that has to be done whilst 
		//page is loading
		require(__DIR__ . "/../AnalysisWorker.php");
		$work = new AnalysisWorker();
		$work->run();
	}
}
?>