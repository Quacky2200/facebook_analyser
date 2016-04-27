<?php
require(__DIR__ . '/analysis/PostInteraction.php');
require(__DIR__ . '/analysis/ActivityAnalysis.php');
require(__DIR__ . '/analysis/HoroscopeAnalysis.php');
class AsyncAnalysisWorker{
	//IN is the data we're getting from Facebook, INTO the analysis
	const IN = "extracted";
	//OUT is the data we find out from the analysis and what will be used in the results
	const OUT = "results";

	//Create an array we can use to store our data
	private $data = array(
		self::IN => array(),
		self::OUT => array()
	);

	//The asynchronous methods used to gain an analysis
	private $asyncFunctions;

	public function __construct(){
		//Create the functions we want to use, the anonymous functions can ONLY be created at class initialisation
		$this->asyncFunctions = array(
			//Grab the data
			"Getting profile data" => function(){
				$this->getUserDataFromUserFunction('getUserProfile');
			},
			"Getting user posts" => function(){
				$this->getUserDataFromUserFunction('getUserPosts');
			},
			"Getting liked pages" => function(){
				$this->getUserDataFromUserFunction('getUserLikes');
			},
			"Getting user photos" => function(){
				$this->getUserDataFromUserFunction('getUserPhotos');
			},
			"Getting user videos" => function(){
				$this->getUserDataFromUserFunction('getUserVideos');
			},
			"Getting liked movies" => function(){
				$this->getUserDataFromUserFunction('getUserMovies');
			},
			"Getting liked music" => function(){
				$this->getUserDataFromUserFunction('getUserMusic');
			},
			"Getting liked books" => function(){
				$this->getUserDataFromUserFunction('getUserBooks');
			},
			//Now for the analysis
			"Analysing post interaction" => function(){
				$interaction = new PostInteraction();
				$this->data[self::OUT]['interaction'] = $interaction->analyse($this->data[self::IN]);
			},
			"Analysing activity" => function(){
				//Apply another analysis
				$activity = new ActivityAnalysis();
				$this->data[self::OUT]['activity'] = $activity->analyse($this->data[self::IN]);
			},
			"Analysing birthdate" => function(){
				//Apply horoscope analysis
				$horoscope = new HoroscopeAnalysis();
				$this->data[self::OUT]['horoscope'] = $horoscope->analyse($this->data[self::IN]);
			},
			"Creating analysis" => function(){
				//Save *other* information for the Facebook share button
				//Save the user's name - we can get this in the database but this is just easier for now.
				$this->data[self::OUT]['name'] = User::instance()->name;
				//Generate a code we can use to show the result to the user
				$this->data[self::OUT]['analysis-id'] = Engine::generateRandomString(8);
				$this->data[self::OUT]['share-url'] = Engine::getRemoteAbsolutePath('/public/' . (new Results())->getURL() . $this->data[self::OUT]['analysis-id']);
				$this->data[self::OUT]['share-title'] = "Click to see my analysis!";
				$this->data[self::OUT]['share-description'] =  User::instance()->name . " has shared a Facebook Analysis with you. Click to see their result or create your own... What type of Facebook user are you?";

				$this->data[self::OUT]['share-image-url'] = Engine::getRemoteAbsolutePath((new Results())->getURL() . $this->data[self::OUT]['analysis-id'] . '/image/');
			}
		);
	}

	//Get data we will require from Facebook and automatically fill the array for analysis and outputting the data as a generic object (an array)
	private function getUserDataFromUserFunction($function){
		$this->data[self::IN] = array_merge($this->data[self::IN], User::Instance()->{$function}()->getGraphUser()->asArray());
	}

	private function startAsync(){
		foreach($this->asyncFunctions as $key=>$value){
			set_time_limit(0);
			//Say the action we're taking to the user
			$this->echoImplicit($key);
			//Do the action we wish to accomplish
			call_user_func($value);
		}
	}

	public function echoImplicit($text){
		//Send a message if they have JavaScript enabled.
		echo "<script id=\"remove\">$('.loaderDescription').append($('<p>$text</p>'));</script>";
		//Flush the message to show the message to the user
		for($k = 0; $k < 40000; $k++) echo ' ';
	}

	public function echoImplicitFinishAndRedirect($URL){
		//Finish up by either redirection with JavaScript, or without JavaScript, to view the result.
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
		//Stop us having to flush our output
		ob_implicit_flush(false);
		//We're also finished now.
		exit();
	}

	public function run(){
		//Tell the user we're going to use the API to get the user's data
		$this->echoImplicit("Preparing...");
		//Start working
		$this->startAsync();
		$resultID = $this->data[self::OUT]['analysis-id'];
		$resultURL = Engine::getRemoteAbsolutePath((new Results())->getURL() . $this->data[self::OUT]['analysis-id']);
		//Get the database connection so we can upload the result
		$this->dbh = Engine::getDatabase();
		//Make sure that if we get any errors, that we get told about them.
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//Insert the data into the results first
		$sql = "INSERT INTO Results (Result_ID, Date, Data, Visible) VALUES (:result, NOW(), :data, true)";
		$stmt = $this->dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$stmt->execute(array(':result'=>$resultID, ':data'=> json_encode($this->data[self::OUT])));
		//Add the relationship between the result and the user
		$this->dbh->exec("INSERT INTO Result_History (User_ID, Result_ID) VALUES ('" . User::instance()->id . "', '" . $resultID . "')");
		//Tell the user that we're finished
		$this->echoImplicit("All finished!");
		//Stop processing the page and redirect the user
		$this->echoImplicitFinishAndRedirect($resultURL);
	}

}
?>