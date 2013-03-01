<?php
/*Takes two arrays of words and returns the percetange similarity*/
function CosineSimilarity($tokensA, $tokensB)
{
	$a = $b = $c = 0;
	$uniqueTokensA = $uniqueTokensB = array();

	$uniqueMergedTokens = array_unique(array_merge($tokensA, $tokensB));

	foreach ($tokensA as $token) $uniqueTokensA[$token] = 0;
	foreach ($tokensB as $token) $uniqueTokensB[$token] = 0;

	foreach ($uniqueMergedTokens as $token) {
		$x = isset($uniqueTokensA[$token]) ? 1 : 0;
		$y = isset($uniqueTokensB[$token]) ? 1 : 0;
		$a += $x * $y;
		$b += $x;
		$c += $y;
	}
	return $b * $c != 0 ? $a / sqrt($b * $c) : 0;
}


?> 
