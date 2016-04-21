<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="description" content="">
		<meta name="keywords" content="FaceBook Analyser - What type of Facebook user are you?">
		<meta http-equiv="X-UA-Compatible" content="IE=9">
		<meta http-equiv="X-UA-Compatible" content="IE=8">
		<?php
			echo $this->getGeneratedTitle($template, true);
			//Assume we're in our public directory
			$template->addMultipleCSS(array(
				"css/main.css",
				"css/opensans/stylesheet.css",
				"https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css",
				"css/social-buttons.css",
				"css/centerstage.css"
			));
			$template->addMultipleJS(array(
				"js/jquery-1.11.2.min.js",
			));
			$template->echoCSSAndJSDependencies();
		?>
	</head>
	<body>
			