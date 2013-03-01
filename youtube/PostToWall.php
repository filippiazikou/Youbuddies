<?
	require '../php-sdk/src/facebook.php';
	
	$Video = $_POST['videourl'];

	$config = array(
		'appId'  => '166815126764719',
		'secret' => '5db9fed1b100246aee7d84e80b185e5a',
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