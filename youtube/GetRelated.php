<?php
require_once '../php-sdk/src/facebook.php';

$youtubeid = $_POST['youtubeid'];
$user = $_POST['facebookid'];

/*Set Encoding*/
header('Content-Type: text/html; charset=utf-8');

/*Include Library for Youtube and Initialize*/
$clientLibraryPath = 'ZendGdata-1.11.11/library';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');
$yt = new Zend_Gdata_YouTube();

$facebook = new Facebook(array(
	'appId'  => '166815126764719',
	'secret' => '5db9fed1b100246aee7d84e80b185e5a',
	'cookie' => true,
));

/*Connect to DataBase*/
$con = mysql_connect ("localhost:3306", "root", "fli2411");
if (!$con) {
	error(DB_ERR);
}
mysql_select_db("brain_browser", $con);

/*Array To Store Data*/
$data=array();


/*Find the id of record in youtube table*/
$res = mysql_query("SELECT id FROM youtube WHERE youtubeid='$youtubeid' AND fbid='$user'", $con);
$row = mysql_fetch_array($res);
$id = $row['id'];

/*Find The related Ids*/
$res = mysql_query("SELECT * FROM related WHERE id1='$id' OR id2='$id'", $con);
while ($row = mysql_fetch_array($res)) {
	if ($row['id1'] == $id) $RelatedId =  $row['id2'];
	else $RelatedId =  $row['id1'];
	AddToArray($RelatedId);
	echo json_encode($RelatedId);
}
echo json_encode($data);

function ShowFriends() {
	global $facebook, $data;
	if (empty($data)) {
		return -1;
	}
	else {
		$friends = array_keys($data);
		for ($i=0 ; $i<count($friends) ; $i++) {
			$FriendId = $friends[$i];
			$friend_profile = $facebook->api($FriendId);
				echo '<th><a href="http://www.facebook.com/profile.php?id=' . $FriendId .'" target="_blank"><img src="https://graph.facebook.com/' . $FriendId . '/picture" title="' . $friend_profile['name'] . '"/></a></th>';
				echo "<th>$friend_profile[name]</th>";

			for ($j=0 ; $j<count($data[$FriendId]) ; $j++) {
				echo $data[$FriendId][$j];
			}
		}
	}
}

function AddToArray($RelatedId) {
	global $facebook, $data, $con;

	/*Find Friend Id And Related Youtube Id*/
	$res = mysql_query("SELECT youtubeid, fbid, title FROM youtube WHERE id='$RelatedId'", $con);
	$row = mysql_fetch_array($res);
	$FriendId = $row['fbid'];
	$VideoId = $row['youtubeid'];
	$VideoTitle = $row['title'];

	/*Check if Friend is already inserted*/
	if (!isset($data[$FriendId]))  $data[$FriendId] = array();
	
	/*Check if Video already inserted*/
	if (!in_array($VideoId, $data[$FriendId])) {
		$friend_profile = $facebook->api($FriendId);

		$array_length = count($data[$FriendId]);
		$data[$FriendId]['name'] = $friend_profile['name'];
		$data[$FriendId]['related_videos'][$array_length]['title'] = $VideoTitle;
		$data[$FriendId]['related_videos'][$array_length]['youtubeid'] = $VideoId;
// 		$data[$FriendId][$array_length] = $VideoTitle . '<a href="http://www.youtube.com/watch?v=' . $VideoId . '" target="_blank"><img src="../youplay.ico" title="Play" align="right"/></a>';
	}

}
?>