<?php
	require '../php-sdk/src/facebook.php';
	$facebook = new Facebook(array(
		'appId'  => 'Your_App_Id',
		'secret' => 'Your_App_secret',
	));
	
	$user = $facebook->getUser();
	if ($user) {
		try {
			header( 'Location: http://cyberang3l.ath.cx:33335/youtube/' ) ;
		} catch (FacebookApiException $e) {
			
		}
	}
	

?>