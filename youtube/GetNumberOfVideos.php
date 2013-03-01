<?php
	$FacebokId = $_POST['facebookid'];

	/*Connect To Database*/
	$con = mysql_connect ("localhost:3306", "root", "fli2411");
	mysql_select_db("brain_browser", $con);
	
	$res = mysql_query("SELECT count(*) as n FROM youtube WHERE fbid='$FacebokId'", $con);

	if (mysql_num_rows($res) == 0 ) {
		$number = "ERROR";
	}
	else {
		$row=mysql_fetch_array($res);
		$number = $row['n'];
	}

	mysql_close($con); 

	$return['number'] = $number;
	echo json_encode($return);

?>