<?php 
abstract class Page{
	//The name of the page
	public abstract function getName();
	//What the URL will match against
	public abstract function getURLRegex();
	private $URL = '';
	public final function getURL(){
		//Return the matched URL
		return $URL;
	}
	public final function setURL($URL){
		//Set the matched URL
		$this->URL = $URL;
	}
	public function getGeneratedTitle($Template, $includeElement = false){
		//Generate a nice Title for our page
		$startElement = ($includeElement ? "<title>" : "");
		$endElement = ($includeElement ? "</title>" : "");
		return $startElement . $this->getName() . " - " . $Template->getName() . $endElement;
	}
	//Run the code for the page
	public abstract function run($Template);
	//Start showing the page details
	public abstract function show($Template);
}
?>