<?php
class Home extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURLRegex(){
		return "~^\/(home|index)?[\/]?$~";
	}
	public function run($Template){
		echo $this->getName() . "->(Run code)";
	}
	public function show($Template){
		echo "<br/>" . $this->getName() , "->(Show template)";
	}
}
?>