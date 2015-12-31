<?php

	session_start();

	require_once 'Facebook/autoload.php';

	$fb = new Facebook\Facebook([
		'app_id' => '1662017130721013',
		'app_secret' => '80001adde8707802a194f667e05adcb7',
		'default_graph_version' => 'v2.2',
    ]);


   try {
		$response = $fb->get('/me?fields=id,name,gender,birthday,work,education', $_SESSION['facebook_access_token']);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error '.$e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error '.$e->getMessage();
		exit;
	}

	$user = $response->getGraphUser();

	// we can store userdata in database and access it later to compare it with other users, below is a small example
	
	$con = mysqli_connect("127.0.0.1", "root", "secret", "sma");

	if (!$con) {
		echo "Error connection to database ".mysqli_connect_error();
	}
	/* This currently stores the fb profile id, user name, birthday, gender and university
	for the prototype, we could start comparing their education to see if they are classmates or not
	here is an image of how this data is stored in the database: http://i.imgur.com/PYAzgB2.png (my profile used as an example) */

	$query = "INSERT INTO users VALUES('".$user['id']."', '".$user['name']."', '".$user['birthday']->format('Y-m-d')."', '".$user['gender']."', '".$user['education'][0]['school']['name']."');";
	
	if (!mysqli_query($con, $query)) {
		echo ("Query error ".mysqli_error($con));
	}


	echo "<h1>Sucessfully connected to the database</h1>";
	


?>
