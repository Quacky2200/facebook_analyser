<?php
class User extends DBObject{
	public $id, $name, $gender, $birthday, $education;//, $username, $email, $languages, $locale, $timezone, $gender, $location, $hometown;
	private static $permissions = ["email", "user_likes", "user_work_history", "user_education_history", "user_birthday"];
	//user_about_me, user_birthday, user_friends, user_likes, user_photos, user_relationships, user_tagged_places, user_work_history, user_education_history, user_games_activity, user_location, user_posts, user_videos, user_events, user_hometown, user_relationship_details, user_status, user_website, user_religion_politics, email
	public static function instance(){
		static $instance;
		if(is_null($instance)){
			$instance = new User();
		}
		return $instance;
	}
	public function getToken(){
		return (isset($_SESSION['facebook_sdk_access_token']) ? $_SESSION['facebook_sdk_access_token'] : null);
	}
	public function setToken($value){
		$_SESSION['facebook_sdk_access_token'] = $value;
	}
	public function isLoggedIn(){
		//Try gaining access to the Facebook PHP SDK
		try{
			$accessToken = SDK::instance()->helper->getAccessToken();
		} catch (Facebook\Exceptions\FacebookResponseException $e){
			throw new Exception("Graph returned an error: " . $e->getMessage());
		} catch (Facebok\Exceptions\FacebookSDKException $e){
			throw new Exception("Facebook SDK returned an error: " . $e->getMessage());
		}
		//Assuming it went well, let's process our login state
		if(isset($accessToken) && is_null($this->getToken())){
			$this->setToken((string)$accessToken);
			return true;
		} else if(isset($accessToken) && !is_null($this->getToken())){
			return true;
		} else if(!isset($accessToken) && !is_null($this->getToken())){
			return true;
		} else {
			return false;
		}
	}
	public function loadProfile(){
		if ($this->isLoggedIn()){
			//Load profile
			//Get this instead...
			//me?fields=id,name,birthday,photos{tags},videos{tags},likes{about,artists_we_like,attire,awards,band_interests,bio,app_id,name,name_with_location_descriptor},tagged,work,posts.include_hidden(true){privacy,place,actions,name,description},friends{about,address,age_range,birthday,education,email},about,education,age_range,email,hometown,relationship_status,religion,gender,bio,music{name},movies{name},books{name}
			$response = SDK::instance()->facebook->get('/me?fields=id,name,gender,birthday,work,education', $this->getToken());
			$this->importFromJson($response->getGraphUser());
			//Load was successful
			return true;
		} elseif(SDK::instance()->helper->getError()) {
			throw new Exception(var_dump(SDK::instance()->helper->getError()));
		}
		//Couldn't get anything
		return false;
	}
	public function getFacebookAuthURL($URL){
		return SDK::instance()->helper->getLoginURL($URL, self::$permissions);
	}
}

?>