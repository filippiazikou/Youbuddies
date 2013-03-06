<?php

	require '../php-sdk/src/facebook.php';
	$facebook = new Facebook(array(
		'appId'  => 'Your_App_ID',
		'secret' => 'Your_App_Secret',
	));
	
	$user = $facebook->getUser();
	if ($user) {
		try {
			$return['msg'] = $user;
		} catch (FacebookApiException $e) {
			$return['msg'] = "error";
		}
	}
	else {
		$return['msg'] = "error";
	}
	echo json_encode($return);
?>