<?php
// RETURN VALUES:
// 1. ERROR
// 2. NOT_LOGGED_IN
// 3. SUCCESS
// require "Session.php";
// if (GetValue() == "off") exit();

// require 'Session.php';
// if (GetValue() == "off") {
// 	$ReturnValue = "OffRecord";
// 	echo $_GET["jsoncallback"] . "({\"Answer\":\"". $ReturnValue . "\"})";
// 	exit();
// }

/*Set Encoding*/
header('Content-Type: text/html; charset=utf-8');

require 'GetArtist.php';
/*Include Library for Youtube and Initialize*/
$clientLibraryPath = 'ZendGdata-1.11.11/library';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');

/*Include Library For Facebook*/
require_once '../php-sdk/src/facebook.php';

// require_once 'FindRelated.php';


/*Insert user - link - verb - category to database*/
function InsertToDatabase($youtube, $category, $artist, $tags, $title, $user) {
	$con = mysql_connect ("location", "username", "password");
        if (!$con) {
		error(DB_ERR);
        }
	mysql_select_db("brain_browser", $con);

	/*Escape Special Characters*/
	$title=mysql_real_escape_string($title);
	$artist=mysql_real_escape_string($artist);
	$tags=mysql_real_escape_string($tags);

	/*Check if video already exist*/
	$res = mysql_query ("SELECT * FROM youtube WHERE youtubeid='$youtube' AND fbid='$user'", $con);
	$num_rows = mysql_num_rows($res);
	if ($num_rows==0) {
		mysql_query("INSERT INTO youtube (youtubeid, title, category, artist, tags, fbid) VALUES ('$youtube', '$title', '$category', '$artist', '$tags', '$user')", $con);
// 		FindRelated(mysql_insert_id());
	}
	else {
		mysql_query("UPDATE youtube SET date=NOW() WHERE youtubeid='$youtube' AND fbid='$user'", $con);
	}
	/*Check if user has more that 50 videos!*/
	$res = mysql_query ("SELECT * FROM youtube WHERE fbid='$user'", $con);
	$num_rows = mysql_num_rows($res);
	if ($num_rows==101) {
		$resDel = mysql_query("SELECT id FROM youtube WHERE fbid='$user' ORDER BY date LIMIT 1", $con);
		$rowDel = mysql_fetch_array($resDel);
		$idDel = $rowDel['id'];
		mysql_query("DELETE FROM youtube WHERE id='$idDel'", $con);
// 		mysql_query("DELETE FROM related WHERE id1='$idDel'", $con);
// 		mysql_query("DELETE FROM related WHERE id2='$idDel'", $con);
	}
// 	if ($num_rows==0) {
	mysql_close($con);
	return "OK";
	
}

function CheckFacebookConnection() {
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
			return $user;
		} catch (FacebookApiException $e) {
			return "FB_ERR";
		}
	}
	else {
		return "NOT_LOGGED_IN";
	}
}

/*Get Video Id*/
$ReturnValue = "ERROR";

if (!isset($_GET['youtube'])) {
	if ($_GET['source'] == "ext") {
		echo $_GET["jsoncallback"] . "({\"Answer\":\"". $ReturnValue . "\"})";
		exit();
	}
	else {
		$return['msg'] = $ReturnValue ;
		echo json_encode($return);
	}
}
$youtube = $_GET['youtube'];

/*Get Facebook User*/
$user = CheckFacebookConnection();
if ($user == "FB_ERR") {
	$ReturnValue = "ERROR";
	if ($_GET['source'] == "ext") {
		echo $_GET["jsoncallback"] . "({\"Answer\":\"". $ReturnValue . "\"})";
		exit();
	}
}
else if ($user == "NOT_LOGGED_IN") {
	$ReturnValue = "NOT_LOGGED_IN";
	if ($_GET['source'] == "ext") {
		echo $_GET["jsoncallback"] . "({\"Answer\":\"". $ReturnValue . "\"})";
		exit();
	}
	else {
		$return['msg'] = $ReturnValue ;
		echo json_encode($return);
	}
}

/*Get The Data For The Youtube Video*/
$yt = new Zend_Gdata_YouTube();
$videoEntry = $yt->getVideoEntry($youtube);
$title =  $videoEntry->getVideoTitle();
$category = $videoEntry->getVideoCategory();

$artist = GetArtist($youtube);


$tags = implode(",", $videoEntry->getVideoTags());
$title_tags = str_replace(" ",",",$title);
$title_tags = str_replace(" - ",",",$title);
$tags = "$tags,$title_tags";
/*Remove capitals from Tags*/
$tags = strtolower($tags);



/*Insert to Database and Return*/
if (InsertToDatabase($youtube, $category, $artist, $tags, $title, $user) == "OK") $ReturnValue = "SUCCESS";


if ($_GET['source'] == "ext") {
	echo $_GET["jsoncallback"] . "({\"Answer\":\"". $ReturnValue . "\"})";
}
else {
	$return['msg'] = $ReturnValue ;
	echo json_encode($return);
}
?>