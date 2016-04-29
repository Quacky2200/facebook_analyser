	<?php
		require(__DIR__ . '/top_account_header.php');
	?>
	<main class="settings">
		<div class="container">
			<!--<h4>User Account</h4>-->
			<div class="user">
				<?php
				$name = explode(" ", User::instance()->name);
				$pictureStyle =  " style=\"background: url('https://graph.facebook.com/" . User::instance()->id . "/picture?type=large') no-repeat rgb(40,40,40); background-position:center;background-size:cover;\" ";?>			
				<div><div class="accountpicture" <?php echo $pictureStyle;?>></div></div>
				<div><h2>Hey <?php echo substr(User::instance()->name, 0, strpos(User::instance()->name, " "));?>!</h2></div>
			</div>
			<h4>Here's a list of all your results</h4>
			<div class='results'>
				<?php $this->getAllResultHistory($dbh);?>
			</div>
			<div align='right'>
			<a id='delete-account' <?php echo "href='" . Engine::getRemoteAbsolutePath((new Account())->getURL()) . "delete'";?>>Delete account</a>
			</div>
		</div>
	</main>