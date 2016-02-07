<?php
class User extends DBObject{

	public $id, $name, $email, $posts, $totalLikeCount, $totalCommentCount, $likingUsers, $commentingUsers;

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
		if(!is_null($this->getToken()) || isset($accessToken)){
			//This if statements means that it doesn't matter if the session token is set or not, 
			//as long as we have the access token either by request or by session, we can use the session
			if(is_null($this->getToken())) $this->setToken((string)$accessToken);
			//Get basic user profile information such as user id, name and email to test whether the session works
			$this->importFromJson($this->getBasicUserProfile()->getGraphUser());
			//Get user statuses and metadata such as likes, comments
			$this->importFromJson($this->getUserPosts()->getGraphUser());

			return true;
		} else {
			return false;
		}
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
		return $this->getFacebookData("likes{about,artists_we_like,attire,awards,band_interests,bio,app_id,name,name_with_location_descriptor}");
	}
	public function getUserPosts(){
		return $this->getFacebookData("posts{likes{id, name}, comments{from}}");
	}
	public function getUserPhotos(){
		return $this->getFacebookData("photos{tags}");
	}
	public function getUserVideos(){
		return $this->getFacebookData("videos{tags}");
	}
	public function getUserTagged(){
		return $this->getFacebookData("tagged");
	}
	public function getUserFriends(){
		return $this->getFacebookData("friends{about,address,age_range,birthday,education,email}");
	}
	public function getUserMovies(){
		return $this->getFacebookData("movies{name}");
	}
	public function getUserMusic(){
		return $this->getFacebookData("music{name}");
	}
	public function getUserBooks(){
		return $this->getFacebookData("books{name}");
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


	public function getUserInteraction() {
		$this->likingUsers = array();
	    $this->commentingUsers = array();
		$this->totalLikeCount = 0;
		$this->totalCommentCount = 0;

		for ($i = 0; $i < count($this->posts); $i++) {
			if (isset($this->posts[$i]->likes)) {
			    $this->getLikeUsers($this->posts[$i]->likes);
			}
			if (isset($this->posts[$i]->comments)) {
			    $this->getCommentUsers($this->posts[$i]->comments);
			}
		}



		echo "<pre> array containing likes below.</br>";
		print_r($this->likingUsers);
		echo "commenting</br>";
		print_r($this->commentingUsers);
		echo "liking and commenting</br>";
		print_r($this->getLikeAndCommentUsers());
		echo "Users that like and comment above avg</br>";
		print_r($this->getMostLikeAndCommentUsers($this->getLikeAndCommentUsers()));
		echo "</pre>";
	}

	public function getLikeUsers($userMessageLikes) {       
        //stores name,id and # of times user has liked all user posts
		for ($x = 0; $x < count($userMessageLikes); $x++) {
			$this->totalLikeCount++;
			if (array_key_exists($userMessageLikes[$x]->id, $this->likingUsers)) {
				$this->likingUsers[$userMessageLikes[$x]->id][0]++;
			} elseif ($this->id != $userMessageLikes[$x]->id) {
				$this->likingUsers[$userMessageLikes[$x]->id] = array(1, $userMessageLikes[$x]->name, $userMessageLikes[$x]->id); //notice first element is not zero
			}
		}
	}

	public function getMostLikesUser() {
		$mostLikingUsers = array();
		foreach ($this->likingUsers as $user) {
		    if ((count($this->likingUsers) == $this->totalLikeCount) || ($user[0] > (count($this->likingUsers) / $this->totalLikeCount)) && ($this->id != $user[2])) {
                $mostLikingUsers[$user[2]] = $user;
			}
		}
		return $mostLikingUsers;
	}

	public function getCommentUsers($userMessageComments) {
        for ($x = 0; $x < count($userMessageComments); $x++) {
        	$this->totalCommentCount++;
            if (array_key_exists($userMessageComments[$x]->from->id, $this->commentingUsers)) {
            	$this->commentingUsers[$userMessageComments[$x]->from->id][0]++;
            } elseif ($this->id != $userMessageComments[$x]->from->id) {
            	$this->commentingUsers[$userMessageComments[$x]->from->id] = array(1, $userMessageComments[$x]->from->name, $userMessageComments[$x]->from->id); //notice first element is not zero
            }
        }
	}

	public function getMostCommentingUsers() {
		$mostCommentingUsers = array();
        foreach ($this->commentingUsers as $user) {
            foreach ($this->commentingUsers as $user) {
				if ((count($this->commentingUsers) == $this->totalCommentCount) || ($user[0] > (count($this->commentingUsers) / $this->totalCommentCount)) && ($this->id != $user[2])) {
					$mostCommentingUsers[$user[2]] = $user;
                } 
            }        	
        }
        return $mostCommentingUsers;
	}

	public function getLikeAndCommentUsers() {
		$likeAndCommentUsers = array();
        foreach ($this->likingUsers as $userOne) {
        	foreach($this->commentingUsers as $userTwo) {
        		if ($userOne[2] == $userTwo[2]){
        			$likeAndCommentUsers[$userOne[2]] = [($userOne[0] + $userTwo[0]), $userOne[1], $userOne[2]];
        	    }
        	}
        }
        return $likeAndCommentUsers;
	}

	public function getMostLikeAndCommentUsers($likeAndCommentUsers) {
		$mostLikeAndCommentUsers = array();
        foreach ($likeAndCommentUsers as $user) {
        	//we add 2 to the count($likeAndCommentUsers) becauser totalLikeCount and totalCommentCount isnt zero based
			if ((count($likeAndCommentUsers) == $likeAndCommentUsers) || ($user[0] > (($this->totalLikeCount + $this->totalCommentCount) / (count($likeAndCommentUsers) + 2))) && ($this->id != $user[2])) {
				array_push($mostLikeAndCommentUsers, $user);
            }       	
        }
        return $mostLikeAndCommentUsers;
	}
	
}

?>