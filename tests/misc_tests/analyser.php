<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '936539896431912', // Replace {app-id} with your app id
  'app_secret' => 'c1277207e923cc138650b3bc520286c1',
  'default_graph_version' => 'v2.2',
 ]);

try {
	$response = $fb->get('/me?fields=id,name,posts{id, message,message_tags}', $_SESSION['fb_access_token']);
} catch (Facebook\Exceptions\FacebookResponseException $e) {
	echo 'Graph returned an error: ' . $e->getMessage();
	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;
}

$graphNode = $response->getGraphNode();
$userPosts = $graphNode->getProperty('posts');
$postContents = readPosts($userPosts);
$friendDetails = analysePosts($postContents);



function readPosts($posts) {
	echo "<pre>";
	print_r($posts[0]);
	echo "</pre>";

	$postContents = array();

	for ($i = 0; $i < count($posts); $i++) {		
		$taggedUsers = array();
		foreach ($posts[$i]['message_tags'] as $friends) {
			$taggedUsers[$posts[$i]['id']] = array($friends['id'], $friends['name']);
		}

		$postContents[$posts[$i]['id']] = array($posts[$i]['id'], $posts[$i]['message'], $taggedUsers);
	}

	return $postContents;
}

function analysePosts($posts) {

	$userConnections = array();

	foreach($posts as $post) {
		$results = analyseContext($post[1]);

		//Currently breaks if NO users are tagged or more than ONE user is tagged
		
		if (!array_key_exists($post[2][$post[0]][0], $userConnections)) {
				$userConnections[$post[2][$post[0]][0]] = array(0, 0); //First index represents friendship and the second enmity
		}
		
		/* 
		* First branch executed when user makes postive and negative
		* comments about tagged users
		*
		* Second branch executed when user makes positive comments about tagged users
		*
		* Third branch executed when user makes negative comments about the tagged users
		*/
		if (($results[0] > $results[1] && $results[1] > $results[0] / 2) || ($results[1] > $results[0] && $results[0] > $results[1] / 2))  {
			$userConnections[$post[2][$post[0]][0]][0]++;
			$userConnections[$post[2][$post[0]][0]][1]++;
		} else if ($results[0] > $results[1]) {
			$userConnections[$post[2][$post[0]][0]][0]++;
		} else if ($results[0] < $results[1]) {
			$userConnections[$post[2][$post[0]][0]][1]++;
		}
	}
}

function analyseContext($message) {
	$positiveKeywords = array('love' => 0, 'like' => 0, 'friend' => 0, 'bestfriend' => 0, 'fun' => 0);
	$negativeKeywords = array('angry' => 0, 'hate' => 0, 'mad' => 0, 'sad' => 0 , 'disappointed' => 0);

	$message = explode(" ", $message);
	foreach ($message as $word) {
		if (array_key_exists($word, $positiveKeywords)) {
			$positiveKeywords[$word]++;
		} elseif (array_key_exists($word, $negativeKeywords)) {
			$negativeKeywords[$word]++;
		}
	}
	$totalPositiveKeywords = 0;
	$totalNegativeKeywords = 0;

	foreach ($positiveKeywords as $word => $count) {
		$totalPositiveKeywords += $count;
	}

	foreach ($negativeKeywords as $word => $count) {
		$totalNegativeKeywords += $count;
	}
	return array($totalPositiveKeywords, $totalNegativeKeywords);

}	

?>