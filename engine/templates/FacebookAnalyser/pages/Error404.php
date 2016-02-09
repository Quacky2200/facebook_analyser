<?php
class Error404 extends Page{
	public function getName(){
		return "Uh-Oh!";
	}
	public function getURL(){
		return null;
	}
	public function isMatch($URL){
		return true;
	}
	public $homeButton;
	public function run($template){
		$homeURL = Engine::getRemoteAbsolutePath((new Home())->getURL());
		$this->homeButton = "<a href=\"$homeURL\">Take me home, please.</a>";
	}
	public function show($template){
		include("section/header.php");
		include("section/middle_error404.php");
		include("section/footer.php");
	}
}
return new Error404();

?>