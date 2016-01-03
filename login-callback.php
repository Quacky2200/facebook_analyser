<?php
    session_start();

    require_once __DIR__ . '/vendor/autoload.php';

    $fb = new Facebook\Facebook([
        'app_id' => '1662017130721013',
        'app_secret' => '80001adde8707802a194f667e05adcb7',
        'default_graph_version' => 'v2.2',
    ]);


    $helper = $fb->getRedirectLoginHelper();

    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error '.$e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error'.$e->getMessage();
        exit;
    }

    if (isset($accessToken)) {
        $_SESSION['facebook_access_token'] = (string) $accessToken;
    }

    header('Location:  process.php');

  ?>