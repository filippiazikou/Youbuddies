<?
	require '../php-sdk/src/facebook.php';
	
	$Video = $_POST['videourl'];

	$config = array(
		'appId'  => 'YOURAPPID',
		'secret' => 'YOURAPPSECRET',
	);

	$facebook = new Facebook($config);
	$user_id = $facebook->getUser();
	if($user_id) {
		try {
			$ret_obj = $facebook->api('/me/feed', 'POST',
						array(
							'link' => $Video
				));
			$return['msg'] = "success";

		} catch(FacebookApiException $e) {
			$return['msg'] = "error";
		}   
	} else {
		$return['msg'] = "error";
	} 
	echo json_encode($return);
?>