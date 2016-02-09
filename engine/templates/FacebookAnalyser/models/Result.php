<?php
class Result{
	public $Result_ID, $Date, $Data;

	public function getFriendDescription(){
		$count = $this->Data['friend_count'];
		//$age = $this->Analysis_Data['age']; - this could potentially be useful perhaps?
		switch(true){
			case $count <= 50:
				//Close friends only - suggests secretive/unpopular person?
				return "";
			case $count <= 150:
				//Dunbars number - within 150. 
				return "";
			case $count <= 350:
				//Most type of people with Facebook, probably gone to many places in education/pretty popular
			case $count <= 500:
				//Very popular
			default:
				//Most likely well over 500... Hmmm..
				//You must be a celebrity haha
		}
	}
	private function getMostEngagedFriend(){
		//Get's the most engaged friend at the top of the list (assuming it's descending in popularity).
		return "You're most engaged user is " . reset($this->Data['most_engaged_users']);
	}
	private function getUserEngagement(){
		//This gets the average time it takes for a user to make a post - looking through the users most recent posts going back to 100 posts.
		$avg = $this->Data['average_user_engagement'];
		/* 
		E.g. within 30 mins = , 
		within 2.5 hours, 
		within 8 hours, 
		within 24 hours, 
		within 5 days,
		over = you most likely have only made a few posts
		*/
		switch($avg){
			default:
			return "";
		}
	}
	public function getResult(){
		return $this->getFriendDescription() . "." . $this->getMostEngagedFriend() . " and " . $getUserEngagement();
	}
	//Thanks to http://php.net/manual/en/function.time.php#Hcom108581
	public function getTimeElapsedShort($secs){
		$bit = array(
			'y' => $secs / 31556926 % 12,
			'w' => $secs / 604800 % 52,
			'd' => $secs / 86400 % 7,
			'h' => $secs / 3600 % 24,
			'm' => $secs / 60 % 60,
			's' => $secs % 60
		);
			
		foreach($bit as $k => $v)
			if($v > 0)$ret[] = $v . $k;
			
		return join(' ', $ret);
	}
	public function getTimeElapsedApproximate($secs){
		$bit = array(
			'years' => $secs / 31556926 % 12,
			'weeks' => $secs / 604800 % 52,
			'days' => $secs / 86400 % 7,
			'hours' => $secs / 3600 % 24,
			'minutes' => $secs / 60 % 60//,
			//' seconds' => $secs % 60
		);
			
		foreach($bit as $k => $v){
			if($v > 0){
				$ret[] = $v . $k;
				return join(' ', $ret) . "ago";
			}
		}
		return "just now";
	}
	public function getTimeElapsedLong($secs){
		$bit = array(
			' year'        => $secs / 31556926 % 12,
			' week'        => $secs / 604800 % 52,
			' day'        => $secs / 86400 % 7,
			' hour'        => $secs / 3600 % 24,
			' minute'    => $secs / 60 % 60,
			' second'    => $secs % 60
		);
			
		foreach($bit as $k => $v){
			if($v > 1)$ret[] = $v . $k . 's';
			if($v == 1)$ret[] = $v . $k;
			}
		array_splice($ret, count($ret)-1, 0, 'and');
		$ret[] = 'ago.';
		
		return join(' ', $ret);
		//TESTING:
		// $nowtime = time();
		// $oldtime = 1335939007;

		// echo "time_elapsed_A: ".time_elapsed_A($nowtime-$oldtime)."\n";
		// echo "time_elapsed_B: ".time_elapsed_B($nowtime-$oldtime)."\n";
	}
}
$test = new Result();
$test->Result_ID = 1;
$test->Date = time();
$test->Analysis_Data = [
	"friend_count" => 150,
	"most_engaged_users" => [
		"Shaun George" => [
			"likes"=>[
				0 =>"23401234910861",
				1 =>"10011019128101"
			],
			"comments"=>[
				0=>"920181238234"
			]
		],
		"Matthew James" => [
			"likes"=>[
				0=>"9023499485"
			],
			"comments"=>[
			]
		]
	],
	"average_user_engagement"=>"943920600", //This is unix time format - the current value is 10 minutes (the average duration of this dummy user)
	"most_popular_post_recently"=>[
		"id"=>"292001118811", 
		"likes"=>30,
		"comments"=>10
	]
];
?>