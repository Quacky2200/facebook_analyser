<?php
require(__DIR__ . '/analysis/PostInteraction.php');
class AsyncAnalysisWorker{
	const IN = "extracted";
	const OUT = "results";

	private $data = array(
		self::IN => array(),
		self::OUT => array()
	);

	private $asyncGetData;

	private $asyncAnalyseData;

	public function __construct(){
		$this->asyncGetData = array(
			"Getting profile data" => function(){
				$this->getUserDataFromUserFunction('getUserProfile');
			},
			"Getting user posts" => function(){
				$this->getUserDataFromUserFunction('getUserPosts');
			}
		);

		$this->asyncAnalyseData = array(
			"Analysing post interaction" => function(){
				$interaction = new PostInteraction();
				$this->data[self::OUT]['interaction'] = $interaction->analyse($this->data[self::IN]);
			}
		);
	}

	private function getUserDataFromUserFunction($function){
		$this->data[self::IN] = array_merge($this->data[self::IN], User::Instance()->{$function}()->getGraphUser()->asArray());
	}

	public function retrieveAll(){
		foreach($this->asyncGetData as $key=>$value){
			set_time_limit(0);
			//Say the action we're taking to the user
			$this->echoImplicit($key);
			//Do the action we wish to accomplish
			call_user_func($value);
		}
	}

	public function analyseAll(){
		foreach($this->asyncAnalyseData as $key=>$value){
			set_time_limit(0);
			//Say what we're analysing
			$this->echoImplicit($key);
			//Analyse it!
			call_user_func($value);
		}
	}

	public function echoImplicit($text){
		//Send a message if they have JavaScript enabled.
		echo "<script id=\"remove\">$('.loaderDescription').append($('<p>$text</p>'));</script>";
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
		ob_implicit_flush(false);
		exit();
	}

	public function run(){
		$this->echoImplicit("Preparing to retrieve data...");
		$this->retrieveAll();
		$this->echoImplicit("Preparing to analyse...");
		$this->analyseAll();
		$this->echoImplicit("All finished!");
		// require '/kint/Kint.class.php';
		// kint::dump($this->data);
		// die();
		$newResultCode = Engine::generateRandomString(8);
		$this->dbh = Engine::getDatabase();
		$this->dbh->exec("INSERT INTO Users (User_ID, Name, Email) VALUES ('" . User::instance()->id . "', '" . User::instance()->name . "', '" . User::instance()->email . "')");
		$this->dbh->exec("INSERT INTO Results (Result_ID, Date, Data, Visible) VALUES ('" . $newResultCode . "', NOW(), '" . json_encode($this->data[self::OUT]) . "', false)");
		$this->dbh->exec("INSERT INTO Result_History (User_ID, Result_ID) VALUES ('" . User::instance()->id . "', '" . $newResultCode . "')");
		$addr = Engine::getRemoteAbsolutePath((new Results())->getURL() . $newResultCode);
		$this->echoImplicitFinishAndRedirect($addr);
	}

}
?>