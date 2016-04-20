<?php
    
class UserDummy {

    public $id;

    public function __construct($id) {
        $this->$id = $id;
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

	}

	public function getLikeUsers($userMessageLikes) {
	$totalLikeCount = 0;
	$likingUsers = array();   
        //stores name,id and # of times user has liked all user posts
		for ($x = 0; $x < count($userMessageLikes); $x++) {
			$totalLikeCount++;
			if (array_key_exists($userMessageLikes[$x]->id, $likingUsers)) {
				$likingUsers[$userMessageLikes[$x]->id][0]++;
			} elseif ($this->id != $userMessageLikes[$x]->id) {
				$likingUsers[$userMessageLikes[$x]->id] = array(1, $userMessageLikes[$x]->name, $userMessageLikes[$x]->id); //notice first element is not zero
			}
		}
	return [$totalLikeCount, $likingUsers];
	}

    public function getMostLikeUsers($likingUsers, $totalLikeCount) {
		$mostLikingUsers = array();
		foreach ($likingUsers as $user) {
		    if ((count($likingUsers) == $totalLikeCount) || ($user[0] > ($totalLikeCount / count($likingUsers))) && ($this->id != $user[2])) {
                $mostLikingUsers[$user[2]] = $user;
			}
		}
		return $mostLikingUsers;
    }

	public function getCommentUsers($userMessageComments) {
		$commentingUsers = array();
		$totalCommentCount = 0;
        for ($x = 0; $x < count($userMessageComments); $x++) {
        	$totalCommentCount++;
            if (array_key_exists($userMessageComments[$x]->from->id, $commentingUsers)) {
            	$commentingUsers[$userMessageComments[$x]->from->id][0]++;
            } elseif ($this->id != $userMessageComments[$x]->from->id) {
            	$commentingUsers[$userMessageComments[$x]->from->id] = array(1, $userMessageComments[$x]->from->name, $userMessageComments[$x]->from->id); //notice first element is not zero
            }
        }
        return [$totalCommentCount, $commentingUsers];
	}

	public function getMostCommentingUsers($commentingUsers, $totalCommentCount) {
		$mostCommentingUsers = array();
        foreach ($commentingUsers as $user) {
            foreach ($commentingUsers as $user) {
				if ((count($commentingUsers) == $totalCommentCount) || ($user[0] > ($totalCommentCount / count($commentingUsers))) && ($this->id != $user[2])) {
					$mostCommentingUsers[$user[2]] = $user;
                } 
            }        	
        }
        return $mostCommentingUsers;
	}

	public function getLikeAndCommentUsers($likingUsers, $commentingUsers) {
		$likeAndCommentUsers = array();
        foreach ($likingUsers as $userOne) {
        	foreach($commentingUsers as $userTwo) {
        		if ($userOne[2] == $userTwo[2]){
        			$likeAndCommentUsers[$userOne[2]] = [($userOne[0] + $userTwo[0]), $userOne[1], $userOne[2]];
        	    }
        	}
        }
        return $likeAndCommentUsers;
	}

	public function getMostLikeAndCommentUsers($likeAndCommentUsers, $totalLikeCount) {
		$mostLikeAndCommentUsers = array();
        foreach ($likeAndCommentUsers as $user) {
        	//we add 2 to the count($likeAndCommentUsers) becauser totalLikeCount and totalCommentCount isnt zero based
			if ((count($likeAndCommentUsers) == $likeAndCommentUsers) || ($user[0] > ($totalLikeCount / (count($likeAndCommentUsers) + 2))) && ($this->id != $user[2])) {
				array_push($mostLikeAndCommentUsers, $user);
            }       	
        }
        return $mostLikeAndCommentUsers;
	}



	function readPosts($posts) {
		$postContents = array();

		for ($i = 0; $i < count($posts); $i++) {		
			$taggedUsers = array();
			foreach ($posts[$i]['message_tags'] as $friends) {
				$taggedUsers[$posts[$i]['id']] = array($friends['id'], $friends['name']);
			}
			$postContents[$posts[$i]['id']] = array($posts[$i]['id'], $posts[$i]['message'], $taggedUsers);
		}
		return $postContents;
	}

	function analysePosts($posts) {
		echo "<pre>";
		print_r($posts);
		echo "</pre>";

		$userConnections = array();

		foreach($posts as $post) {
			$results = analyseContext($post[1]);
			
			if (!array_key_exists($post[2][$post[0]][0], $userConnections)) {
					$userConnections[$post[2][$post[0]][0]] = array(0, 0); //First index represents friendship and the second enmity
			}

			if (($results[0] > $results[1] && $results[1] > $results[0] / 2) || ($results[1] > $results[0] && $results[0] > $results[1] / 2))  {
				$userConnections[$post[2][$post[0]][0]][0]++;
				$userConnections[$post[2][$post[0]][0]][1]++;
			} else if ($results[0] > $results[1]) {
				$userConnections[$post[2][$post[0]][0]][0]++;
			} else if ($results[0] < $results[1]) {
				$userConnections[$post[2][$post[0]][0]][1]++;
			}
		}
	}

	function analyseContext($message) {
		$positiveKeywords = array('love' => 0, 'like' => 0, 'friend' => 0, 'bestfriend' => 0, 'fun' => 0);
		$negativeKeywords = array('angry' => 0, 'hate' => 0, 'mad' => 0, 'sad' => 0 , 'disappointed' => 0);

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
		return array($totalPositiveKeywords, $totalNegativeKeywords);

	}	
}

?>
