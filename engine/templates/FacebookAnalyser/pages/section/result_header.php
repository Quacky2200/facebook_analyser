<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<?php if($this->result){?>
			<meta property="og:url"           <?php echo "content=\"" . $this->result->Data['share-url'] . "\"";?> />
			<meta property="og:type"          content="article" />
			<meta property="og:title"         <?php echo "content=\"" . $this->result->Data['share-title'] . "\"";?> />
			<meta property="og:description"   <?php echo "content=\"" . $this->result->Data['share-description'] . "\"";?> />
			<meta property="og:image"         <?php echo "content=\"" . $this->result->Data['share-image-url'] . "\"";?> />
		<?php } ?>
		<meta name="description" content="FaceBook Analyser - What type of Facebook user are you?">
		<meta name="keywords" content="facebook, analyser, cardiff, uni, university, group, project">
		<meta name="viewport" content="width=720, maximum-scale=1.0" />
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
			if ($this->result){
		?>
			
		<?php }?>
	</head>
	<body>
