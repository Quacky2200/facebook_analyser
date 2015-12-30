<?php

	session_start();

	require_once 'Facebook/autoload.php';

	$fb = new Facebook\Facebook([
		'app_id' => '1662017130721013',
		'app_secret' => '80001adde8707802a194f667e05adcb7',
		'default_graph_version' => 'v2.2',
    ]);


   try {
		$response = $fb->get('/me?fields=id,name,gender,age_range,work,education', $_SESSION['facebook_access_token']);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error '.$e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error '.$e->getMessage();
		exit;
	}


	$user = $response->getGraphUser();

	echo 'User ID: '.$user['id']." Name: ".$user['name'].$user['age_range']." Gender: ".$user['gender']." Education : ".$user['education'];

	// we can store userdata in database and access it to compare it with other users 
	
	$con = mysqli_connect("127.0.0.1", "root", "root", "mysql");

	if (!$con) {
		echo "Error connection to database ".mysqli_connect_error();
	}

	$query = "INSERT INTO users (".$user['id'].", "
		

	echo "<h1>Sucessfully connected to the database</h1>";


?>
