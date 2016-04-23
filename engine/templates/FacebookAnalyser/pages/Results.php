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
	public function isMatch($URL){
		/*
			This regex is to match an example like:
				/result/a8fjS8wK
				/result/a8fjS8wK/
				/public/result/a8fjS8wK/
				/result/a8fjS8wK/delete
				/result/a8fjS8wK/share
		*/
		return preg_match("/^(?:\/(public))?\/(?:result)\/(\w{8})(?:\/(delete|share|unshare))?[\/]?$/", $URL, $this->URLMatch);
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
		} catch (PDOException $e){
			throw new Exception(400, "Invalid request");
		}
	}

	public function changeVisibility($resultID, $visible){
		//Try sharing the result
		try{
			$dbh = Engine::getDatabase();
			$sql = "UPDATE Results SET Visible=$visible WHERE Result_ID = :result AND Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "')";
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array(':result'=> $resultID));
		} catch (PDOException $e){
			throw new Exception(400, "Invalid Request");
		}
	}
	public $isViewingPublic;
	public function run($template){
		//Remove the whole string as the first result
		array_shift($this->URLMatch);
		//Get the database
		$dbh = Engine::getDatabase();
		//Are we visiting a public result?
		$this->isViewingPublic = in_array("public", $this->URLMatch);
		$resultID = $this->URLMatch[1];
		$isAction = (count($this->URLMatch) > 2 ? in_array($this->URLMatch[2], array("delete", "share", "unshare")) : false);
		$isLoggedIn = User::instance()->isLoggedIn();
		//Make sure we're only running actions with non-public requests.
		if($this->isViewingPublic && !$isAction || $isLoggedIn){
			$sql = "SELECT Result_ID, UNIX_TIMESTAMP( DATE ) \"Date\", Data, Visible FROM Results WHERE Result_ID = :result AND " . 
				($this->isViewingPublic ? "Visible" : "Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "')") . 
				" LIMIT 1";
			$stmt = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$stmt->execute(array(':result'=> $resultID));
			//Apply an action if there is one
			$result = $stmt->fetchObject('Result');
			$result->Data = json_decode($result->Data, true);
			if($result && $isAction){
				if($this->URLMatch[2] == "delete"){
					$this->deleteResult($resultID);
				} //else if (in_array($this->URLMatch[2], array("share", "unshare"))){
					//$this->result = $result;
					//$this->changeVisibility($resultID, $this->URLMatch[2] == "share");
				//}
			} else {
				//Set the result
				$this->result = $result;
			}
		}
	}
	public function show($template){
		include("section/result_header.php");
		include("section/middle_result.php");
		include("section/footer.php");
	}


}
?>