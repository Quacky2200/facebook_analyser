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
        return $commentingUsers;
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
}

?>
