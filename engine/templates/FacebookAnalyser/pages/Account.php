<?php
class Account extends Page{
	public function getName(){
		return __CLASS__;
	}
	public function getURL(){
		return "/account/";
	}

	private $URLMatch;

	public function isMatch($URL){
		$match = preg_match("/^\/(account)\/(delete)?$/", $URL, $this->URLMatch);

		return $match;

	}

	public function run($template){
		require("login.php");
	}

	private $template;

	public function show($template){
		$this->template = $template;
		include("section/header.php");
		include("section/middle_account.php");
		include("section/footer.php");
	}

	private function deleteResults($dbh) {
		try {
			$sql = "DELETE FROM `results` WHERE Result_ID IN (SELECT Result_ID FROM result_history WHERE USER_ID = '" . User::instance()->id . "');";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
		} catch (PDOException $e){
			throw new Exception(400, "Invalid request");
		}
	}

	private function deleteAccount($dbh) {
		$this->deleteResults($dbh);
		try {
			$sql = "DELETE FROM `users` WHERE USER_ID = '" . User::instance()->id . "';";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			echo "All your records are being deleted from our database...please wait <meta http-equiv='refresh' content='3;url=". User::instance()->getFacebookDeAuthURL(Engine::getRemoteAbsolutePath((new Home())->getURL())) ."'";
		} catch (PDOException $e){
			throw new Exception(400, "Invalid request");
		} 
	}

	public function getAllResultHistory($dbh){
		//Insert a new analysis button
		?>
		<div class='result'>
			<a <?php echo "href='" . Engine::getRemoteAbsolutePath((new Analyse())->getNewAnalysisURL()) . "'";?> title='Make a new analysis'>
				<div>
					<div>
						<img <?php echo "src='" . Engine::getRemoteAbsolutePath($this->template->getLocalDir() . "/public/images/add1.png") . "'";?>>
					</div>
				</div>
				<span>Create Analysis</span>
			</a>
		</div>
		<?php
		//Get all the results and order them by the date (Most recent come first)
		$query = $dbh->query("SELECT Result_ID, UNIX_TIMESTAMP( DATE ) \"Date\", Data, Visible FROM Results WHERE Result_ID IN (SELECT Result_ID FROM Result_History WHERE User_ID='" . User::instance()->id . "') ORDER BY Date DESC");
		//For all of the analyses, we give a link and the time created.
		foreach($query->fetchALL(PDO::FETCH_CLASS, 'Result') as $obj){
			$timeSinceCreation = $obj->getTimeDifferenceApproximate(time() - $obj->Date);
			$timeSinceCreation = (!$timeSinceCreation ? " just now" : $timeSinceCreation . " ago");
			$resultLink = Engine::getRemoteAbsolutePath((new Results())->getURL() . $obj->Result_ID);
			$resultActionDelete = null;
			$resultActionShare = null;
			?>
			<div class="result">
				<span class="actions">
					<a <?php echo "href='" . $resultLink . "/delete'";?> title='Delete this result'>
						<img <?php echo "src='" . Engine::getRemoteAbsolutePath($this->template->getLocalDir() . "/public/images/delete.png") . "'";?>>
					</a>
				</span>
				<a <?php echo "href='" . $resultLink . "'";?> title='View this result'>
					<div>
						<div>
							<img <?php echo "src='" . Engine::getRemoteAbsolutePath($this->template->getLocalDir() . "/public/images/graph.png") . "'";?>>
						</div>
						<span>Created<br><?php echo $timeSinceCreation;?></span>
					</div>
				</a>
			</div>
			<?php
		}
	}
}
?>