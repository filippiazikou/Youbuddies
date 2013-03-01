<?php
require '../php-sdk/src/facebook.php';
$facebook = new Facebook(array(
	'appId'  => '166815126764719',
	'secret' => '5db9fed1b100246aee7d84e80b185e5a',
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