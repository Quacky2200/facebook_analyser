<?php
class Analyse extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/analyse/";
	}
	public function isMatch($URL){
		return $URL == $this->getURL();
	}
	public $user;
	public function run($template){
		$this->user = User::instance();
		if(!$this->user->isLoggedIn()){
			//Redirect back to the login page
			ob_clean();
			header("Location: " . Engine::getRemoteAbsolutePath((new Home())->getURL()));
		}
	 // } else {
		// 	header("Location: " . Engine::getRemoteAbsolutePath($this->user->getFacebookAuthURL($this->getURL())));
		// 	exit();
		// }
	}
	public function tempRecursive($obj){
		$stuff = null;
		foreach($obj as $key=>$value){
			if(is_array($value)){
				$stuff .= "\n$key:" . $this->tempRecursive($value);
			} else if(is_object($obj)){
				$stuff .= "\n$key:" . $this->tempRecursive(get_object_vars($this));
			} else {
				$stuff .= "\n\t$key: $value;";
			}
		}
		return $stuff;
	}
	public function getAll(){
		ob_implicit_flush(true);
		$getStuff = array(
			"Getting generic profile data..."=>"getUserProfile",
			"Getting user photos..."=>"getUserPhotos",
			"Getting user videos..."=>"getUserVideos",
			"Getting tagged information..."=>"getUserTagged",
			"Getting user posts..."=>"getUserPosts",
			"Getting user likes..."=>"getUserLikes",
			"Getting user books..."=>"getUserBooks",
			"Getting user movies..."=>"getUserMovies",
		);
		foreach($getStuff as $key=>$value){
			echo "<br/>$key";
			for($k = 0; $k < 40000; $k++) echo ' ';
			sleep(0.1);
			$this->user->{$value}();
		}
		// return SDK::instance()->facebook->get(
		// 	'me?fields=id,name,birthday,photos{tags},videos{tags},likes{about,artists_we_like,attire,awards,band_interests,bio,app_id,name,name_with_location_descriptor},tagged,work,posts.include_hidden(true){privacy,place,actions,name,description},friends{about,address,age_range,birthday,education,email},about,education,age_range,email,hometown,relationship_status,religion,gender,bio,music{name},movies{name},books{name}',
		// 	$this->getToken()
		// );
	}
	public function show($template){
		include(__DIR__ . '/views/header.php');
		echo "<h1>Start trying to do things with FB and the analysis part...</h1>";
		echo "User's first name: " . $this->user->name;
		$this->getAll();
		//echo "<br/>User's birth date: " . $this->user->birthday;
		//Add lots more to list, then to check against etc :)
		//echo "User's educational venue: " . $this->user->education['school']['name'];
		//echo "User's educational vocation: " . $this->user->education['concentration']['name'];
		//var_dump($this->user->getAll());
		include(__DIR__ . '/views/footer.php');
	}
}
?>