<header title>
	<span style='display:inline-block;margin:5% 0;'>
		<h1 style='margin:0'>FacebookAnalyser</h1>
		<div align='right'>
			<span>What type of Facebook user are you?</span>
		</div>
	</span>
	<h4 style='margin:0;font-weight:100'>Analyse today</h4>
	<?php
		$authenticatePageURL = Engine::getRemoteAbsolutePath((new Analyse())->getURL());
		$FacebookAuthenticationURL = User::instance()->getFacebookAuthURL($authenticatePageURL);
		$title = "<b>Login</b> with <b>Facebok</b>";
		if(User::instance()->isLoggedIn() && User::instance()->loadProfile()){
			$FacebookAuthenticationURL = $authenticatePageURL;
			$title = "<b>Continue</b> as " . User::instance()->name . "<b></b>";
		}
	?>
	<a class='fblogin' <?php echo "href='$FacebookAuthenticationURL'";?>>
		<i class="fa fa-facebook"></i>
		<span><?php echo $title;?></span>
	</a>
</header>