<?php

// To add: 	Check if ThisRecord exist in FriendsVideos.related!!!
//		YoutubeSearch!

require "CosineSimilarity.php";

/*Set Encoding*/
header('Content-Type: text/html; charset=utf-8');

/*Include Library for Youtube and Initialize*/
$clientLibraryPath = 'ZendGdata-1.11.11/library';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');
$yt = new Zend_Gdata_YouTube();

/*Include Library for Facebook and Initialize*/
require_once '../php-sdk/src/facebook.php';

$ThisId = -1;
$facebook = null;
$user = -1;
$con = null;
$ids=array();
$VideoId=-1; 
$videoEntry=null;
$yt=null;
$ThisArtist="null";

function FindRelated($id) {
	global $ThisId, $facebook, $user, $con, $ids, $VideoId, $videoEntry, $yt,$ThisArtist;

	$ThisId = $id;
	$facebook = new Facebook(array(
		'appId'  => 'YOURAPPID',
		'secret' => 'YOURAPPSECRET',
		'cookie' => true,
	));


	/*Connect to DataBase*/
	$con = mysql_connect ("location", "username", "password");
	if (!$con) {
		error(DB_ERR);
	}
	mysql_select_db("brain_browser", $con);


	/*Get Info for the Video*/
	$ThisRes = mysql_query ("SELECT * FROM youtube WHERE id='$ThisId'", $con);
	$ThisRow=mysql_fetch_array($ThisRes);
	$VideoId = $ThisRow['youtubeid'];
	$user = $ThisRow['fbid'];

	/*Get Friends Of User who also have videos stored on DataBase*/
	$friends = $facebook->api("/$user/friends");
	//Store all Friends to $ids array
	$x = count($friends['data']);
	$ids=$friends['data'][0]['id'];
	for ($i=1 ; $i<$x ; $i++) {
		$ids.= ",";
		$ids .= $friends['data'][$i]['id'];
	}

	$ThisArtist = $ThisRow['artist'];
	try {
		$videoEntry = $yt->getVideoEntry($VideoId);
	} catch (Zend_Gdata_App_HttpException $httpexception) {
	}


	/*Find The Related Friends - Videos*/
	SameVideos();
	RelatedVideos();
	TagComparison();
	if ($ThisArtist != "null") SameArtist();


	mysql_close($con);

}

/*Find Friends Who have watched the same video*/
function SameVideos() {
	global $con, $VideoId, $ids, $user;
	$res = mysql_query ("SELECT id FROM youtube WHERE youtubeid='$VideoId' AND fbid<>'$user' AND fbid IN ($ids)", $con);
	while ($row=mysql_fetch_array($res)) {
		AddToTable($row['id']);
	}
}


/*Find Friends who have watched the related videos*/
function RelatedVideos(){
	global $videoEntry, $yt, $con, $ids, $user;


	
	$relatedVideosFeed = $yt->getRelatedVideoFeed($videoEntry->getVideoId());
	foreach ($relatedVideosFeed as $relatedVideoEntry) {
		$RelatedId = $relatedVideoEntry->getVideoId();
		$res = mysql_query ("SELECT id FROM youtube WHERE youtubeid='$RelatedId' AND fbid<>'$user' AND fbid IN ($ids)", $con);
		while ($row=mysql_fetch_array($res)) {
			AddToTable($row['id']);
		}
	}
}


/*~~~~~~~~Find Friends who have watched videos with similar tags - Compare tables~~~~~~*/
function TagComparison(){
	global $videoEntry, $con, $user, $ids;
	$VideoTagsStr = implode(",", $videoEntry->getVideoTags());
	/*Remove capitals from Tags*/
	$VideoTagsStr = strtolower($VideoTagsStr);
	/*Make it array again*/
	$VideoTags = explode(",", $VideoTagsStr);

	$res = mysql_query ("SELECT * FROM youtube WHERE fbid<>'$user' AND fbid IN ($ids)", $con);
	while ($row=mysql_fetch_array($res)) {
		$FriendTags = explode(",", $row['tags']);
		if (CosineSimilarity($VideoTags, $FriendTags) > 0.4 ) {
			AddToTable($row['id']);
		}
	}
}


function SameArtist() {
	global $con, $VideoId, $ids, $user, $ThisArtist;
	$res = mysql_query ("SELECT id FROM youtube WHERE artist='$ThisArtist' AND fbid<>'$user' AND fbid IN ($ids)", $con);
	while ($row=mysql_fetch_array($res)) {
		AddToTable($row['id']);
	}

}

function AddToTable($id) {
	global $ThisId, $con;
	/*$res1 = mysql_query ("SELECT * FROM related WHERE id1='$id' AND id2='$ThisId')", $con);
	$res2 = mysql_query ("SELECT * FROM related WHERE id1='$ThisId' AND id2='$id')", $con);*/
	$res1 = mysql_query ("SELECT * FROM related WHERE (id1='$id' AND id2='$ThisId') OR (id1='$ThisId' AND id2='$id')", $con);
	if (!$row = mysql_fetch_array($res1)){
		mysql_query ("INSERT INTO related (id1, id2) VALUES ('$ThisId', '$id')", $con);
	}
}

/*Find Friends who have watched videos with similar tags - Search tags on youtube*/
// $QueryString="";
// $QueryString .= implode("|", $videoEntry->getVideoTags());
// $query = $yt->newVideoQuery();
// $query->setOrderBy('relevance');
// $query->setVideoQuery($QueryString);
// 
// $relatedVideosFeed = $yt->getVideoFeed($query->getQueryUrl(2));
// foreach ($relatedVideosFeed as $relatedVideoEntry) {
// 	$RelatedId = $relatedVideoEntry->getVideoId();
// 	$res = mysql_query ("SELECT fbid FROM youtube WHERE youtubeid='$RelatedId' AND fbid<>'$user' AND fbid IN ($ids) ORDER BY RAND() LIMIT 3", $con);
// 	while ($row=mysql_fetch_array($res)) {
// 		AddToData($row['fbid'], $RelatedId);
// 	}
// }

?>