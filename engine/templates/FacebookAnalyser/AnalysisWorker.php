<?php
class AnalysisWorker{
	private $asyncUserFunctions, $asyncAnalyseFunction;
	public function __construct(){
		$this->asyncUserFunctions = [
			"Getting generic profile data..."=>"getUserProfile",
			"Getting user posts..."=>"getUserPosts",
			"Getting user photos..."=>"getUserPhotos",
			"Getting user videos..."=>"getUserVideos",
			"Getting tagged information..."=>"getUserTagged",
			"Getting user likes..."=>"getUserLikes",
			"Getting user books..."=>"getUserBooks",
			"Getting user movies..."=>"getUserMovies",
		];
		$this->asyncAnalyseFunction = [
			"Analysing best popular users..." => "getPopularUsers",
			"Analysing friendship..."=>"getUserFriendCount"
		];
		//Just temporary
		$this->asyncAnalyseFunction = [ "Finding user interaction"=>"getUserInteraction"];
	}
	public function retrieveAll(){
		foreach($this->asyncUserFunctions as $key=>$value){
			set_time_limit(0);
			$this->echoImplicit($key);
			//Retrieve the data from the functions inside the associative array
			$data = User::Instance()->{$value}()->getGraphUser();
			//Store this data in a function, that function is now created from the user into this class
			//so that the developer only has to get the data from this class so $this->getUserLikes() will
			//return the data after this is all done.
			$this->{$value} = $data;//function() use ($data) {
			//	return $data;
			//};
		}
	}
	public function analyseAll(){
		foreach($this->asyncAnalyseFunction as $key=>$value){
			set_time_limit(0);
			$this->echoImplicit($key);
			//Analyse the data from the functions inside the associative array
			$this->{$value}();
		}
	}
	public function echoImplicit($text){
		echo "<script id=\"remove\">$('.loaderDescription').append($('<p>$text</p>'));</script>";
		for($k = 0; $k < 40000; $k++) echo ' ';
	}
	public function echoImplicitFinishAndRedirect($URL){
		echo "<noscript>
			<div align='center'>
				<a href='$URL' class='fblogin'>
					<i></i>
					<span>
						<b>Click</b> to view your <b>result</b>
					</span>
				</a>
			</div>
		</noscript><script id='remove'>urlRedirect = '$URL';</script>";
		for($k = 0; $k < 40000; $k++) echo ' ';
		ob_implicit_flush(false);
		exit();
	}
	public $data, $dbh;
	public function run(){
		$this->data = array();
		$this->echoImplicit("Preparing to retrieve data...");
		$this->retrieveAll();
		$this->echoImplicit("Preparing to analyse...");
		$this->analyseAll();
		$this->echoImplicit("All finished!");
		$newResultCode = Engine::generateRandomString(8);
		$this->dbh = Engine::getDatabase();
		$this->dbh->exec("INSERT INTO Users (User_ID, Name, Email) VALUES ('" . User::instance()->id . "', '" . User::instance()->name . "', '" . User::instance()->email . "')");
		$this->dbh->exec("INSERT INTO Results (Result_ID, Date, Data) VALUES ('" . $newResultCode . "', NOW(), '" . json_encode($this->data) . "')");
		$this->dbh->exec("INSERT INTO Result_History (User_ID, Result_ID) VALUES ('" . User::instance()->id . "', '" . $newResultCode . "')");
		$addr = Engine::getRemoteAbsolutePath((new Results())->getURL() . $newResultCode);
		$this->echoImplicitFinishAndRedirect($addr);
	}
	//We can't actually get friend count as it's not within the data structure you recieve...
	// private function getUserFriendCount(){
	// 	$this->data['friend_count'] = User::Instance()->getUserFriends()->getGraphUser()['summary']['count'];
	// }
	public $likingUsers, $commentingUsers, $getLikeAndCommentUsers, $getMostLikeAndCommentUsers, $posts;

	public function getUserInteraction() {
		$posts = $this->getUserPosts;
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
		    if ((count($this->likingUsers) == $this->totalLikeCount) || ($user[0] > ($this->totalLikeCount / count($this->likingUsers))) && ($this->id != $user[2])) {
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
				if ((count($this->commentingUsers) == $this->totalCommentCount) || ($user[0] > ($this->totalCommentCount / count($this->commentingUsers))) && ($this->id != $user[2])) {
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


	/**
	function readPosts($posts) {
		$postContents = array();

		for ($i = 0; $i < count($posts); $i++) {
			//echo $posts[$i];
			$taggedUsers = array();
			foreach ($posts[$i]['message_tags'] as $friends) {
				$taggedUsers[$posts[$i]['id']] = array($friends['id'], $friends['name']);
			}

			$postContents[$posts[$i]['id']] = array($posts[$i]['id'], $posts[$i]['message'], $taggedUsers);
		}

		return $postContents;
	}


	function analysePosts($posts) {
		$userConnections = array(); //temporary storage, storing in database would be better!

		foreach($posts as $post) {

			echo "<pre><h2>Post message</h2>";
			print_r($post[1]);
			echo "<h2>users tagged</h2>";
			print_r($post[2][$post[0]][0]);
			echo "</pre></br>";

			$results = analyseContext($post[1]);
			
			if (($results[0] > $results[1] && $results[1] > $results[0] / 2) || ($results[1] > $results[0] && $results[0] > $results[1] / 2))  {

			} else if ($results[0] > $results[1]) {
				if (array_key_exists($post[2][$post[0]][0], $userConnections)) {
					$userConnections[$post[2][$post[0]][0]]++;
				} else {
					$userConnections[$post[2][$post[0]][0]] = 1;
				}
			}
		}

		echo "<pre>";
		print_r($userConnections);
		echo "</pre>";
	}

	function analyseContext($message) {
		$positiveKeywords = array('love' => 0, 'like' => 0, 'friend' => 0, 'bestfriend' => 0, 'fun' => 0);
		$negativeKeywords = array('angry' => 0, 'hate' => 0, 'mad' => 0, 'sad' => 0 , 'dissapointed' => 0);

		$message = explode(" ", $message);
		foreach ($message as $word) {
			if (array_key_exists($word, $positiveKeywords)) {
				$positiveKeywords[$word]++;
			} elseif (array_key_exists($word, $negativeKeywords)) {
				$negativeKeywords[$word]++;
			}
		}

		$totalPositiveKeywords = 0;
		$totalNegativeKeywords = 0;

		foreach ($positiveKeywords as $word => $count) {
			$totalPositiveKeywords += $count;
		}

		foreach ($negativeKeywords as $word => $count) {
			$totalNegativeKeywords += $count;
		}

		echo "<pre><h3>Positive comment collection</h3>";
		print_r($positiveKeywords);
		echo "<h3>Negative comment collection</h3>";
		print_r($negativeKeywords);
		echo "</pre>";

		return array($totalPositiveKeywords, $totalNegativeKeywords);

	}	
	**/
	
}
?>