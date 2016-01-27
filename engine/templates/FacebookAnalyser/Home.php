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
	public function run($template){
		//echo $this->getName() . "->(Run code)";
	}
	public function show($template){
		//echo "<br/>" . $this->getName() , "->(Show template)";
		include(__DIR__ . '/views/header.php');
		include(__DIR__ . '/views/home.php');
		include(__DIR__ . '/views/footer.php');
	}
}
?>