<?php
	require_once 'v3/v3-config.php';
	// Youtube Developer Key. NEEDED TO WORK...
	$YT_Developer_Key = "AI39si7SnB9SXTjsmmmbzQwMFmKqE4gkIU6EpBQsM9zKcgBl9ql7JsBbDebEZ51b_uDMQDSQ_egkBUkJf3J2qa35erTQhXTLnA";
	// Get your Developer Key @ http://code.google.com/apis/youtube/dashboard/gwt/index.html
	$api_key = 'AIzaSyDgx4Kt7GGo7cr6_r-JdAqqLr3JEEF25RY';

	if(@$_REQUEST['act'] == 'ajax')
	{
		if (!empty($_REQUEST['code'])) {
			if (strval($_SESSION['state']) !== strval($_GET['state'])) {
			  die('The session state did not match.');
			}
			echo "Code: " .$_REQUEST['code'];
			/*if($client->isAccessTokenExpired()) {
			    echo 'Access Token Expired'; // Debug

			    $client->authenticate($_GET['code']);
			    $NewAccessToken = json_decode($client->getAccessToken());
			    $client->refreshToken($NewAccessToken->refresh_token);
			    $_SESSION['token'] = $client->getAccessToken();
			}*/

			$client->authenticate($_GET['code']);
			$_SESSION['token'] = $client->getAccessToken();
			header('Location: ' . REDIRECT_URI);
		}

		if (isset($_SESSION['token'])) {
			$client->setAccessToken($_SESSION['token']);
		}

		if ($client->getAccessToken()) {
			if($client->isAccessTokenExpired()) {
				$test = '<p>You need to <a href="logout.php">Logout</a> before proceeding.<p>';
				echo $test;
			} else {
				$_SESSION['token'] = $client->getAccessToken();
				echo "Login OK";
			}
			
		} else {
			// If the user hasn't authorized the app, initiate the OAuth flow
			$state = mt_rand();
			$client->setState($state);
			$_SESSION['state'] = $state;
		  
			$authUrl = $client->createAuthUrl();
			$htmlBody = '
				<h3>Authorization Required</h3>
				<p>You need to <a href="'.$authUrl.'">authorize access</a> before proceeding.<p>';
			echo $htmlBody;
		}
		
	}
?>