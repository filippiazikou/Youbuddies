<?php
	/*Include Library for Youtube and Initialize*/
	$clientLibraryPath = 'ZendGdata-1.11.11/library';
	$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
	require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
	Zend_Loader::loadClass('Zend_Gdata_YouTube');

	/*Search Form*/
	if (isset($_GET['query'])) {
		echo '<form action="search_example.php" method="get">';
		echo '<input type="text" name="query" value="' . $_GET['query'] . '"/>';
		echo '<input type="submit" value="Search" /></form>';
	}
	else {
		echo '<form action="search_example.php" method="get">';
		echo '<input type="text" name="query"/>';
		echo '<input type="submit" value="Search" /></form>';
	}


	if (isset($_GET['play'])) {
		playVideo($_GET['play']);
	}
	else if (isset($_GET['query'])) {
		searchAndPrint($_GET['query']);
	}

	

	function searchAndPrint($searchTerms)
	{
		$yt = new Zend_Gdata_YouTube();
		$yt->setMajorProtocolVersion(2);
		$query = $yt->newVideoQuery();
		$query->setSafeSearch('none');
		$query->setVideoQuery($searchTerms);
		$query->setMaxResults('50');
		// Note that we need to pass the version number to the query URL function
		// to ensure backward compatibility with version 1 of the API.
		$videoFeed = $yt->getVideoFeed($query->getQueryUrl(2));
		printVideoFeed($videoFeed, 'Search results for: ' . $searchTerms);
	}


	function printVideoFeed($videoFeed)
	{
		$count = 1;
		foreach ($videoFeed as $videoEntry) {
			printVideoEntry($videoEntry);
			$count++;
		}
	}

	function printVideoEntry($videoEntry) 
	{
		// the videoEntry object contains many helper functions
		// that access the underlying mediaGroup object
		echo '<a href="search_example.php?play=' . $videoEntry->getVideoId() .
'"><img align="middle" src="http://img.youtube.com/vi/' . $videoEntry->getVideoId() .
'/1.jpg"/>' . $videoEntry->getVideoTitle() . '</a><br><br>';
		
		

// 		echo 'Video ID: ' . $videoEntry->getVideoId() . "<br>";
// 		echo 'Updated: ' . $videoEntry->getUpdated() . "\n";
// 		echo 'Description: ' . $videoEntry->getVideoDescription() . "\n";
// 		echo 'Category: ' . $videoEntry->getVideoCategory() . "\n";
// 		echo 'Tags: ' . implode(", ", $videoEntry->getVideoTags()) . "\n";
// 		echo 'Watch page: ' . $videoEntry->getVideoWatchPageUrl() . "\n";
// 		echo 'Flash Player Url: ' . $videoEntry->getFlashPlayerUrl() . "\n";
// 		echo 'Duration: ' . $videoEntry->getVideoDuration() . "\n";
// 		echo 'View count: ' . $videoEntry->getVideoViewCount() . "\n";
// 		echo 'Rating: ' . $videoEntry->getVideoRatingInfo() . "\n";
// 		echo 'Geo Location: ' . $videoEntry->getVideoGeoLocation() . "\n";
// 		echo 'Recorded on: ' . $videoEntry->getVideoRecorded() . "\n";
// 		
// 		// see the paragraph above this function for more information on the 
// 		// 'mediaGroup' object. in the following code, we use the mediaGroup
// 		// object directly to retrieve its 'Mobile RSTP link' child
// 		foreach ($videoEntry->mediaGroup->content as $content) {
// 			if ($content->type === "video/3gpp") {
// 				echo 'Mobile RTSP link: ' . $content->url . "\n";
// 			}
// 		}
	}

	function playVideo($VideoID) {
		echo '<object width="425" height="350" data="http://www.youtube.com/v/' . $VideoID .
'" type="application/x-shockwave-flash"><param name="src" value="http://www.youtube.com/v/' .	
$VideoID . '" /></object>';
		echo '<br><br>';
	}

?>