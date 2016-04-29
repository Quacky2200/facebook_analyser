<?php
class Result extends Page{
	public $Result_ID, $Date, $Data, $Visible, $Name, $User_ID;
	private $URLMatch, $POSTFunctions;
	public function __construct(){
		$this->POSTFunctions = array(
		);
	}
	public function getName(){
		return ($this->Result_ID ? $this->getPronoun() . " Result" : "Error");
	}
	public function getURL(){
		return "/result/" . $this->Result_ID;
	}
	public function isMatch($URL){
		//Returns a boolean value on whether this regex matches a result URL such as: /result/a8fjS8wK
		return preg_match("/^(?:\/result)\/(\w{8})[\/]?$/", $URL, $this->URLMatch);
	}
	private $mustExist = array(
		'activity',
		'interaction',
		'horoscope'
	);
	public static function create($Result_ID, $Data){
		//Get the database connection so we can upload the result
		$dbh = Engine::getDatabase();
		//Make sure that if we get any errors, that we get told about them.
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//Insert the data into the results first
		$sql = "INSERT INTO Results (Result_ID, Date, Data, Visible) VALUES (:result, NOW(), :data, true)";
		$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':result'=>$Result_ID, ':data'=> json_encode($Data)));
		//Add the relationship between the result and the user
		$dbh->exec("INSERT INTO Result_History (User_ID, Result_ID) VALUES ('" . User::instance()->id . "', '" . $Result_ID . "')");
	}
	public static function toggleVisibility($Result_ID){
		//Try sharing the result
		try{
			$dbh = Engine::getDatabase();
			$sql = "UPDATE Results SET Visible=NOT Visible WHERE Result_ID = :result AND Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "')";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array(':result'=> $Result_ID));
			return true;
		} catch (PDOException $e){
			return false;
		}
	}
	public static function delete($Result_ID){
		//TODO - return either truth or false for deleting the result - running a MySQL query
		try{
			$dbh = Engine::getDatabase();
			$sql = "DELETE FROM Results WHERE Result_ID = :result AND Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "')";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array(':result'=> $Result_ID));
			return true;
		} catch (PDOException $e){
			return false;
		}
	}
	public function load($Result_ID){
		//TODO - return either null or a Result object.
		//Get the database
		$dbh = Engine::getDatabase();
		$sql = "SELECT 
				Results.Result_ID, 
				UNIX_TIMESTAMP(Results.DATE) AS Date, 
				Results.Data, 
				Results.Visible, 
				Users.Name,
				Users.User_ID
			FROM Results 
				INNER JOIN Result_History
					ON Results.Result_ID=Result_History.Result_ID
				INNER JOIN Users
					ON Result_History.User_ID=Users.User_ID 
			WHERE 
				Results.Result_ID = :result AND 
				(Results.Visible " . (User::instance()->isLoggedIn() ? "OR Users.User_ID='" . User::instance()->id . "'" : "") . ") 
			LIMIT 1
		";
		$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':result'=> $Result_ID));
		//Get the result into an object
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result){
			(new ReflectiveObject())->copy($result, $this);
			$this->Data = json_decode($this->Data, true);
			return true;
		} else {
			return false;
		}
	}

	public function run($template){
		if(isset($_POST['action']) && $_POST['action'] == 'delete'){
			if(self::delete($this->URLMatch[1])){
				header('Location: ' . Engine::getRemoteAbsolutePath((new Account())->getURL()));
			}
		} else if (isset($_POST['action']) && ($_POST['action'] == 'make-public' || $_POST['action'] == 'make-private')){
			$test = self::toggleVisibility($this->URLMatch[1]);
			var_dump($test);
			if($test){
				header('Location: ' . Engine::getRemoteAbsolutePath((new Account())->getURL()));
			}
		}
	}
	public function show($template){
		//Retrieve the result 
		$Result_ID = $this->URLMatch[1];
		$loaded = $this->load($Result_ID);
		include(__DIR__ . "/../pages/section/header.php");
		if($loaded){
			include(__DIR__ . "/../pages/section/middle_result.php");
		} else {
			include(__DIR__ . "/../pages/section/middle_result_invalid.php");
		}
		
		include(__DIR__ . "/../pages/section/footer.php");
	}
	public function isCorrupt(){
		//Checks whether the result is corrupt
		//TODO: Make a better test system that checks to make sure an analysis result can be read or not.
		foreach ($this->mustExist as $requirement){
			if(!array_key_exists($requirement, $this->Data)){
				return true;
			}
		}
		return false;
	}
	public function isPublicViewer(){
		return $this->User_ID != User::instance()->id;
	}
	private function getPronoun(){
		//TODO: if viewing publically, change the result to the user's analysis name
		$firstName = explode(" ", $this->Name)[0];
		return ($this->isPublicViewer() ? (Engine::endsWith($firstName, "s") ? $firstName . "'" : $firstName . "'s") : "Your");
	}
	private function getRating(){
		//TODO: Make a rating system for each analysis result so the user can give us feedback
		//Either show if public what the user rated this as or create a rate system in a presentable HTML format that the user can review.
		/*
		$starRatingControl = "<div class='rating'></div>";
		$control = "";
		if (1==2){ //TODO: if rating has been completed by the user
			$editable = true; //TODO: if editable, allow the user to edit, otherwise say that it was rated.
			$rateable = "<div class='rated'>You have already rated this but you can change your mind if you wish</div>";
			$unratable = "<div class='rated'>Sorry, but this analysis is too old to be rated.</div>";
			$control = ($editable ? $rateable : $unratable);
		} else {

		}
		return "<div class='rate'><sub>Ratings will be unavailable after 24 hours have elapsed.</sub></div>";
		*/
	}
	private function getShareButton(){
		$appID = SDK::instance()->facebook->getApp()->getId();
		return "
			<div id='fb-root'></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = '//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.6&appId=" . $appID . "';
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<!-- Your share button code -->
			<div align='right'>
				<div class='fb-share-button' data-href='" . $this->Data['share-url'] . "'data-layout='button' data-mobile-iframe='false'></div>
			</div>
		";
	}
	public function getHoroscope(){
		$strengths = $this->Data['horoscope']['zodiac']['strengths'];
		$lastStrength = array_pop($strengths);
		$firstName = explode(" ", $this->Name)[0];
		$strengths = count($strengths) ? implode($strengths, ", ") . ", and " . $lastStrength : $lastStrength;
		$weaknesses = $this->Data['horoscope']['zodiac']['weaknesses'];
		$lastWeakness = array_pop($weaknesses);
		$weaknesses =  count($weaknesses) ? implode($weaknesses, ", ") . " and " . $lastWeakness : $lastWeakness;
		return "<h5>" . $this->getPronoun() . " personality</h5><p>" .  $this->Data['horoscope']['zodiac']['description'] . "</p> <p><b>TLDR? " . ($this->isPublicViewer() ? $firstName . " is" : "you are") . " " . strtolower($strengths) . " but at times " . ($this->isPublicViewer() ? "" : "you ") . "can also be " . strtolower($weaknesses) . ".</b></p><h5>" . ($this->isPublicViewer() ? $this->getPronoun() . " insight for today"  : "Today's insight") . "</h5><p>" . $this->Data['horoscope']['insight'] . "</p>";
	}
	private function getHoroscopeFact(){
		//Returns the DOB used in a presentable HTML format
		return "
			<h5>Horoscope</h5>
			<p>" . $this->getPronoun() . " zodiac sign must be '" . $this->Data['horoscope']['zodiac']['name'] . "' because " . ($this->isPublicViewer() ? "their" : "your") . " date of birth falls between " . $this->Data['horoscope']['zodiac']['start-date'] . " and " . $this->Data['horoscope']['zodiac']['end-date'] . ".";
	}
	private function getAveragePost(){
		return "
			<h5>Activity</h5>
			<p>An average created post takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['posts']['mean'])) . "</p>
			<p>" . $this->getPronoun() . " next post will most likely be created in " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['posts']['prediction'])) . "</p>
			<p>An average created photo takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['photos']['mean'])) . "</p>
			<p>" . $this->getPronoun() . " next photo will most likely be in " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['photos']['prediction'])) . "</p>
			<p>An average liked page takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['likes']['mean'])) . "</p>
			<p>An average created video takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['videos']['mean'])) . "</p>
			<p>An average liked movie takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['movies']['mean'])) . "</p>
			<p>An average liked music artist takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['music']['mean'])) . "</p>
			<p>An average liked book takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['books']['mean'])) . "</p>";
	}
	private function getTopThreeFriends() {
		//Returns the first three top friends from all categories in a presentable HTML format
		$userIDs = array_keys($this->Data['interaction']);
		return "
			<div class='topThree'>
				<h5>" . $this->getPronoun() . " top three friends!</h5>
				<div class='friend second'>
					<div class='avatar' style='background: url(\"" . User::getAvatar($userIDs[1]) . "\") no-repeat; background-position: center; background-size: cover;'>
						<span>2<sup>nd</sup></span>
					</div>
					<p>" . $this->Data['interaction'][$userIDs[1]]['name'] . "</p>
				</div>
				<div class='friend first starred'>
					<div class='avatar' style='background: url(\"" . User::getAvatar($userIDs[0]) . "\") no-repeat; background-position: center; background-size: cover;'>
					<span>1<sup>st</sup></span>
					</div>
					<p>" . $this->Data['interaction'][$userIDs[0]]['name'] . "</p>
				</div>
				<div class='friend third'>
					<div class='avatar' style='background: url(\"" . User::getAvatar($userIDs[2]) . "\") no-repeat; background-position: center; background-size: cover;'>
						<span>3<sup>rd</sup></span>
					</div>
					<p>" . $this->Data['interaction'][$userIDs[2]]['name'] . "</p>
				</div>
			</div>
		";
	}
	private function getTopThreeCatergory(){
		//Returns three friends that are the first in the 3 categories in a presentable HTML format
		$topLike = null;
		$topComment = null;
		$topTag = null;
		foreach($this->Data['interaction'] as $id=>$person){
			if($topLike['likes'] < $person['likes']){
				$topLike = array_merge($person, array('id'=>$id));
			}
			if($topComment['comments'] < $person['comments']){
				$topComment = array_merge($person, array('id'=>$id));
			}
			if($topTag['tags'] < $person['tags']){
				$topTag = array_merge($person, array('id'=>$id));
			}
		}
		$result = "";
		if(!is_null($topLike)){
			$result .= "
				<div class='friend starred'>
					<p><b>Likes</b></p>
					<div class='avatar' style='background: url(\"" . User::getAvatar($topLike['id']) . "\") no-repeat; background-position: center; background-size: cover;'>
						<span>1<sup>st</sup></span>
					</div>
					<p>" . $topLike['name'] . "</p>
				</div>
			";
		}
		if(!is_null($topComment)){
			$result .= "
				<div class='friend starred'>
				<p><b>Comments</b></p>
					<div class='avatar' style='background: url(\"" . User::getAvatar($topComment['id']) . "\") no-repeat; background-position: center; background-size: cover;'>
					<span>1<sup>st</sup></span>
					</div>
					<p>" . $topComment['name'] . "</p>
				</div>
			";
		}
		if(!is_null($topTag)){
			$result .= "
				<div class='friend starred'>
					<p><b>Tagged</b></p>
					<div class='avatar' style='background: url(\"" . User::getAvatar($topTag['id']) . "\") no-repeat; background-position: center; background-size: cover;'>
						<span>1<sup>st</sup></span>
					</div>
					<p>" . $topTag['name'] . "</p>
				</div>
			";
		}
		if($result != ""){
			$result = "
				<div class='topThree category'>
				<h5>" . $this->getPronoun() . " top interactive friends!</h5>
				$result
				</div>
			";
		}
		return $result;
	}
	private function getInteractionTable(){
		//Returns a presentable HTML table of the user's friend interaction
		$warning = "These numbers indicate scores and do not refer to the amount found";
		$result = "
			<h5>Interaction</h5>
			<p></p>
			<table class='facts interaction' border='0'>
				<tr>
					<th></th>
					<th title='$warning'>Likes<b>*</b></th>
					<th title='$warning'>Comments<b>*</b></th>
					<th title='$warning'>Tags<b>*</b></th>
				</tr>
		";
		foreach ($this->Data['interaction'] as $id=>$person){
			$name = $person['name'];
			$result .= "
				<tr>
					<td><a href='https://facebook.com/$id' target='_blank' title=\"Go to " . (Engine::endsWith($name, "s") ? $name . "'" : $name . "'s") . " profile\">$name</a></td>\t
					<td>" . $person['likes'] . "</td>\t
					<td>" . $person['comments'] . "</td>\t
					<td>" . $person['tags'] . "</td>\t
				</tr>
			";
		}
		return $result . "
			</table>
			<p><i><b>*</b> $warning</i></p>
		";
	}
	//Thanks to http://php.net/manual/en/function.time.php#Hcom108581
	public function getTimeDifferenceShort($secs){
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
	public function getTimeDifferenceLong($secs){
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
		
		return join(' ', $ret);
	}
	public function getTimeDifferenceApproximate($secs){
		$bit = array(
			' year' => $secs / 31556926 % 12,
			' week' => $secs / 604800 % 52,
			' day' => $secs / 86400 % 7,
			' hour' => $secs / 3600 % 24,
			' minute' => $secs / 60 % 60,
			' second' => $secs % 60
		);

		foreach($bit as $k => $v){
			if($v > 0){
				$ret[] = $v . ($v == 1 ? $k : $k . "s");
				return join(' ', $ret);
			}
		}
	}

}
?>