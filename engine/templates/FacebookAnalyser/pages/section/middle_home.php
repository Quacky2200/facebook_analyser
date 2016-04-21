<main class="centerstage">
<header title>
	<span style='display:inline-block;margin:5% 0;'>
		<h1 style='margin:0;font-weight:400;letter-spacing:-5px'>
			<img id='logo' <?php echo "src='" . Engine::getRemoteAbsolutePath($template->getLocalDir() . '/public/images/white-logo-transparent.png') . "'";?> height="128"/>
			FacebookAnalyser
		</h1>
		<div align='right'>
			<span>What type of Facebook user are you?</span>
		</div>
	</span>
	<h5 style='margin:0;font-weight:100'>Analyse today</h5>
	<?php echo $this->loginButton;?>
</header>
</main>