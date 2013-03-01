<?php
	require_once("./computeTimePassed.php");
	/*Set Encoding*/
// 	header('Content-Type: text/html; charset=utf-8');
	$VideoId = $_POST['videoid'];
	$FacebokId = $_POST['facebookid'];

	/*Connect To Database*/
	$con = mysql_connect ("localhost:3306", "root", "fli2411");
	mysql_select_db("brain_browser", $con);
	/*Check if video already exist*/
	$res = mysql_query("SELECT title, youtubeid, date FROM youtube WHERE id='$VideoId' AND fbid='$FacebokId' ORDER BY date", $con);

	if (mysql_num_rows($res) == 0 ) {
		$title = "ERROR";
	}
	else {
		$row=mysql_fetch_array($res);
		$title = $row['title'];
		$id =  $row['youtubeid'];
		$date =  $row['date'];
	}

	mysql_close($con); 

	$passedTime = computeTimePassed($date);

	$return['title']=$title;
	$return['id']=$id;
	$return['passedTime']=$passedTime;
	echo json_encode($return);


// 2012-03-13 18:00:47



?>