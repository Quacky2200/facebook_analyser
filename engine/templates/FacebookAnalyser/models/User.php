<?php
require(Engine::getLocalDir() . "/structure/ReflectiveObject.php");
class User extends ReflectiveObject{
	public $id, $name, $email;
	const YEARS_TO_GO_BACK = 2;
	const MOST_POST_AVG_PER_DAY = 10;
	public static function instance(){
		static $instance;
		if(is_null($instance)) $instance = new User();
		return $instance;
	}
	public function __construct(){
		$this->isLoggedIn = $this->authenticate();
	}
	public function getToken(){
		return (isset($_SESSION['facebook_sdk_access_token']) ? $_SESSION['facebook_sdk_access_token'] : null);
	}
	public function setToken($value){
		$_SESSION['facebook_sdk_access_token'] = $value;
	}
	private $isLoggedIn;
	private function authenticate(){
		//Try gaining access to the Facebook PHP SDK
		try{
			$accessToken = SDK::instance()->helper->getAccessToken();
		} catch (Facebook\Exceptions\FacebookResponseException $e){
			throw new Exception("Graph returned an error: " . $e->getMessage());
		} catch (Facebok\Exceptions\FacebookSDKException $e){
			throw new Exception("Facebook SDK returned an error: " . $e->getMessage());
		}
		//Assuming it went well, let's process our login state
		if(!is_null($this->getToken()) || isset($accessToken)){
			//This if statements means that it doesn't matter if the session token is set or not, 
			//as long as we have the access token either by request or by session, we can use the session
			if(is_null($this->getToken())){
				$this->setToken((string)$accessToken);
				header("Location: " . Engine::getRemoteAbsolutePath((new Analyse())->getURL()));
			}
			//Get basic user profile information such as user id, name and email to test whether the session works
			try{
				$this->importFromJson($this->getBasicUserProfile()->getGraphUser());
			} catch (Facebook\Exceptions\FacebookResponseException $e){
				if(strpos($e->getMessage(), "The user has not authorized application") > -1){
					Engine::clearSession();
					header("Location: " . Engine::getRemoteAbsolutePath((new Home())->getURL()));
				} else{
					throw $e;
				}
				exit();
			}
			return true;
		} else {
			return false;
		}
	}
	public function isLoggedIn(){
		return $this->isLoggedIn;
	}
	public function getFacebookAuthURL($URL){
		/*
			Make a login callback, by specifying the url to the SDK. For Example, 
			when a user clicks on the FB login button, they are redirected to FB
			for authentication. When the authentication is complete, FB will 
			redirect the user back to our website and our website will then 
			contain the session the user created.
		*/
		return SDK::instance()->helper->getLoginURL($URL, SDK::instance()->permissions);
	}
	public function getFacebookDeAuthURL($URL){
		$token = $this->getToken();
		return "https://www.facebook.com/logout.php?next=$URL&access_token=$token";
	}
	public function getUserLikes(){
		return $this->getFacebookData("likes{about,artists_we_like,attire,awards,band_interests,bio,app_id,name,name_with_location_descriptor,created_time}");
	}
	public function getUserPosts(){
		//return $this->getFacebookData("posts.include_hidden(true){privacy,place,actions,name,description}");
		return $this->getFacebookData('posts.limit(' . ((self::MOST_POST_AVG_PER_DAY * 7) * 52) * self::YEARS_TO_GO_BACK . '){privacy,name,description,message,status_type,likes{id,name},comments{from,message, created_time},created_time,message_tags,with_tags}');
	}
	public function getUserPhotos(){
		return $this->getFacebookData("photos{tags,created_time}");
	}
	public function getUserVideos(){
		return $this->getFacebookData("videos{tags,created_time}");
	}
	public function getUserTagged(){
		return $this->getFacebookData("tagged");
	}
	public function getUserMovies(){
		return $this->getFacebookData("movies{name,created_time}");
	}
	public function getUserMusic(){
		return $this->getFacebookData("music{name,created_time}");
	}
	public function getUserBooks(){
		return $this->getFacebookData("books{name,created_time}");
	}
	public function getBasicUserProfile(){
		return $this->getFacebookData("id,name,email");
	}
	public function getUserProfile(){
		return $this->getFacebookData("id,name,email,gender,age_range,birthday,about,work,education,hometown,relationship_status,religion,bio");
	}
	public function getFacebookData($fields){
		//Allow other classes to get Facebook Data if they require it, rather than 
		//restricting it to the data we get from the functions
		return SDK::instance()->facebook->get("me?fields=$fields", $this->getToken());
	}
	public static function getAvatar($id){
		return "https://graph.facebook.com/$id/picture?type=large";
	}
}

?>