<?php
require('libs/Facebook/autoload.php');
class SDK{
	public static function instance(){
		static $instance;
		if(is_null($instance)){
			$instance = new SDK();
		}
		return $instance;
	}

	public $facebook, $helper;
	public function __construct(){
		$this->facebook = new Facebook\Facebook([
			"app_id" => "1662017130721013",
			"app_secret" => "80001adde8707802a194f667e05adcb7",
			"default_graph_version" => "v2.2"
		]);
		$this->helper = $this->facebook->getRedirectLoginHelper();
	}
}
?>