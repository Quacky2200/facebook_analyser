<?php
class TemplateSetup extends SetupChapter{
	public function __construct(){
		parent::__construct("engine-template-chapter", "Template Time", "Select the template to use for this installation");
	}
	public function onLoad(){
		//If the template is from one of the templates then ignore
		if($this->testTemplate(basename(dirname(Engine::getConfig()->TEMPLATE)))) {
			$this->setEnabled(false);
		}
	}
	private function getTemplates(){
		$templates = Engine::getTemplates();
		$me = realpath(__DIR__ . "/../main.php");
		$getKey = array_search($me, $templates);
		unset($templates[$getKey]);	
		return $templates;
	}
	private function testTemplate($templateName){
		return Engine::getTemplate($templateName) !== null;
	}
	private function getTemplateOptions(){
		$templates = $this->getTemplates();
		$elements = array(new Element("option", array(""), "Select a Template"));
		if(is_array($templates)){
			foreach($templates as $template){
				$name = basename(dirname($template));
				array_push($elements, new Element("option", array("name="=>$this->addName("testing"), "value"=>$name), $name));
			}
			return $elements;
		}
		return null;
	}
	public function getElements(){
		return array(
			new Element("p", array("class"=>"input error " . $this->addName("error")), "Cannot select this Template. Please select another"),
			//new Element("h3", null, "Select Template"),
			new Element("select", array("name"=>$this->addName("testing")), $this->getTemplateOptions()),
		);
	}
	public function onSubmit(){
		var_dump($_POST[$this->addName("testing")]);
		die();
		// $templateName = $_POST[$this->addName("option")];
		// if($this->testTemplate($templateName) === true){
		// 	Engine::getConfig()->TEMPLATE = $templateName;
		// 	$this->sendSuccess();
		// } else {
		// 	$this->sendFail(array($this->addName("error")));
		// }
	}
}
?>