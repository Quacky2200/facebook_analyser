<?php
class Analyse extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/analyse/";
	}
	public function isMatch($URL){
		return $URL == $this->getURL();
	}
	public $user;
	public function run($template){
		$this->user = User::instance();
		if($this->user->isLoggedIn()){
			if(!$this->user->loadProfile()){
				//Couldn't load profile :O Whaaaaaaat??? :S
				throw new Exception("Could not load the Facebook profile URL");
			}
		} else {
			header("Location: " . Engine::getRemoteAbsolutePath($this->user->getFacebookAuthURL($this->getURL())));
			exit();
		}
	}
	public function show($template){
		include(__DIR__ . '/views/header.php');
		echo "<h1>Start trying to do things with FB and the analysis part...</h1>";
		echo "User's first name: " . $this->user->name;
		//echo "<br/>User's birth date: " . $this->user->birthday;
		//Add lots more to list, then to check against etc :)
		//echo "User's educational venue: " . $this->user->education['school']['name'];
		//echo "User's educational vocation: " . $this->user->education['concentration']['name'];
		include(__DIR__ . '/views/footer.php');
	}
}
?>