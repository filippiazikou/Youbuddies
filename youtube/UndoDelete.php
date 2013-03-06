<?php
/*Set Encoding*/
header('Content-Type: text/html; charset=utf-8');
require 'GetArtist.php';
/*Include Library for Youtube and Initialize*/
$clientLibraryPath = 'ZendGdata-1.11.11/library';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');



/*Insert user - link - verb - category to database*/
function InsertToDatabase($youtube, $category, $artist, $tags, $title, $user) {
	$con = mysql_connect ("location", "username", "password");
        if (!$con) {
		error(DB_ERR);
        }
	mysql_select_db("brain_browser", $con);

	/*Check if video already exist*/
	$res = mysql_query ("SELECT * FROM youtube WHERE youtubeid='$youtube' AND fbid='$user'", $con);
	$num_rows = mysql_num_rows($res);
	if ($num_rows==0) {
		mysql_query("INSERT INTO youtube (youtubeid, title, category, artist, tags, fbid) VALUES ('$youtube', '$title', '$category', '$artist', '$tags', '$user')", $con);
	}
	/*Check if user has more that 50 videos!*/
	$res = mysql_query ("SELECT * FROM youtube WHERE fbid='$user'", $con);
	$num_rows = mysql_num_rows($res);
	if ($num_rows==51) {
		mysql_query("DELETE FROM youtube WHERE fbid='$user' ORDER BY id LIMIT 1", $con);
	}
// 	if ($num_rows==0) {
	mysql_close($con);
	return "OK";
	
}

$return['msg'] = "error";

$youtube = $_POST['videoid'];
$user = $_POST['facebookid'];


/*Get The Data For The Youtube Video*/
$yt = new Zend_Gdata_YouTube();
$videoEntry = $yt->getVideoEntry($youtube);
$title =  $videoEntry->getVideoTitle();
$category = $videoEntry->getVideoCategory();
$artist = GetArtist($youtube);
$tags = implode(", ", $videoEntry->getVideoTags());



/*Insert to Database and Return*/
if (InsertToDatabase($youtube, $category, $artist, $tags, $title, $user) == "OK") $return['msg'] = "success";


echo json_encode($return);

?> 