<?php 
abstract class Page{
	//The name of the page
	public abstract function getName();
	//Get the official URL for the page
	public abstract function getURL();
	//Let the derived class determine if they match the URL by returning a true/false
	public abstract function isMatch($URL);
	// private $URL = '';
	// public final function getURL(){
	// 	//Return the matched URL
	// 	return $this->URL;
	// }
	// public final function setURL($URL){
	// 	//Set the matched URL
	// 	$this->URL = "http://" . $_SERVER['HTTP_HOST'] . Engine::getRemoteDir($URL);
	// }
	public function getGeneratedTitle($template, $includeElement = false){
		//Generate a nice Title for our page
		$startElement = ($includeElement ? "<title>" : "");
		$endElement = ($includeElement ? "</title>" : "");
		return $startElement . $this->getName() . " - " . $template->getName() . $endElement;
	}
	//Run the code for the page
	public abstract function run($template);
	//Start showing the page details
	public abstract function show($template);
}
?>