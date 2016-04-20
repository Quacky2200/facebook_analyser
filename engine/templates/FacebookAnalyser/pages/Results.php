<?php
class Results extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/result/";
	}
	public function getDeleteURL(){

	}
	public function getShareURL(){

	}
	public $result;
	private $URLMatch;
	private $deleted = false;
	private $shared = false;
	public function isMatch($URL){
		/*
			This regex is to match an example like:
				/result/a8fjS8wK
				/result/a8fjS8wK/
				/public/result/a8fjS8wK/
				/result/a8fjS8wK/delete
				/result/a8fjS8wK/share
		*/
		return preg_match("/^(?:\/(public))?\/(?:result)\/(\w{8})(?:\/(delete|share))?[\/]?$/", $URL, $this->URLMatch);
	}
	public function deleteResult($resultID){
		//Try deleting the result
		try{
			$dbh = Engine::getDatabase();
			$sql = "DELETE FROM Results WHERE Result_ID = :result AND Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "')";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array(':result'=> $resultID));
			//Show delete complete?
			$this->deleted = true;
		} catch (PDOException $e){/*Invalid request*/}
	}
	public function shareResult($resultID){
		//Try sharing the result
		try{
			echo "SHARE";
			$dbh = Engine::getDatabase();
			$sql = "UPDATE Results SET Visible=TRUE WHERE Result_ID = :result AND Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "')";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array(':result'=> $resultID));
			//Show the shared URL?
			$this->shared = true;
		} catch (PDOException $e){/*Invalid Request*/}
	}
	public function run($template){
		//Remove the whole string as the first result
		array_shift($this->URLMatch);
		//Get the database
		$dbh = Engine::getDatabase();
		//Are we visiting a public result?
		$viewPublic = in_array("public", $this->URLMatch);
		$resultID = $this->URLMatch[1];
		$isAction = in_array("delete", $this->URLMatch) || in_array("share", $this->URLMatch);
		$isLoggedIn = User::instance()->isLoggedIn();
		//Make sure we're only running actions with non-public requests.
		if($viewPublic && !$isAction || $isLoggedIn){
			$sql = "SELECT * FROM Results WHERE Result_ID = :result AND " . 
				($viewPublic ? "Visible" : "Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "')") . 
				" LIMIT 1";
			$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$stmt->execute(array(':result'=> $resultID));
			//Apply an action if there is one
			$result = $stmt->fetchObject('Result');

			if($result && $isAction){
				if(in_array("delete", $this->URLMatch)){
					$this->deleteResult($resultID);
				} else if (in_array("share", $this->URLMatch)){
					$this->result = $result;
					$this->shareResult($resultID);
				}
			} else {
				//Set the result
				$this->result = $result;
			}
		}
	}
	public function show($template){
		include("section/header.php");
		include("section/middle_result.php");
		include("section/footer.php");
	}


}
?>