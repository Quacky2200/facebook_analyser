<?php
class FacebookAnalyser extends Template{
	public function getName(){
		return __CLASS__;
	}
	public function __construct(){
		parent::__construct();
		ErrorHandler::setErrorHTML(require('Error500.php'));
	}
	public function getPages(){
		require('SDK.php');
		require('User.php');
		require('Home.php');
		require('Analyse.php');
		require('AnalysisResult.php');
		require('Results.php');
		require('Error404.php');

		return array(
			new Home(),
			new Analyse(),
			new Results(),
			new Error404()
		);
	}
}

return new FacebookAnalyser();
?>