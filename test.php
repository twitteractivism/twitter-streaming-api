<?php 

include 'get_tweets_keywords.php';

$json = file_get_contents("output.json");
$jsond = json_decode($json);

$count = count($jsond->statuses);

//$first = $jsond->statuses[0];
//enqueueStatus($first);

for ($i=1; $i < 3; $i++) { 
	$tweet = $jsond->statuses[$i];
	enqueueStatus($tweet);
}
echo "here";
 ?>
