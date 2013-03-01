<?php
	require "CategoryNames.php";
	$category = $_GET['category'];
	$facebookid = $_GET['facebookid'];
	

	$con = mysql_connect ("localhost:3306", "root", "fli2411");
	mysql_select_db("brain_browser", $con);

	if ($category!="Other"){ 
		$categoryName = $CategoryReplacement[$category];

		$res = mysql_query ("SELECT id FROM youtube WHERE fbid='$facebookid' AND category='$categoryName' ", $con);
		$num = mysql_num_rows($res);
		
	}
	
	else {
		$CategoriesString = implode(",", $CategoryReplacement);
		echo $CategoriesString;
		$res = mysql_query ("SELECT id FROM youtube WHERE fbid='$facebookid' AND category IN ('$CategoriesString') ", $con);
		$num = mysql_num_rows($res);
	}
	mysql_close($con);

	$return['num'] = $num;
	echo json_encode($return);
?> 
