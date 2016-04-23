<?php
class Result{
	public $Result_ID, $Date, $Data, $Visible;
	/*
		This class needs a lot of work to make the analysis to be finalised 
		so that it will work in tandem with derived AnalysisElement classes and the
		AsyncAnalysisWorker

		We can currently only look at:
		-> Friendship by post interaction [DONE]
		-> Time of posts (perhaps likes on pages/movies) [DONE]
		-> Privacy of posts
		-> Amount of privacy based on user profile details

		We then need to write down the analysis in a readable format. [DONE]

		Should we have Facebook user categories because of their interaction with Facebook?

		Should we have a trophy system to award the user/friends based on their result?

		Should we have a Top 3 friend display to show who's the most loyal friend? [DONE]

	*/
	private $pageObj;
	public function getReadable($pageObj){
		$this->pageObj = $pageObj;
		//Returns a user readable result based from the raw data.
		return $this->topThree() . $this->topThreeCatergory() . "
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc elit mauris, pretium id nisl nec, interdum laoreet nulla. Donec quis pretium sapien. Mauris condimentum at sem id pharetra. Integer non dui at elit elementum dictum. Donec auctor libero at sapien pharetra semper. Nulla maximus metus eros, ac mattis leo fermentum nec. Sed urna sem, finibus pellentesque ipsum vel, pellentesque varius dolor.</p>
			
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc elit mauris, pretium id nisl nec, interdum laoreet nulla. Donec quis pretium sapien. Mauris condimentum at sem id pharetra. Integer non dui at elit elementum dictum. Donec auctor libero at sapien pharetra semper. Nulla maximus metus eros, ac mattis leo fermentum nec. Sed urna sem, finibus pellentesque ipsum vel, pellentesque varius dolor.</p>
		";
	}
	public function getFacts($pageObj){
		$this->pageObj = $pageObj;
		//Returns the raw data that *should* still be formatted in a presentable HTML format
		return "
			<div>
				<h3 align='center'> Facts </h3>
				<br/>
				<div>" . 
				$this->averagePost() . 
				$this->tableOfInteraction() .
				"
					<!--<div align='center' style='padding: 10px'><div style='background: rgb(240,240,240);border-radius: 5px;display:inline-block; padding: 1vh 1vw; margin:auto'> No data is available at this time</div></div>-->
				</div>
			</div>
		";
	}
	private function getRating(){
		if ($this->pageObj->isViewingPublic) return; //Never show any kind of rating control when we're running publicly.
		//Either show if public what the user rated this as or create a rate system in a presentable HTML format that the user can review.
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
	}
	private function horoscopeFact(){
		//Returns the DOB used in a presentable HTML format
		return "
			<h5>Horoscope</h5>
			<p>You must be ";
	}
	private function averagePost(){
		return "
			<h5>Activity</h5>
			<p>An average created post takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['posts']['mean'])) . "</p>
			<p>Your next post will most likely be created in " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['posts']['prediction'])) . "</p>
			<p>An average created photo takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['photos']['mean'])) . "</p>
			<p>Your next photo will most likely be in " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['photos']['prediction'])) . "</p>
			<p>An average liked page takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['likes']['mean'])) . "</p>
			<p>An average created video takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['videos']['mean'])) . "</p>
			<p>An average liked movie takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['movies']['mean'])) . "</p>
			<p>An average liked music artist takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['music']['mean'])) . "</p>
			<p>An average liked book takes " . $this->getTimeDifferenceApproximate(abs($this->Data['activity']['books']['mean'])) . "</p>";
	}
	private function topThree() {
		//Returns the first three top friends from all categories in a presentable HTML format
		$userIDs = array_keys($this->Data['interaction']);
		return "
			<div class='topThree'>
				<h5>Your top three friends!</h5>
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
	private function topThreeCatergory(){
		//Returns three friends that are the first in the 3 categories in a presentable HTML format
		$topLike = array('likes' => 0);
		$topComment = array('comments' => 0);
		$topTag = array('tags' => 0);
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
		return "
			<div class='topThree category'>
				<h5>Your top interactive friends!</h5>
				<div class='friend starred'>
					<p><b>Likes</b></p>
					<div class='avatar' style='background: url(\"" . User::getAvatar($topLike['id']) . "\") no-repeat; background-position: center; background-size: cover;'>
						<span>1<sup>st</sup></span>
					</div>
					<p>" . $topLike['name'] . "</p>
				</div>
				<div class='friend starred'>
				<p><b>Comments</b></p>
					<div class='avatar' style='background: url(\"" . User::getAvatar($topComment['id']) . "\") no-repeat; background-position: center; background-size: cover;'>
					<span>1<sup>st</sup></span>
					</div>
					<p>" . $topComment['name'] . "</p>
				</div>
				<div class='friend starred'>
					<p><b>Tagged</b></p>
					<div class='avatar' style='background: url(\"" . User::getAvatar($topTag['id']) . "\") no-repeat; background-position: center; background-size: cover;'>
						<span>1<sup>st</sup></span>
					</div>
					<p>" . $topTag['name'] . "</p>
				</div>
			</div>
		";
	}
	private function tableOfInteraction(){
		//Returns a presentable HTML table of the user's friend interaction
		$warning = "These numbers indicate scores and do not refer to the amount found";
		$result = "
			<h5>Interaction</h5>
			<p></p>
			<table class='facts interaction' border='0'>
				<tr>
					<th></th>
					<th>Likes</th>
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
	//I should probably deprecate this function...
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