<?php
if(!User::instance()->isLoggedIn()){
	//Redirect back to the login page
	ob_clean();
	header("Location: " . Engine::getRemoteAbsolutePath((new Home())->getURL()));
	exit();
}
?>