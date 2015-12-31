<?php
    session_start();

    require_once __DIR__ . '/vendor/autoload.php';

	$fb = new Facebook\Facebook([
		'app_id' => '1662017130721013',
		'app_secret' => '80001adde8707802a194f667e05adcb7',
		'default_graph_version' => 'v2.2',
    ]);

    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email', 'user_likes', 'user_work_history', 'user_education_history', 'user_birthday'];
    $loginUrl = $helper->getLoginUrl('http://localhost/sma/login-callback.php', $permissions);

    echo '<a href="'.$loginUrl.'">Login with Facebook</a>';

?>