<?php 
class ConfigSetup extends SetupChapter{
	private $htaccessFilename;
	public function __construct(){
		$this->htaccessFilename = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'] . ".htaccess";
		parent::__construct("engine-welcome-chapter", "Welcome", "Before we start, we first have to setup a few things to get you going. Please follow the steps, it will only take a couple of minutes.");
	}
	public function onLoad(){
		if(file_exists(TEMP_CONFIG_FILE) && file_exists($this->htaccessFilename)){
			$this->setEnabled(false);
		}
	}
	public function onSubmit(){
		ErrorHandler::stop();
		$Config = new Config(TEMP_CONFIG_FILE, false);
		$done_htaccess = file_put_contents($this->htaccessFilename, 
"Options -Indexes
RewriteEngine On
#RewriteBase /
#RewriteCond %{HTTPS} !on
#RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

RewriteCond %{REQUEST_FILENAME} -f
RewriteRule \.(jp[e]?g|gif|png|css|js|ttf|woff|ico|bmp|pdf|doc[x]?)$ - [L]

#Redirect all files not match index.php
RewriteCond %{REQUEST_FILENAME} !(.*)/index\.php$
RewriteRule ^.*$ index.php?current_engine_page=$0 [L,NC,QSA]");
		$done_config = $Config->save(true);
		if(!$done_htaccess || !$done_config){
			$this->sendStatus(true, array($this->addName('write-error'), "htaccess: " . (string)$done_htaccess, "config: " . (string)$done_config));
		}
	}
	public function getElements(){
		return array(
			new Element("p", array("class"=>"input error " . $this->addName("write-error")), "Cannot write to the directory. Make sure you have given the right priviledges to your webserver software."),
		);
	}
}
?>