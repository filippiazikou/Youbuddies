<?php

	require '../php-sdk/src/facebook.php';
	$facebook = new Facebook(array(
		'appId'  => '166815126764719',
		'secret' => '5db9fed1b100246aee7d84e80b185e5a',
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