<?php 
class ConfigSetup extends SetupChapter{
	public function __construct(){
		parent::__construct("engine-welcome-chapter", "Welcome", "Before we start, we first have to setup a few things to get you going. Please follow the steps, it will only take a couple of minutes.");
	}
	public function onLoad(){
		if(file_exists(TEMP_CONFIG_FILE) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/.htaccess")){
			$this->setEnabled(false);
		}
	}
	public function onSubmit(){
		$Config = new Config(TEMP_CONFIG_FILE, false);
		try{
			$htaccess = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
			if(file_exists($htaccess)){
				unlink($htaccess);
			}
			file_put_contents($htaccess, 
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
		$Config->save(true);
	} catch (Exception $e){
		$this->sendFail(array($this->addName('write-error')));
		exit();
	}
		$this->sendSuccess();
	}
	public function getElements(){
		return array(
			new Element("p", array("class"=>"input error " . $this->addName("write-error")), "Cannot write to the directory. Make sure you have given the right priviledges to your webserver software."),
		);
	}
}
?>