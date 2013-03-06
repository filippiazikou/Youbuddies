<?php
require '../php-sdk/src/facebook.php';
$facebook = new Facebook(array(
	'appId'  => 'Your_app_id',
	'secret' => 'Your_app_secret',
	'cookie' => true,
));
// Get User ID
$user = $facebook->getUser();
if ($user) {
	try {
		$msg = "connected";
	} catch (FacebookApiException $e) {
		$msg = "error";
	}
}
else {
	$msg = "not_connected";
}
echo $_GET["jsoncallback"] . "({\"Answer\":\"". $msg . "\"})";
?>