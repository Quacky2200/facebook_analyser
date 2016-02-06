<?php
class Home extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/";
	}
	public function isMatch($URL){
		return $URL == $this->getURL();
		//return preg_match("/^[" . $this->getURL() . "]$/", $URL);
	}
	public $loginButton;
	public function run($template){
		$authenticatePageURL = Engine::getRemoteAbsolutePath((new Analyse())->getURL());
		$FacebookAuthenticationURL = User::instance()->getFacebookAuthURL($authenticatePageURL);
		$title = "<b>Login</b> with <b>Facebok</b>";
		if(User::instance()->isLoggedIn()){
			$FacebookAuthenticationURL = $authenticatePageURL;
			$title = "<b>Continue</b> as " . User::instance()->name . "<b></b>";
		}
		$this->loginButton = "<a class=\"fblogin\" href=\"$FacebookAuthenticationURL\"><i class=\"fa fa-facebook\"></i><span>$title</span></a>";
	}
	public function show($template){
		include(__DIR__ . '/views/header.php');
		include(__DIR__ . '/views/home.php');
		include(__DIR__ . '/views/footer.php');
	}
}
?>