<?php
class Analyse extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/analyse/";
	}
	public function isMatch($URL){
		$isNew = ($URL == $this->getURL() . "new/");
		$this->forceNew = $isNew;
		return $URL == $this->getURL() || $isNew;
	}
	public $user, $forceNew;
	public function run($template){
		$this->user = User::instance();
		if(!$this->user->isLoggedIn()){
			//Redirect back to the login page
			ob_clean();
			header("Location: " . Engine::getRemoteAbsolutePath((new Home())->getURL()));
			exit();
		}
		//TODO: if there's an AnalysisResult, redirect to results page rather than create a new one
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
		echo "<script>$(\".loaderDescription\").append($(\"<p>Preparing to analyse...</p>\"));</script>";
		$getStuff = array(
			"Getting generic profile data..."=>"getUserProfile",
			"Getting user posts..."=>"getUserPosts",
			"Getting user photos..."=>"getUserPhotos",
			"Getting user videos..."=>"getUserVideos",
			"Getting tagged information..."=>"getUserTagged",
			"Getting user likes..."=>"getUserLikes",
			"Getting user books..."=>"getUserBooks",
			"Getting user movies..."=>"getUserMovies",
		);
		foreach($getStuff as $key=>$value){
			echo "<script>$(\".loaderDescription\").append($(\"<p>$key</p>\"));</script>";
			for($k = 0; $k < 40000; $k++) echo ' ';
			sleep(0.1);
			$stuff = $this->user->{$value}()->getGraphUser();
			echo "<script>alert(\"$stuff\");</script>";
		}
		$addr = Engine::getRemoteAbsolutePath((new Results())->getURL());
		echo "<noscript><div align='center'><a href='$addr' class='fblogin'><i></i><span><b>Click</b> to view your <b>result</b></span></a></div></noscript><script>urlRedirect = '$addr';</script>";
		ob_implicit_flush(false);
		exit();
	}
	public function show($template){
		include(__DIR__ . '/views/header.php');
		include(__DIR__ . '/views/analyse.php');
		$this->getAll();
	}
}
?>