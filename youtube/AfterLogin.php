<?php
	require '../php-sdk/src/facebook.php';
	$facebook = new Facebook(array(
		'appId'  => '166815126764719',
		'secret' => '5db9fed1b100246aee7d84e80b185e5a',
	));
	
	$user = $facebook->getUser();
	if ($user) {
		try {
			header( 'Location: http://cyberang3l.ath.cx:33335/youtube/' ) ;
		} catch (FacebookApiException $e) {
			
		}
	}
	

?>