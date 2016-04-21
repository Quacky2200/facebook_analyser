<?php
class Account extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/account/";
	}
	public function isMatch($URL){
		return $URL == $this->getURL();
	}
	public function run($template){
		require("login.php");
	}
	public function show($template){
		include("section/header.php");
		include("section/middle_account.php");
		include("section/footer.php");
	}
	public function getAllResultHistory(){
		$dbh = Engine::getDatabase();
		echo "<div class='result'><a href='" . Engine::getRemoteAbsolutePath((new Analyse())->getNewAnalysisURL()) . "'><div><i class='fa fa-plus' style='font-size: 60px;'></i></div></a><span>Create Analysis</span></div>";
		$query = $dbh->query("SELECT Result_ID, UNIX_TIMESTAMP( DATE ) \"Date\", Data FROM Results WHERE Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "') ORDER BY Date DESC");
		foreach($query->fetchALL(PDO::FETCH_CLASS, 'Result') as $obj){
			echo "<div class='result'><a href='" . Engine::getRemoteAbsolutePath((new Results())->getURL() . $obj->Result_ID) . "'><div></div><span>Created<br/>" . $obj->getTimeElapsedApproximate(time() - $obj->Date) . "</span></a></div>";
		}
	}
}
?>