<?php

$VideoId=$_POST['videoid'];
$FbId = $_POST['facebookid'];
$con = mysql_connect ("localhost:3306", "root", "fli2411");
mysql_select_db("brain_browser", $con);
$res = mysql_query ("SELECT id FROM youtube WHERE fbid='$FbId' AND youtubeid='$VideoId' ", $con);
if ($row = mysql_fetch_array($res)) {
	$id = $row['id'];
	mysql_query ("DELETE FROM youtube WHERE id='$id'", $con);
	/*mysql_query ("DELETE FROM related WHERE id1='$id'", $con);
	mysql_query ("DELETE FROM related WHERE id2='$id'", $con);*/
	mysql_close($con);
}
$return['msg'] = "success";
echo json_encode($return);
?> 
