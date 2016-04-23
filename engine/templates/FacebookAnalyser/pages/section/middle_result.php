<?php if (!$this->result && !$this->deleted){
?>
	<main class="centerstage">
		<header title="">
			<span style="display:inline-block;margin: 5% 0;">
				<h1 style="margin:0">Invalid Request</h1>
				<p><a <?php echo "href='" . Engine::getRemoteAbsolutePath((new Home())->getURL()) . "'";?> title="Go back home">Take me home, please</a></p>
			</span>
		</header>
	</main>
<?php
exit();
}
require(__DIR__ . '/top_account_header.php');
?>
	<main class="settings">
		<div class="container">
			<?php if($this->deleted){ ?>
				<h2 align='center' style='padding:100px 0;'>Your result was deleted</h2>
				<p align='center' style='padding: 0 0 50px'><a <?php echo "href='" . Engine::getRemoteAbsolutePath((new Account())->getURL()) . "'"?>>Take me back to my account</a></p>
			<?php die();
			}
			if ($this->result->Visible && !$this->isViewingPublic){
				//TODO: Output FB share button and make private
				include(__DIR__ . '/middle_facebook_share_result.php');
			}
			/*else if (!$this->result->Visible && !$this->isViewingPublic){
				//TODO: Show make public
				<!--<a <?php echo "href='" . $this->result->Data['share-url'] . "/unshare'"?> title='Make this result private' class='button'>Make private</a>-->
				echo "<a href='" . $this->result->Data['share-url'] . "/share' title='Make this result public' class='button'>Make public</a>";
			}*/
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
					//echo for the bottom AGAIN :)
					include(__DIR__ . '/middle_facebook_share_result.php');
				}
				?>
			</div>
		</div>
	</main>