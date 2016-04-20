<?php if (!$this->result && !$this->deleted){
	ErrorHandler::primitiveError(400, "Invalid Request", "");
}
require(__DIR__ . '/top_account_header.php');
?>
	<main class="settings">
		<div class="container">
			<?php if($this->deleted){ ?>
				<h2 align='center' style='padding:100px 0;'>Your result was deleted</h2>
				<p align='center' style='padding: 0 0 50px'><a <?php echo "href='" . Engine::getRemoteAbsolutePath((new Account())->getURL()) . "'"?>>Take me back to my account</a></p>
			<?php die();
			} else if ($this->shared){ ?>

			<?php } ?>
			<div class="result information">
				<h2 align="center">Your Result</h2>
				<h6 align="center"><?php echo "Created " . $this->result->getTimeElapsedApproximate($this->result->Date);?></h6>
				<div class="text">
					<?php
						//TODO: if viewing as public, change from 2nd person to 3rd person
						//E.g. Your Result => Matthew's Result
						echo $this->result->getReadable() . $this->result->getFacts();
					?>
				</div>
		</div>
	</main>