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

	public $facebook, $helper, $permissions;
	public function __construct(){
		$this->permissions = [
			"user_about_me", 
			"user_birthday", 
			"user_friends", 
			"user_likes", 
			"user_photos", 
			"user_relationships", 
			"user_tagged_places", 
			"user_work_history", 
			"user_education_history", 
			"user_games_activity", 
			"user_location", 
			"user_posts", 
			"user_videos", 
			"user_events", 
			"user_hometown", 
			"user_relationship_details", 
			"user_status", 
			"user_website", 
			"user_religion_politics", 
			"email"
		];
		$this->facebook = new Facebook\Facebook([
			"app_id" => "1662017130721013",
			"app_secret" => "80001adde8707802a194f667e05adcb7",
			"default_graph_version" => "v2.2"
		]);
		$this->helper = $this->facebook->getRedirectLoginHelper();
	}
}
?>