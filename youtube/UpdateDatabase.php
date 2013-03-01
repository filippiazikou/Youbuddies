<?php
/*
	1. Get Youtube Videos From News Feed and Wall of User
	2. Update Name and Friends of User
*/
	/*Set Encoding*/
	header('Content-Type: text/html; charset=utf-8');
	require 'GetArtist.php';
	/*Include Library for Youtube and Initialize*/
	$clientLibraryPath = 'ZendGdata-1.11.11/library';
	$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
	require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path


	Zend_Loader::loadClass('Zend_Gdata_YouTube');


	/*Connect to Database*/
	$con = mysql_connect ("localhost:3306", "root", "fli2411");
	if (!$con) {
		echo "error on DB!";
	}
	mysql_select_db("brain_browser", $con);

	/*Facebook Init*/
	require_once '../php-sdk/src/facebook.php';
	$config = array(
		'appId'  => '166815126764719',
		'secret' => '5db9fed1b100246aee7d84e80b185e5a',
	);

	$facebook = new Facebook($config);
	$user_id = $facebook->getUser();
	if($user_id) {
		try {

			/*Update Name and Friends*/
			UpdateFacebookNames($user_id);

			/*Insert Videos From Wall*/
			$fql = 'SELECT post_id, attachment, created_time FROM stream WHERE source_id = ' . $user_id . ' AND type=80';
			$ret_obj = $facebook->api(array(
						'method' => 'fql.query',
						'query' => $fql,
						));
			for ($i=0 ; $i<count($ret_obj) ; $i++) {
				if (isset($ret_obj[$i]['attachment']['href'])) {
					$url = $ret_obj[$i]['attachment']['href'];
					$created_time = $ret_obj[$i]['created_time'];
					$sqlDateTime = date('Y-m-d H:i:s', $created_time);
					if (substr_compare($url,"http://www.youtube.com", 0, strlen("http://www.youtube.com")) == 0) {
						$pos = strpos($url, "v=");
						if ($pos != false) {
							$videoid = substr ( $url , $pos+2, 11 );
							InsertToDB($videoid, $user_id, $sqlDateTime);
						}
					}
					if (substr_compare($url,"http://youtu.be", 0, strlen("http://youtu.be")) == 0) {
						$videoid = substr ( $url , strlen("http://youtu.be"), 11 );
						InsertToDB($videoid, $user_id, $sqlDateTime);
					}
				}
			}


			/*Insert Videos From News Feed*/
			$fql = "SELECT post_id, attachment, created_time FROM stream WHERE filter_key IN (SELECT filter_key FROM stream_filter WHERE uid = me() AND type = 'newsfeed')";
			$ret_obj = $facebook->api(array(
						'method' => 'fql.query',
						'query' => $fql,
						));
			for ($i=0 ; $i<count($ret_obj) ; $i++) {
				if (isset($ret_obj[$i]['attachment']['href'])) {
					$url = $ret_obj[$i]['attachment']['href'];
					$post_id = $ret_obj[$i]['post_id'];
					$created_time = $ret_obj[$i]['created_time'];
					$sqlDateTime = date('Y-m-d H:i:s', $created_time);
					if (substr_compare($url,"http://www.youtube.com", 0, strlen("http://www.youtube.com")) == 0) {
						$pos = strpos($post_id, "_");
						if ($pos != false) {
							$friend_id = substr ($post_id , 0, $pos );
						}
						$pos = strpos($url, "v=");
						if ($pos != false) {
							$videoid = substr ( $url , $pos+2, 11 );
							InsertToDB($videoid, $friend_id, $sqlDateTime);
						}
					}
					if (substr_compare($url,"http://youtu.be", 0, strlen("http://youtu.be")) == 0) {
						$pos = strpos($post_id, "_");
						if ($pos != false) {
							$friend_id = substr ($post_id , 0, $pos );
						}
						$videoid = substr ( $url , strlen("http://youtu.be"), 11 );
						UpdateFacebookNames($friend_id);
						InsertToDB($videoid, $friend_id, $sqlDateTime);
					}
				}
			}
		} catch(FacebookApiException $e) {
		}   
	}


function UpdateFacebookNames($fbid) {

	global $con, $facebook;
	
	/*Add to table if not exist*/
	$res = mysql_query("SELECT fbid FROM FacebookNames WHERE fbid = '$fbid'", $con);
	if (mysql_num_rows($res) == 0) mysql_query("INSERT INTO FacebookNames (fbid) VALUES ('$fbid')", $con);


	/*Update Friends*/
	try {
		$friends = $facebook->api("/$fbid/friends");
		$x = count($friends['data']);
		$ids=$friends['data'][0]['id'];
		for ($i=1 ; $i<$x ; $i++) {
			$ids.= ",";
			$ids .= $friends['data'][$i]['id'];
		}
		mysql_query("UPDATE  FacebookNames SET friends='$ids' WHERE fbid = '$fbid'", $con);
	} catch (FacebookApiException $e) {
	}

	/*Update Name*/
	try {
		$profile = $facebook->api("/$fbid?fields=name");
		$name = $profile['name'];
		mysql_query("UPDATE  FacebookNames SET name='$name' WHERE fbid = '$fbid'", $con);
	} catch (FacebookApiException $e) {
	}
}


function InsertToDB($videoid, $user_id, $sqlDateTime) {
	
	global $con;

	try {
		/*Check if video already exist*/
		$res = mysql_query ("SELECT * FROM youtube WHERE youtubeid='$videoid' AND fbid='$user_id'", $con);
		$num_rows = mysql_num_rows($res);
		echo "$user_id $videoid $num_rows <br>";


		if ($num_rows==0) {
			/*Get The Data For The Youtube Video*/
			$yt = new Zend_Gdata_YouTube();
			$videoEntry = $yt->getVideoEntry($videoid);
			$title =  $videoEntry->getVideoTitle();
			$category = $videoEntry->getVideoCategory();
			$artist = GetArtist($videoid);
			$tags = implode(",", $videoEntry->getVideoTags());
			$title_tags = str_replace(" ",",",$title);
			$title_tags = str_replace(" - ",",",$title);
			$tags = "$tags,$title_tags";
			/*Remove capitals from Tags*/
			$tags = strtolower($tags);
			/*Insert*/
			$con = mysql_connect ("localhost:3306", "root", "fli2411");
			if (!$con) {
				error(DB_ERR);
			}
			mysql_select_db("brain_browser", $con);

			mysql_query("INSERT INTO youtube (youtubeid, title, category, artist, tags, fbid, date) VALUES ('$videoid', '$title', '$category', '$artist' , '$tags', '$user_id', '$sqlDateTime')", $con);




// 			FindRelated(mysql_insert_id());
		}
		/*Check if user has more that 100 videos!*/
		$res = mysql_query ("SELECT * FROM youtube WHERE fbid='$user_id'", $con);
		$num_rows = mysql_num_rows($res);
		if ($num_rows==101) {
			$resDel = mysql_query("SELECT id FROM youtube WHERE fbid='$user' ORDER BY date LIMIT 1", $con);
			$rowDel = mysql_fetch_array($resDel);
			$idDel = $rowDel['id'];
			mysql_query("DELETE FROM youtube WHERE id='$idDel'", $con);
// 			mysql_query("DELETE FROM related WHERE id1='$idDel'", $con);
// 			mysql_query("DELETE FROM related WHERE id2='$idDel'", $con);
		}
		// 	if ($num_rows==0) {
	} catch (Zend_Gdata_App_HttpException $httpexception) {
	}

}

mysql_close($con);
$return['msg']="ok";
echo json_encode($return);

?>