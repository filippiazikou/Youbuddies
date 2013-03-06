<?php
$youtubeid = $_GET['youtubeid'];

/*Get Facebook ID*/
require_once '../php-sdk/src/facebook.php';
// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
	'appId'  => 'YOURAPPID',
	'secret' => 'YOURAPPSECRET',
	'cookie' => true,
));
// Get User ID
$user = $facebook->getUser();
if ($user) {
	try {
		$user_profile = $facebook->api('/me');
	} catch (FacebookApiException $e) {
		echo "Sorry, an error occured!";
	}
}
else {
	echo "Please Login!";
	exit();
}

/*Connect to DataBase*/
$con = mysql_connect ("location", "username", "password");
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
}

ShowFriends();

function ShowFriends() {
	global $facebook, $data;
	if (empty($data)) {
		echo "<table>";
		echo "<tr><td>No Friend Found!</td></tr>";
		echo "</table>";
	}
	else {
		$friends = array_keys($data);
		echo "<table>";
		for ($i=0 ; $i<count($friends) ; $i++) {
			$FriendId = $friends[$i];
			$friend_profile = $facebook->api($FriendId);
			echo "<tr>";
				echo '<th><a href="http://www.facebook.com/profile.php?id=' . $FriendId .'" target="_blank"><img src="https://graph.facebook.com/' . $FriendId . '/picture" title="' . $friend_profile['name'] . '"/></a></th>';
				echo "<th>$friend_profile[name]</th>";
			echo "</tr>";

			for ($j=0 ; $j<count($data[$FriendId]) ; $j++) {
				echo "<tr>";
				echo "<td></td><td>";
				echo $data[$FriendId][$j];
			}
			
			/*Separator!*/
			if ($i != count($friends) - 1) {
				echo '<tr><td colspan="2">';
				echo '<div class="separator" style="height:1px;background:#cdcbcb;border-bottom:2px solid #ededed;border-top:2px solid #ededed;"></div>';
				echo '<td></tr>';
			}
		}
		echo "</table>";
	}
}

function AddToArray($RelatedId) {
	global $data, $con;

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
		$array_length = count($data[$FriendId]);
		$data[$FriendId][$array_length] = $VideoTitle . '<a href="http://www.youtube.com/watch?v=' . $VideoId . '" target="_blank"><img src="../youplay.ico" title="Play" align="right"/></a>';
	}

}
?>