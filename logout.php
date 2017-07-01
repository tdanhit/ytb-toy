<?php
require_once 'v3/v3-config.php';

unset($_SESSION['token']);
//unset($_SESSION['refresh_token']);
unset($_SESSION['state']);
unset($_SESSION['google_data']); //Google session data unset
$client->revokeToken();
//session_destroy();
header("Location:index.php");
//exit;
?>