<?php
require_once "CategoryNames.php";

$FbId = $_GET['facebookid'];
$con = mysql_connect ("localhost:3306", "root", "fli2411");
mysql_select_db("brain_browser", $con);

$res = mysql_query ("SELECT category FROM youtube WHERE fbid='$FbId' GROUP BY category ORDER BY count(*) DESC", $con);

$return = array();
$i=0;
while ($row = mysql_fetch_array($res)) {
	$cat = $row['category'];
// 	echo "$cat<br><br>";
	$return[$i] = array();
	$return[$i]['name'] = $cat;
	$return[$i]['id'] = $CategoryIDReplacement[$cat];
	$i++;
}
mysql_close($con);
echo json_encode($return);
?> 
