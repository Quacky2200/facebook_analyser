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
			if (!$this->userExists){
				$dbh->exec("INSERT INTO Users (User_ID, Name, Email) VALUES ('" . User::instance()->id . "', '" . User::instance()->name . "', '" . User::instance()->email . "')");
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
		//Make sure we are logged in
		require("login.php");
	}
	public function show($template){
		//We're going to start Asyncronous work, prevent any output unless we flush it whilst we are running asyncronously
		ob_implicit_flush(true);
		include("section/header.php");
		include("section/middle_analyse.php");
		//Flush the header and analyse section to the user
		for($k = 0; $k < 40000; $k++) echo ' ';
		//Run the asyncronous work with this class.
		require(__DIR__ . "/../AsyncAnalysisWorker.php");
		$work = new AsyncAnalysisWorker();
		//Let's go and run our analysis.
		$work->run();
	}
}
?>