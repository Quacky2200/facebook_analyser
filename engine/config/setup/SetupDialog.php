<?php
DEFINE('TEMP_CONFIG_FILE', dirname(__FILE__) . '/temp-config-data.json');
class SetupDialog extends Page{
	public function getName(){
		return "Welcome";
	}
	public function getURLRegex(){
		return "/^(.*)$/";
	}
	public function run($Template){
		if(file_exists(TEMP_CONFIG_FILE)){
			Engine::getConfig()->setConfigFilename(TEMP_CONFIG_FILE);
			Engine::getConfig()->open();
		}
	}
	public function show($Template){
		require('views/header.php');
		echo "
			<form method='POST'>
				<slides>";
		echo $this->getAllChaptersHTML();
		echo "  </slides>
			</form>";
		require('views/footer.php');
	}
	private $allChapters;
	public function __construct($allChapters){
		$this->allChapters = &$allChapters;
		//var_dump($this->allChapters[0]);
		//var_dump($this->setupChapters);
	}
	private function getAllChaptersHTML(){
		if(!is_null($this->allChapters)){
			$setActive = 0;
			foreach($this->allChapters as $key=>$chapter){
				if($chapter->isEnabled()){
					$chapter->SlideControls->elements = array();
					if($setActive == 0){
						//Show first Chapter
						$chapter->attributes = array('class'=>'active');
						$setActive++;
					}
					if ($key > 0){
						//Show Previous
						array_push($chapter->SlideControls->elements, new Element("input", array("type"=>"button", "value"=>"Previous"), "&nbsp;"));
					}
					if ($key < count($this->allChapters) - 1){
						//Show Next
						array_push($chapter->SlideControls->elements, new Element("input", array("type"=>"submit", "value"=>"Next", "name"=>$chapter->getName()),null));
					} else {
						//Show Finish
						array_push($chapter->SlideControls->elements, new Element("input", array("type"=>"submit", "value"=>"Finish", "name"=>$chapter->getName()),null));
					}
					echo $chapter->toHTML();
				}
			}
		}
	}
}
?>