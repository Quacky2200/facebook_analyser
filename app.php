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
		$UserEducation = $userDetails[4];


		$UserDetails = array(

			                $UserID => array(
			                	            $UserEducation = compareEducation($cUserEducation, $UserEducation),
			                	            ),

			                );


		array_push($databaseUsers, $UserDetails);

	}

		
	echo "<pre>";
	var_dump($databaseUsers);
	echo "</pre>";	


	function compareEducation($cUserEducation, $UserEducation) {
		$result = ($cUserEducation == $UserEducation ? 1 : 0);
		return $result;
	}
 

?>
