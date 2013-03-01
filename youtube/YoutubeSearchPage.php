<?php
/*Set Encoding*/
header('Content-Type: text/html; charset=utf-8');
require_once '../php-sdk/src/facebook.php';
require "CosineSimilarity.php";
/*Include Library for Facebook and Initialize*/
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
	} catch (FacebookApiException $e) {
		echo "An error occured";
		exit();
	}
}
else {
	echo "An error occured";
	exit();
}
/*Get Friends Of User who also have videos stored on DataBase*/
$friends = $facebook->api('/me/friends');
//Store all Friends to $ids array
$x = count($friends['data']);
$ids=$friends['data'][0]['id'];
for ($i=1 ; $i<$x ; $i++) {
	$ids.= ",";
	$ids .= $friends['data'][$i]['id'];
}

/*Data array*/
$data=array();



/*Connect To Database*/
$con = mysql_connect ("localhost:3306", "root", "fli2411");
if (!$con) {
	error(DB_ERR);
}
mysql_select_db("brain_browser", $con);


/*Include Library for Youtube and Initialize*/
$clientLibraryPath = 'ZendGdata-1.11.11/library';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');
$yt = new Zend_Gdata_YouTube();

$search_query = $_POST['query'];

$search_query = str_replace("+", " ", $search_query);

$yt->setMajorProtocolVersion(2);
$query = $yt->newVideoQuery();
// 	$query->setOrderBy('RELEVANCE');
$query->setSafeSearch('none');
$query->setMaxResults('50');
$query->setVideoQuery($search_query);

// Note that we need to pass the version number to the query URL function
// to ensure backward compatibility with version 1 of the API.
$videoFeed = $yt->getVideoFeed($query->getQueryUrl(2));


/*Find Friends Who have watched one of the 50 first serach results*/
foreach ($videoFeed as $videoEntry) {
	$VideoID = $videoEntry->getVideoId();
	$res = mysql_query ("SELECT fbid, title FROM youtube WHERE youtubeid='$VideoID' AND fbid<>'$user' AND fbid IN ($ids)", $con);
	while ($row=mysql_fetch_array($res)) {
		AddToData($row['fbid'], $VideoID, $row['title']);
	}
}


/*Find Friends who have wathed videos with similar tags*/
$search_query = strtolower($search_query);
$SearchArray = explode(" ", $search_query);

$res = mysql_query ("SELECT * FROM youtube WHERE fbid<>'$user' AND fbid IN ($ids)", $con);
while ($row=mysql_fetch_array($res)) {
	$FriendTags = explode(",", $row['tags']);
	if (CosineSimilarity($SearchArray, $FriendTags) > 0.2 ) {
		$fbid = $row['fbid'];
		$youtubeid = $row['youtubeid'];
		$title = $row['title'];
		AddToData($fbid, $youtubeid, $title);
	}
}
mysql_close($con);

// print_r($data);
echo json_encode($data);
function AddToData($FriendId, $VideoId, $VideoTitle) {
	global $data, $facebook;

	$friend_profile = $facebook->api($FriendId);

	/*Check if Friend is already inserted*/
	if (!isset($data[$FriendId]))  $data[$FriendId] = array();
	
	/*Check if Video already inserted*/
	if (!in_array($VideoId, $data[$FriendId])) {
		$array_length = count($data[$FriendId]);
		$data[$FriendId]['name'] = $friend_profile['name'];
		$data[$FriendId][$array_length] = $VideoId;
		$data[$FriendId]['related_videos'][$array_length]['title'] = $VideoTitle;
		$data[$FriendId]['related_videos'][$array_length]['youtubeid'] = $VideoId;
	}
}

?> 
