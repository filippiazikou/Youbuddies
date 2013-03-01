<?php
// require_once '../php-sdk/src/facebook.php';
require "CosineSimilarity.php";
require_once "GetArtist.php";


/*Set Encoding*/
header('Content-Type: text/html; charset=utf-8');

/*Include Library for Youtube and Initialize*/
$clientLibraryPath = 'ZendGdata-1.11.11/library';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');
$yt = new Zend_Gdata_YouTube();

/*Include Library for Facebook and Initialize*/
// $facebook = new Facebook(array(
// 	'appId'  => '166815126764719',
// 	'secret' => '5db9fed1b100246aee7d84e80b185e5a',
// 	'cookie' => true,
// ));



/*Connect To Database*/
$con = mysql_connect ("localhost:3306", "root", "fli2411");
if (!$con) {
	error(DB_ERR);
}
mysql_select_db("brain_browser", $con);


// Get User ID
$user = $_POST['facebookid'];


/*Get Friends Of User who also have videos stored on DataBase*/
$res = mysql_query ("SELECT friends FROM FacebookNames WHERE fbid='$user'", $con);
$row=mysql_fetch_array($res);
$ids=$row['friends'];


/*Get Video ID*/
if (!isset($_POST['videoid'])) {
	echo "No Video ID Found!";
	exit();
}
$VideoId=$_POST['videoid'];

/*Data array*/
$data=array();
$friendsNames=array();

/*Get Info for the Video*/
$videoEntry = $yt->getVideoEntry($VideoId);

/*Find Friends Who have watched the same video*/
$res = mysql_query ("SELECT fbid, title FROM youtube WHERE youtubeid='$VideoId' AND fbid IN ($ids)", $con);
while ($row=mysql_fetch_array($res)) {
	AddToData($row['fbid'], $VideoId, $row['title']);
}

/*Find Friends Who have watched videos from same Artist*/
$VideoArtist = GetArtist($VideoId);
$res = mysql_query ("SELECT fbid, title, youtubeid FROM youtube WHERE artist<>'null' AND artist='$VideoArtist' AND fbid IN ($ids)", $con);
while ($row=mysql_fetch_array($res)) {
	AddToData($row['fbid'], $row['youtubeid'], $row['title']);
}


/*Find Friends who have watched the related videos*/
$relatedVideosFeed = $yt->getRelatedVideoFeed($videoEntry->getVideoId());
foreach ($relatedVideosFeed as $relatedVideoEntry) {
	$RelatedId = $relatedVideoEntry->getVideoId();
	$res = mysql_query ("SELECT fbid, title FROM youtube WHERE youtubeid='$RelatedId' AND fbid IN ($ids)", $con);
	while ($row=mysql_fetch_array($res)) {
		AddToData($row['fbid'], $RelatedId, $row['title']);
	}
}

/*Find Friends who have watched videos with similar tags - Search tags on youtube*/
// $yt->setMajorProtocolVersion(2);
// $tags = implode(" ", $videoEntry->getVideoTags());
// $query = $yt->newVideoQuery();
// $query->setSafeSearch('none');
// $query->setMaxResults('25');
// $query->setVideoQuery($tags);
// $videoFeed = $yt->getVideoFeed($query->getQueryUrl(2));
// foreach ($videoFeed as $relatedVideoEntry) {
// 	$RelatedId = $relatedVideoEntry->getVideoId();
// 	$res = mysql_query ("SELECT fbid, title FROM youtube WHERE youtubeid='$RelatedId' AND fbid<>'$user' AND fbid IN ($ids)", $con);
// 	while ($row=mysql_fetch_array($res)) {
// 		AddToData($row['fbid'], $RelatedId, $row['title']);
// 	}
// }


/*~~~~~~~~Find Friends who have watched videos with similar tags - Compare tables~~~~~~*/
$VideoTagsStr = implode(",", $videoEntry->getVideoTags());
/*Remove capitals from Tags*/
$VideoTagsStr = strtolower($VideoTagsStr);
/*Make it array again*/
$VideoTags = explode(",", $VideoTagsStr);

$res = mysql_query ("SELECT * FROM youtube WHERE fbid IN ($ids)", $con);
while ($row=mysql_fetch_array($res)) {
	$FriendTags = explode(",", $row['tags']);
	if (CosineSimilarity($VideoTags, $FriendTags) > 0.5 ) {
		$fbid = $row['fbid'];
		$youtubeid = $row['youtubeid'];
		$title = $row['title'];
		AddToData($fbid, $youtubeid, $title);
	}
}
mysql_close($con);


echo json_encode($data);

// PrintData();

function GetName($FbId) {
	global $con, $friendsNames;

	if (!isset($friendsNames[$FbId])) {
		$res = mysql_query ("SELECT * FROM FacebookNames WHERE  fbid='$FbId'", $con);
		if (mysql_num_rows($res) == 0) {
			return "error";
		}
		else {
			$row=mysql_fetch_array($res);
			$friendsNames[$FbId] = $row['name'];
		}
		
	}
	return $friendsNames[$FbId];
}


// PrintData();

function AddToData($FriendId, $VideoId, $VideoTitle) {
	global $data;

	/*Check if Friend is already inserted*/
	if (!isset($data[$FriendId]))  $data[$FriendId] = array();
	
	/*Check if$name=GetName($FriendId); Video already inserted*/
	if (!in_array($VideoId, $data[$FriendId])) {
		$array_length = count($data[$FriendId]);
		$name = GetName($FriendId);
		if ($name!= "error") {
			$data[$FriendId]['name'] = $name;
			$data[$FriendId][$array_length] = $VideoId;
			$data[$FriendId]['related_videos'][$array_length]['title'] = $VideoTitle;
			$data[$FriendId]['related_videos'][$array_length]['youtubeid'] = $VideoId;
		}
	}
}


function PrintData() {
	global $facebook, $data, $yt;
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
				$VideoId = $data[$FriendId][$j];
				echo $yt->getVideoEntry($VideoId)->getVideoTitle();
				echo '<a href="http://www.youtube.com/watch?v=' . $VideoId . '" target="_blank"><img src="http://cyberang3l.ath.cx:33335/youtube/youplay.ico" title="Play" align="right"/></a></td></tr>';
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
?>