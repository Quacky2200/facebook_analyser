<?php
abstract class Template{
	//error500Page only executed on Page code errors!
	public $error404Page, $error500Page;
	private $CSS, $JS;
	private $pages;
	public function __construct(){
		$this->pages = $this->getPages();
		$this->checkAllPageAuthenticity();
		$this->CSS = array();
		$this->JS = array();
	}
	private function checkAllPageAuthenticity(){
		$i = 0;
		$count = count($this->pages);
		if(!is_null($this->pages) && $this->pages !== false){
			foreach($this->pages as $Page){
				if(!($Page instanceof Page)){
					throw new Exception("Page $i of $count is NOT a Page class.");
					exit();
				}
				$i++;
			}
		} else {
			throw new Exception("No pages available");
		}
	}
	public final function addCSS($filename){
		return array_push($this->CSS, $filename);
	}
	public final function addJS(){
		return array_push($this->JS, $filename);
	}
	public final function addMultipleCSS($Multiple){
		$this->CSS = array_unique(array_merge($this->CSS, $Multiple));
	}
	public final function addMultipleJS($Multiple){
		$this->JS = array_unique(array_merge($this->JS, $Multiple));;
	}
	public abstract function getPages();
	public abstract function getKudos();
	public abstract function getName();
	//Return array of CSS and JS Dependencies
	public final function getCSSAndJSDependencies($appendTemplateDefaultPublicDirectory = true){
		$values = array();
		foreach($this->CSS as $value){
			$url = ($appendTemplateDefaultPublicDirectory ? $this->getLocalDir() . '/public/' . $value : $value);
			array_push($values, "<link href='" . Engine::getRemoteDir($url) . "' rel='stylesheet' type='type/css'/>");
		}
		foreach($this->JS as $value){
			$url = ($appendTemplateDefaultPublicDirectory ? $this->getLocalDir() . '/public/' . $value : $value);
			array_push($values, "<script src='" . Engine::getRemoteDir($url) . "'></script>");
		}
		return $values;
	}
	//Echo out the CSS and JS Dependencies
	public final function echoCSSAndJSDependencies(){
		echo implode("\n", $this->getCSSAndJSDependencies());
	}
	public final function getLocalDir(){
		//Returns the local directory for the page
		$reflectionClass = new ReflectionClass(get_class($this));
		return dirname($reflectionClass->getFileName());
	}
	private function callPage($URL, $Page){
		if(!is_null($Page)){
			$Page->setURL($URL);
			$Page->run($this);
			$Page->show($this);
		} else {
			throw new Exception("Selected page was null.");
		}
	}
	public final function traverse($URL){
		if (!is_null($this->pages) and $this->pages !== false){
			foreach($this->pages as $Page){
				if(preg_match($Page->getURLRegex(), $URL)){
					try{
						$this->callPage($URL, $Page);
						exit();
					} catch (Exception $e){
						if(!is_null($this->error500Page) && $this->error500Page instanceof Page){
							$this->callPage($URL, $this->error500Page);
						}
						else{
							ErrorHandler::primitiveError(500, "Page script error", $e->getMessage());
						}
					}
				}
			}
		}
		if(!is_null($this->error404Page) && $this->error404Page instanceof Page){
			$this->callPage($URL, $Page);
		}
		else{
			ErrorHandler::primitiveError(404, "Page not found", "Cannot find any pages with this URL.");
		}

	}
}
?>