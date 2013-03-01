<?php
	/*Set Encoding*/
	header('Content-Type: text/html; charset=utf-8');

	/*Include Library for Youtube and Initialize*/
	$clientLibraryPath = 'ZendGdata-1.11.11/library';
	$oldPath = set_include_path(get_include_path() . PATH_SEPARATOR . $clientLibraryPath);
	require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
	Zend_Loader::loadClass('Zend_Gdata_YouTube');
	$yt = new Zend_Gdata_YouTube();


	$tags="jack johnson, inaudible, melodies, rock";
	$tags = str_replace(","," ",$tags);
	echo $tags;


	$yt->setMajorProtocolVersion(2);
	$query = $yt->newVideoQuery();
// 	$query->setOrderBy('RELEVANCE');
	$query->setSafeSearch('none');
	$query->setMaxResults('25');
	$query->setVideoQuery($tags);

  // Note that we need to pass the version number to the query URL function
  // to ensure backward compatibility with version 1 of the API.
  $videoFeed = $yt->getVideoFeed($query->getQueryUrl(2));
  printVideoFeed($videoFeed, 'Search results for: ' . $searchTerms);


	function printVideoFeed($videoFeed)
{
  $count = 1;
  foreach ($videoFeed as $videoEntry) {
    echo "Entry # " . $count . "\n";
    echo $videoEntry->getVideoId();
    echo "<br>";
    $count++;
  }
}

?>