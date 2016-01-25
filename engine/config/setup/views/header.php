<html>
<head>
	<?php
	// die(get_current_user());
	$this->getGeneratedTitle($Template, true);
		//Assume we're in our public directory.
		$Template->addMultipleCSS(array(
			"css/opensans/stylesheet.css",
			"css/main.css"
		), true);
		$Template->addMultipleJS(array(
			"js/jquery-1.11.2.min.js",
			"js/jquery-1.11.2.min.js",
			"js/jquery-migrate-1.2.1.min.js",
			"js/script.js"
		), true);
		$Template->echoCSSAndJSDependencies();
	?>
</head>
<body>