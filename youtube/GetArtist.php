<?php
function GetArtist($videoid) {


	$url = "http://www.youtube.com/watch?v=$videoid&hl=en";

	$html = file_get_contents($url);
	// echo $html; 


	$start='Artist: <span class="link-like">';
	$start_pos = strpos($html, $start);
	if ($start_pos === false) {
		return "null";
		exit();
	} 
	$html_cut = substr($html, $start_pos);
	$end = "</span>";
	$end_pos =  strpos($html_cut, $end);
	if ($end_pos === false) {
		return "null";
		exit();
	} 
	$artist = substr($html_cut, strlen($start), (strlen($html_cut) - $end_pos)*(-1)); 
	return $artist; 

}
?>