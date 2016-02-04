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
	public function run($template){}
	public function show($template){
		include(__DIR__ . '/views/header.php');
		echo "<h1 align='center'>This page doesn't exist</h1><p align='center'>Sorry about that...</p>";
		include(__DIR__ . '/views/footer.php');
	}
}

?>