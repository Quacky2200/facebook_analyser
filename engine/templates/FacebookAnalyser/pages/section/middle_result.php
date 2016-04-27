<?php require(__DIR__ . '/top_account_header.php');?>
	<main class="settings">
		<div class="container">
			<?php if ($this->result->Visible && !$this->isViewingPublic && !$this->result->isCorrupt()){
				include(__DIR__ . '/middle_facebook_share_result.php');
			}
			?>
			<div class="result information">
			<?php
			
			if($this->result->isCorrupt()){
				$link = Engine::getRemoteAbsolutePath(($this->isViewingPublic ? (new Home())->getURL() : (new Account())->getURL()));
				echo "<h2 align='center'>Analysis incomplete</h2><div align='center' style='padding: 10px'>
						<div class='text'>
							<p>We weren't able to display this result as this analysis was either; unable to catch enough data to provide a result or the result conforms to an old format that our website is unable to read.</p>
							<a href='$link'>Take me back " . ($this->isViewingPublic ? "home" : "to my account") . ", please</a>
							<!--<div style='background: rgb(240,240,240);border-radius: 5px;display:inline-block; padding: 1vh 1vw; margin:auto'>This data doesn't meet the requirements of our analyis.</div>-->
						</div>
					</div>
				";
				exit();
			}?>
			<h2 align='center'><?php echo $this->result->getPronoun(); ?> Result</h2>
			<h6 align='center'>Created <?php echo $this->result->getTimeDifferenceApproximate(time() - $this->result->Date) . " ago";?></h6>
			<div class='text'>
				<?php
				//TODO: if viewing as public, change from 2nd person to 3rd person
				//E.g. Your Result => Matthew's Result
				echo $this->result->getReadable($this) . $this->result->getFacts($this);
				
				if ($this->result->Visible && !$this->isViewingPublic){
					include(__DIR__ . '/middle_facebook_share_result.php');
				}
				?>
			</div>
		</div>
	</main>