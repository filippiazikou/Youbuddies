<?php

	require_once("./computeTimePassed.php");
	/*Set Encoding*/
// 	header('Content-Type: text/html; charset=utf-8');
	$YoutubeId = $_POST['youtubeid'];
	$FacebokId = $_POST['facebookid'];

	/*Connect To Database*/
	$con = mysql_connect ("location", "username", "password");
	mysql_select_db("brain_browser", $con);
	/*Check if video already exist*/
	$res = mysql_query("SELECT * FROM youtube WHERE youtubeid='$YoutubeId' AND fbid='$FacebokId' ORDER BY date desc", $con);

	if (mysql_num_rows($res) == 0 ) {
		$title = "ERROR";
	}
	else {
		$row=mysql_fetch_array($res);
		$title = $row['title'];
		$id = $row['id'];
		$category = $row['category'];
		$artist = $row['artist'];
		$date = $row['date'];
	}

	mysql_close($con); 

	$passedTime = computeTimePassed($date);

	$return['title'] = $title;
	$return['id'] = $id;
	$return['category'] = $category;
	$return['artist'] = $artist;
	$return['date'] = $date;
	
	$return['passedTime'] = $passedTime ;

	echo json_encode($return);

?>