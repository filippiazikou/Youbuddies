<?php
	require "CategoryNames.php";
	$category = $_POST['category'];
	$facebookid = $_POST['facebookid'];

	$categoryName = $CategoryReplacement[$category];

	$con = mysql_connect ("location", "username", "password");
	mysql_select_db("brain_browser", $con);
	$res = mysql_query ("SELECT id FROM youtube WHERE fbid='$facebookid' AND category='$categoryName' ", $con);
	$num = mysql_num_rows($res);
	mysql_close($con);

	$return['num'] = $num;
	echo json_encode($return);

?> 
