<?php
class Error500 extends Page{
	public function getName(){
		return "Oh My!";
	}
	public function getURL(){
		return null;
	}
	public function isMatch($URL){
		return false;
	}
	public function run($template){}

	public function show($template){
		ob_start();
		include("section/header.php");
		include("section/middle_error500.php");
		include("section/footer.php");
		$output = ob_get_contents();
		ob_get_clean();
		return $output;
	}
}

