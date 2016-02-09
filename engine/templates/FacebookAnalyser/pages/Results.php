<?php
class Results extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/result/";
	}
	public $result;
	public function isMatch($URL){
		/*
			This regex is to match an example like:
				/result/a8fjS8wK
				/result/a8fjS8wK/
		*/
		if(preg_match("/^\/(result\/[a-z0-9A-Z]{8})[\/]?$/", $URL)){
			//TODO: Find result for match in DB
			//to return true if found (and store within $this->result), otherwise false
			//$this->result
			return true;
		} 
		return false;
	}
	public function run($template){
		require("login.php");
		//TODO: Load result from DB into Result model using PDO
		// and use getResult function on model
		//TODO: Get time elapsed from loaded result
	}
	public function show($template){
		include("section/header.php");
		include("section/middle_result.php");
		include("section/footer.php");
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
?>