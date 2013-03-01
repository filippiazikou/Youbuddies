<?php
	/*Set Encoding*/
	header('Content-Type: text/html; charset=utf-8');
	require "CategoryNames.php";
	$category = $_POST['category'];
	$facebookid = $_POST['facebookid'];
	

	/*Find Links of user from Database with the category given*/
	$con = mysql_connect ("localhost:3306", "root", "fli2411");
        if (!$con) {
		error(DB_ERR);
        }
	mysql_select_db("brain_browser", $con);
	$categoryName = $CategoryReplacement[$category];
	$res = mysql_query ("SELECT id FROM youtube WHERE fbid='$facebookid' AND category='$categoryName' ORDER BY date desc", $con);

	$StrRes= "";
	while  ($row=mysql_fetch_array($res))
		$StrRes .= "$row[id],";

	mysql_close($con);


	$return['msg'] = $StrRes;
	echo json_encode($return);
?>