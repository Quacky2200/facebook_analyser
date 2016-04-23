<?php require(__DIR__ . '/top_account_header.php');?>
	<main class="settings">
		<div class="container">
			<?php if ($this->result->Visible && !$this->isViewingPublic){
				include(__DIR__ . '/middle_facebook_share_result.php');
			}
			?>
			<div class="result information">
			<h2 align='center'>Your Result</h2>
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