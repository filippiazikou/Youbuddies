<?php



$clientLibraryPath = 'library';
$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_YouTube');
$yt = new Zend_Gdata_YouTube();

$videoEntry = $yt->getVideoEntry('jBDF04fQKtQ');
// printVideoEntry($videoEntry);




function printVideoEntry($videoEntry) 
{
  // the videoEntry object contains many helper functions
  // that access the underlying mediaGroup object
  echo 'Video: ' . $videoEntry->getVideoTitle() . "<br>";
//   echo 'Video ID: ' . $videoEntry->getVideoId() . "\n";
//   echo 'Updated: ' . $videoEntry->getUpdated() . "\n";
//   echo 'Description: ' . $videoEntry->getVideoDescription() . "\n";
//   echo 'Category: ' . $videoEntry->getVideoCategory() . "\n";
   echo 'Tags: ' . implode(", ", $videoEntry->getVideoTags()) . "<br>";
//   echo 'Watch page: ' . $videoEntry->getVideoWatchPageUrl() . "\n";
//   echo 'Flash Player Url: ' . $videoEntry->getFlashPlayerUrl() . "\n";
//   echo 'Duration: ' . $videoEntry->getVideoDuration() . "\n";
//   echo 'View count: ' . $videoEntry->getVideoViewCount() . "\n";
   echo 'Rating: ' . $videoEntry->getVideoRatingInfo() . "<br>";
//   echo 'Geo Location: ' . $videoEntry->getVideoGeoLocation() . "\n";
//   echo 'Recorded on: ' . $videoEntry->getVideoRecorded() . "\n";
//   
//   // see the paragraph above this function for more information on the 
//   // 'mediaGroup' object. in the following code, we use the mediaGroup
//   // object directly to retrieve its 'Mobile RSTP link' child
//   foreach ($videoEntry->mediaGroup->content as $content) {
//     if ($content->type === "video/3gpp") {
//       echo 'Mobile RTSP link: ' . $content->url . "\n";
//     }
//   }
//   
   echo "Thumbnails:<br>";
  $videoThumbnails = $videoEntry->getVideoThumbnails();
// 
  foreach($videoThumbnails as $videoThumbnail) {
    echo $videoThumbnail['time'] . ' - ' . $videoThumbnail['url'];
    echo ' height=' . $videoThumbnail['height'];
    echo ' width=' . $videoThumbnail['width'] . "<br>";
  }
}

$relatedVideosFeed = $yt->getRelatedVideoFeed($videoEntry->getVideoId());
foreach ($relatedVideosFeed as $relatedVideoEntry) {
  printVideoEntry($relatedVideoEntry);
echo "<br><br><br><br>";
}


?> 
