<?php 
class FinishSetup extends SetupChapter{
	public function __construct(){
		parent::__construct("engine-finish-chapter", "Hurrah!", "The setup was successful!<br/><br/>Click Finish to exit the setup.");
	}
	public function onLoad(){
		if(file_exists(TEMP_CONFIG_FILE) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/.htaccess")){
			$this->setEnabled(false);
		}
	}
	public function onSubmit(){
		$Config = new Config(TEMP_CONFIG_FILE);
		$Config->save(true);
		rename(TEMP_CONFIG_FILE, CONFIG_FILE);
		header("Location: " . Engine::getRemoteDir($_SERVER['DOCUMENT_ROOT']) . './');
	}
	public function getElements(){
		return array();
	}
}
?>