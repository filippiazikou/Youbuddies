<?php
	/*Set Encoding*/
// 	header('Content-Type: text/html; charset=utf-8');
	$VideoId = $_POST['videoid'];
	$FacebokId = $_POST['facebookid'];

	/*Connect To Database*/
	$con = mysql_connect ("location", "username", "password");
	mysql_select_db("brain_browser", $con);
	/*Check if video already exist*/
	$res = mysql_query("SELECT youtubeid FROM youtube WHERE id='$VideoId' AND fbid='$FacebokId'", $con);

	if (mysql_num_rows($res) == 0 ) {
		$youtubeid = "ERROR";
	}
	else {
		$row=mysql_fetch_array($res);
		$youtubeid = $row['youtubeid'];
	}

	mysql_close($con); 
	$return['youtubeid']=$youtubeid;
	echo json_encode($return);

?> 
