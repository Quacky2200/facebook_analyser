<?php
require('Analyse.php');
class Authentication extends Page{
	public function getName(){
		return "Authentication";
	}
	public function getURL(){
		return "/authenticate/";
	}
	public function isMatch($URL){
		return $URL == $this->getURL();
	}
	private $user;
	public function run($Template){
		$this->user = User::instance();
		if(!$this->user->isLoggedIn()){
			header("Location: " . Engine::getRemoteAbsolutePath((new Analyse())->getURL()));
			exit();
		}
		//$this->user->getAuthenticationPage($this->getURL());
		//echo $this->getName() . "->(Run code)";
	}
	public function show($Template){
		echo "<br/>" . $this->getName() . "->(Show template)";
	}
	public function getAuthenticationPage(){
		//Return the right page depending if we are logged in or not
		return ($this->isLoggedIn() ? new Analyse() : new Authentication());
	}
}
?>