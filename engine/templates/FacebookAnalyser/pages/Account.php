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
		if(isset($_POST['action']) && $_POST['action'] == "confirm" && isset($_POST['confirm']) && $_POST['confirm'] == "Yes, delete my account"){
			$this->deleteAccount(Engine::getDatabase());
		}
		if (isset($this->URLMatch[2]) && $this->URLMatch[2] == "delete") {
			include("section/middle_user_deletion.php");
		} else {
			include("section/middle_account.php");
		}
		include("section/footer.php");
	}

	private function deleteResults($dbh) {
		try {
			$sql = "DELETE FROM Result_History WHERE USER_ID='" . User::instance()->id . "'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
		} catch (PDOException $e){
			throw new Exception(400, "Invalid request");
		}
	}

	private function deleteAccount($dbh) {
		$this->deleteResults($dbh);
		try {
			$sql = "DELETE FROM Users WHERE USER_ID='" . User::instance()->id . "'";
			$stmt = $dbh->prepare($sql);
			$stmt->execute();
			Engine::clearSession();
			header('Location: ' . Engine::getRemoteAbsolutePath((new Home())->getURL()));
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
			$timeSinceCreation = (stripos($timeSinceCreation, "second") > -1 || !$timeSinceCreation ? " just now" : $timeSinceCreation . " ago");
			$resultLink = Engine::getRemoteAbsolutePath($obj->getURL());
			$resultActionDelete = null;
			$resultActionShare = null;
			?>
			<script>
				function updateActions(sender){
					//if(this === window) return;
					//alert($(sender).html());
					var url = $(sender).parentsUntil('div.result span.actions').attr('action');
					$.ajax({
						type: 'POST',
						url: url,
						data: {'action':$(sender).attr('data-action')},
						complete: function(){url = this.url;},
						success: function(data){
							if($('.results', $(data)) == null){
								alert(window.location.href + " \n" + url)
								alert("We were unable to fulfil your request.");
								return;
							}
							updateResults();
						}
					});
				}
				function updateResults(){
					$.ajax({
						type: 'GET',
						url: window.location.href,
						success: function(data){
							if($('.results', $(data)) == null){return};
							if($('.results', $(data)) !== $('.results')){
								$('.result').remove();
								$('.results').append($('.result', $(data)));
							}
						}
					});
				}
				setInterval(updateResults, 30000);
			</script>
			<div class="result" <?php echo "id='" . $obj->Result_ID . "'";?>>
				<span class="actions">
					<form method='post' <?php echo "action='" . $resultLink . "'";?>>
						<a href='javascript:void(0);' <?php echo "data-action='make-" . ($obj->Visible ? "private" : "public") . "'";?> <?php echo "data-action-done='We have now made the result " . ($obj->Visible ? "private" : "public") . ".'";?> onclick='updateActions(this);' title='Change the result visibility'>
							<i <?php echo "class=\"fa fa-" . ($obj->Visible ? "lock" : "unlock-alt") . "\"";?> aria-hidden="true"></i>
						</a>
						<a href='javascript:void(0);' data-action='delete' data-action-done='We have deleted this result.' onclick='updateActions(this);' title='Delete this result'>
							<i class="fa fa-times" aria-hidden="true"></i>
						</a>
					</form>
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