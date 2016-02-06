<?php
class Results extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/results/";
	}
	public function isMatch($URL){
		return $URL == $this->getURL();
	}
	public $user;
	public function run($template){
		$this->user = User::instance();
		if(!$this->user->isLoggedIn()){
			//Redirect back to the login page
			ob_clean();
			header("Location: " . Engine::getRemoteAbsolutePath((new Home())->getURL()));
		}
	}
	public function show($template){
		echo "Results are here: <br/>BLAH!!!!!!!";
		$this->user->getUserInteraction();
		//echo "User's first name: " . $this->user->name;
		//$this->getAll();
		//echo "<br/>User's birth date: " . $this->user->birthday;
		//Add lots more to list, then to check against etc :)
		//echo "User's educational venue: " . $this->user->education['school']['name'];
		//echo "User's educational vocation: " . $this->user->education['concentration']['name'];
		//var_dump($this->user->getAll());
		//include(__DIR__ . '/views/footer.php');
	}
}

?>