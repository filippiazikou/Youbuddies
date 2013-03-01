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
			$user_profile = $facebook->api('/me');
			$username = $user_profile['name'];
			/*Loggout link*/
			$loginUrl = $facebook->getLoginUrl(array(
				'canvas' => 1,
				'fbconnect' => 0,
				'scope' => 'publish_stream, read_stream',
				'redirect_uri' => 'http://cyberang3l.ath.cx:33335/youtube/AfterLogin',
				'display' => 'popup'
			));
			$logoutUrl = $facebook->getLogoutUrl(array(
				'next' => $loginUrl
			));
			$FacebookID = $user_profile['id'];
			$html ='';
			$html= $html."<a href='http://www.facebook.com/profile.php?id='".$FacebookID."' target='_blank'><div id='homePicBg'><img id='homePic' src='https://graph.facebook.com/".$FacebookID."/picture' title='".$username."'/></div></a>";
			$html= $html."<a href='http://www.facebook.com/profile.php?id=".$FacebookID."' target='_blank'><div id='homefriendName'>".$username."</div></a>"."<a href='".$logoutUrl."' target=_blank id='logout'>Not You?</a>";
			echo "$html";
		} catch (FacebookApiException $e) {
			echo "<p align='right'>Error Occured</p>";
		}
	}
	else {
		$loginUrl = $facebook->getLoginUrl(array(
			'canvas' => 1,
			'fbconnect' => 0,
			'scope' => 'publish_stream, read_stream',
			'redirect_uri' => 'http://cyberang3l.ath.cx:33335/youtube/AfterLogin',
			'display' => 'popup'
		));
// 		echo "<p align='right'><a href=$loginUrl target='_blank'>Please Login!</a></p>";
		echo "<p align='right'><a href='http://cyberang3l.ath.cx:33335/youtube/' target='_blank'>Please Login!</a></p>";
	}
?> 