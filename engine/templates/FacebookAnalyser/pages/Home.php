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
	}
	public $loginButton;
	public function run($template){
		$authenticatePageURL = Engine::getRemoteAbsolutePath((new Analyse())->getURL());
		$FacebookAuthenticationURL = User::instance()->getFacebookAuthURL($authenticatePageURL);
		$title = "<b>Login</b> with <b>Facebook</b>";
		if(User::instance()->isLoggedIn()){
			$FacebookAuthenticationURL = $authenticatePageURL;
			$title = "<b>Continue</b> as " . User::instance()->name . "<b></b>";
		}
		$this->loginButton = "<a class=\"fblogin\" style='margin: 0.5em auto;' href=\"$FacebookAuthenticationURL\"><i class=\"fa fa-facebook\"></i><span>$title</span></a>";
	}
	public function show($template){
		include("section/header.php");
		include("section/middle_home.php");
		include("section/footer.php");
	}
}
?>