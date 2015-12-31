<?php
	session_start();

	require_once __DIR__ . '/vendor/autoload.php';



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

	function databaseQuery($con, $query) {

		if (!$result = mysqli_query($con, $query)) {
			echo "Query error ".mysqli_error($con);
		}
		return $result;
	}


	function checkUserIdExists($con, $UserNode) {
		$query = "SELECT count(1) FROM users WHERE UserID = ".$UserNode['id'].";";
		$result = databaseQuery($con, $query);
		$row = mysqli_fetch_row($result);	

		if (intval($row[0]) == 0) {

			echo "Added user to database";
			$query = "INSERT INTO users VALUES('".$UserNode['id']."', '".$UserNode['name']."', '".$UserNode['birthday']->format('Y-m-d')."', '".$UserNode['gender']."', '".$UserNode['education'][0]['school']['name']."');";
			databaseQuery($con, $query);

		} else {
			echo "User is already in database";
		}
	}


	// we can store userdata in database and access it later to compare it with other users, below is a small example
	
	$con = mysqli_connect("127.0.0.1", "root", "", "sma");

	if (!$con) {
		echo "Error connection to database ".mysqli_connect_error();
	}
	/* This currently stores the fb profile id, user name, birthday, gender and university
	for the prototype, we could start comparing their education to see if they are classmates or not
	here is an image of how this data is stored in the database: http://i.imgur.com/PYAzgB2.png (my profile used as an example) */

	checkUserIdExists($con, $user);

	echo "<h1>Sucessfully connected to the database</h1>";

	
?>
