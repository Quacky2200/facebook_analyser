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
	private $template;
	public function show($template){
		$this->template = $template;
		include("section/header.php");
		include("section/middle_account.php");
		include("section/footer.php");
	}
	public function getAllResultHistory(){
		//Get the PDO instance (it's created by the engine for us)
		$dbh = Engine::getDatabase();
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
			$timeSinceCreation = $obj->getTimeElapsedApproximate(time() - $obj->Date);
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