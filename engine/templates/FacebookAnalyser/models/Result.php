<?php
class Result{
	public $Result_ID, $Date, $Data, $Visibility;
	/*
		This class needs a lot of work to make the analysis to be finalised 
		so that it will work in tandem with derived AnalysisElement classes and the
		AsyncAnalysisWorker

		We can currently only look at:
		-> Friendship by post interaction
		-> Time of posts (perhaps likes on pages/movies)
		-> Privacy of posts
		-> Amount of privacy based on user profile details

		We then need to write down the analysis in a readable format.

		Should we have Facebook user categories because of their interaction with Facebook?

		Should we have a trophy system to award the user/friends based on their result?

		Should we have a Top 3 friend display to show who's the most loyal friend?

	*/
	public function getReadable(){
		//Returns a user readable result based from the raw data.
		return "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc elit mauris, pretium id nisl nec, interdum laoreet nulla. Donec quis pretium sapien. Mauris condimentum at sem id pharetra. Integer non dui at elit elementum dictum. Donec auctor libero at sapien pharetra semper. Nulla maximus metus eros, ac mattis leo fermentum nec. Sed urna sem, finibus pellentesque ipsum vel, pellentesque varius dolor.</p>
			
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc elit mauris, pretium id nisl nec, interdum laoreet nulla. Donec quis pretium sapien. Mauris condimentum at sem id pharetra. Integer non dui at elit elementum dictum. Donec auctor libero at sapien pharetra semper. Nulla maximus metus eros, ac mattis leo fermentum nec. Sed urna sem, finibus pellentesque ipsum vel, pellentesque varius dolor.</p>";
	}
	public function getFacts(){
		//Returns the raw data that *should* still be formatted in a presentable HTML format
		return "<div align='center'><h3> Facts </h3><br/><div style='background: rgb(240,240,240);border-radius: 5px;display:inline-block; padding: 1vh 1vw;'>No data is available at this time</div></div>";
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
			' year' => $secs / 31556926 % 12,
			' week' => $secs / 604800 % 52,
			' day' => $secs / 86400 % 7,
			' hour' => $secs / 3600 % 24,
			' minute' => $secs / 60 % 60//,
			//' second' => $secs % 60
		);

		foreach($bit as $k => $v){
			if($v > 0){
				$ret[] = $v . ($v == 1 ? $k : $k . "s");
				return join(' ', $ret) . " ago";
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
// $test = new Result();
// $test->Result_ID = 1;
// $test->Date = time();
// $test->Analysis_Data = [
// 	"friend_count" => 150,
// 	"most_engaged_users" => [
// 		"Shaun George" => [
// 			"likes"=>[
// 				0 =>"23401234910861",
// 				1 =>"10011019128101"
// 			],
// 			"comments"=>[
// 				0=>"920181238234"
// 			]
// 		],
// 		"Matthew James" => [
// 			"likes"=>[
// 				0=>"9023499485"
// 			],
// 			"comments"=>[
// 			]
// 		]
// 	],
// 	"average_user_engagement"=>"943920600", //This is unix time format - the current value is 10 minutes (the average duration of this dummy user)
// 	"most_popular_post_recently"=>[
// 		"id"=>"292001118811", 
// 		"likes"=>30,
// 		"comments"=>10
// 	]
// ];
?>