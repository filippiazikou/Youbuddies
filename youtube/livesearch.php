<?php
require_once("./computeTimePassed.php");

$q=$_GET["q"];
if ( !isset($q) ){
  $q = "";
}

// echo "q='$q'<br>";

$facebookID=$_GET["u"];
// echo "id='$facebookID'<br>";
if ( !isset($facebookID) ){
  $facebookID = "-1";
}

// echo "id='$facebookID'<br>";


$con = mysql_connect ("location", "username", "password");
if (!$con) {
	error(DB_ERR);
}
mysql_select_db("brain_browser", $con);

$res = mysql_query ("SELECT youtubeid, title, date FROM youtube WHERE fbid='$facebookID' AND ( title LIKE '%$q%' OR ( category='Music' and artist LIKE '%$q%') OR tags LIKE '%$q%' ) ORDER BY date desc", $con);

$videos = array();
$i = 0;
$html = "";
while ( $row=mysql_fetch_array($res) ){
	$id = $row[0];
	$title = $row[1];
	$date =  $row[2];
	$passedTime = computeTimePassed($date);
	$preview = "<span class='video_left' id=" . $id . "><img id='videoImg' src='http://img.youtube.com/vi/" . $id . "/1.jpg' alt=" . $title . " /></span>";
	$title = "<span class='video_title' id=".$id.">".$title."</span>";
	$time = '<span id="videoTime">'. $passedTime .'</span>';
// 	$options = "<img class='deletepost' id=".$id." src='img/delete1.png' onmouseover=\"$(this).attr('src','img/delete2.png');\" onmouseout=\"$(this).attr('src','img/delete1.png');\" /> <span class='video_options'><br><span class='wallpost' id=".$id.">Post To Wall</span><br><span class='askfriends' id=".$id.">Ask My Friends</span><br><span class='videodetails' id=".$id.">View Details</span>".$time."</span>";
	$options = '<img class="deletepost" id="' .$id.'" src="img/delete1.png" onmouseover="$(this).attr(\'src\',\'img/delete2.png\');" onmouseout="$(this).attr(\'src\',\'img/delete1.png\');" /> <span class="video_options"><br><span class="wallpost" id="'.$id.'"><img id="videoIcon" src="img/timeline.png"></span><span class="wallpost" id="'.$id.'">Share on your Timeline</span><br><img class="askfriends" id="videoIcon" src="img/friends.png"><span class="askfriends" id="'.$id.'">Related Friends</span><br><span id="videoIcon" ><img class="videodetails" id="'.$id.'" src="img/video_details.png"></span><span class="videodetails" id="'.$id.'">View Details</span>'.$time.'</span>';
	$html = "<span class='deleteVideo' id='".$id."'><div class='video'>".$preview."<span class='video_right'>".$title." ".$options."</span></div><br clear='all'><div class='separator'></div></span>";
	echo "$html";
// 	echo "$preview";
}
if ($html == "")
	echo "<span style='font-size:11px; color:#272727; float:left;clear:both;margin:10px;' > No Videos</span>";

?>  
