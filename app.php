<?php
	//THIS CLASS DOESNT WORK YET!!
	/* Main class that compares current userdata to data in
	   the database and categorises users in the database as 
	   friends/family/strangers etc.. */

	session_start();

	require_once __DIR__ . '/vendor/autoload.php';
	include('utilities.php');

	$currentUser = $_SESSION['current_user'];
	$result = databaseQuery("SELECT * FROM users WHERE UserID = ".$currentUser.";");
	$userDetails = mysqli_fetch_row($result);

	//Pulling the current users data directly from database instead of using API again, i'm sure theres a better way of doing this lol
	$cUserID = $userDetails[0];
	$cUserName = $userDetails[1];
	$cUserBirthday = $userDetails[2];
	$cUserGender = $userDetails[3];
	$cUserEducation = $userDetails[4];

	$databaseUsers = array();

	$result = databaseQuery("SELECT * FROM users WHERE UserID != ".$currentUser.";");
	
	while($userDetails = mysqli_fetch_row($result)) {

		$UserID = $userDetails[0];
		$UserName = 
		$userDetails[1];
		$UserBirthday = $userDetails[2];
		$UserGender = $userDetails[3];
		$UserEducation = $userDetails[4];

		//array

	}





	function compareEducation($cUserEducation) {
		$UserEducation = $_SESSION['current_user'];
		$query = "SELECT * FROM users WHERE UserEducation = '$cUserEducation';";
		echo $query;
	}

	compareEducation($cUserEducation);


?>
