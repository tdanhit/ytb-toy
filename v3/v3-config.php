<?php
    // OAUTH Configuration
    $oauthClientID = '872375828843-kmpki7pm384a8ei158i8stvupbnslrij.apps.googleusercontent.com'; //'{ClientID}';
    $oauthClientSecret = 'EXPp1jlif_S8oi4o_fC88zFB'; //'{Scereat Key}';
    $baseUri = "http://" .$_SERVER['HTTP_HOST']. "/ytb-tool/";
    $redirectUri = "http://" .$_SERVER['HTTP_HOST']. "/ytb-tool/index.php";
    /*$baseUri = "http://test-tool.mooo.com";
    $redirectUri = "http://test-tool.mooo.com";*/
    $pathJsonFile = "v3/json-upload/client_secret.json";
    
    define('OAUTH_CLIENT_ID',$oauthClientID);
    define('OAUTH_CLIENT_SECRET',$oauthClientSecret);
    define('REDIRECT_URI',$redirectUri);
    define('BASE_URI',$baseUri);
    
    // Include google client libraries
    if (!file_exists('src/autoload.php')) {
	  throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');
	}
    require_once 'src/autoload.php'; 
    require_once 'src/Client.php';
    require_once 'src/Service/YouTube.php';
    @session_start();
    
    $client = new Google_Client();
    $client->setAuthConfigFile($pathJsonFile);
    $client->setAccessType("offline");
    $client->setScopes('https://www.googleapis.com/auth/youtube');
    //$client->setClientId(OAUTH_CLIENT_ID);
    //$client->setClientSecret(OAUTH_CLIENT_SECRET);
    //$client->setRedirectUri(REDIRECT_URI);
    
    // Define an object that will be used to make all API requests.
    $youtube = new Google_Service_YouTube($client);
?>