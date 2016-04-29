<?php require(__DIR__ . '/top_account_header.php');?>
<main class="settings">
	<div class="container">
		<div class='result information'>
			<?php if ($this->isCorrupt()){
				$link = Engine::getRemoteAbsolutePath(($this->isPublicUser ? (new Home())->getURL() : (new Account())->getURL()));
			?>
					<h2 align='center'>Analysis incomplete</h2><div align='center' style='padding: 10px'>
					<div class='text'>
						<p>We weren't able to display this result as this analysis was either; unable to catch enough data to provide a result or the result conforms to an old format that our website is unable to read.</p>
						<?php echo "<a href='$link'>Take me back " . ($this->isPublicViewer() ? "home" : "to my account") . ", please</a>";?>
						<!--<div style='background: rgb(240,240,240);border-radius: 5px;display:inline-block; padding: 1vh 1vw; margin:auto'>This data doesn't meet the requirements of our analyis.</div>-->
					</div>
			<?php } else {
				$timeSinceCreation = $this->getTimeDifferenceApproximate(time() - $this->Date);
				$timeSinceCreation = (stripos($timeSinceCreation, "second") > -1 || !$timeSinceCreation ? " just now" : $timeSinceCreation . " ago");
				echo ($this->Visible && !$this->isPublicViewer() ? $this->getShareButton() : "") . 
					"<h2 align='center'>" . $this->getPronoun() . " Result</h2>
					<h6 align='center'>Created " . $timeSinceCreation . "</h6>
					<div class='text'>" . 
						$this->getTopThreeFriends() . 
						$this->getTopThreeCatergory() . 
						$this->getHoroscope() . " 
						<div>
							<h3 align='center'> Facts </h3>
							<br/>
							<div>" . 
							$this->getAveragePost() . 
							$this->getInteractionTable() .
							$this->getHoroscopeFact() . "
							</div>
						</div>
					</div>
				";
			}
			?>
		</div>
	</div>
</main>