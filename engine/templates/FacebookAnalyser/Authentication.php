<?php
class Authentication extends Page{
	public function getName(){
		return "Login";
	}
	public function getURLRegex(){
		return "~^\/(authenticate)[\/]?$~";
	}
	public function run($Template){
		echo $this->getName() . "->(Run code)";
	}
	public function show($Template){
		echo "<br/>" . $this->getName() . "->(Show template)";
	}
}
?>