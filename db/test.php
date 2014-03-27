<?php 

// This function is called automatically by the Phirehose class
// when a new tweet is received with the JSON data in $status
function enqueueStatus($status, $db) {
  $tweet_object = $status;
	
// Ignore tweets without a properly formed tweet id value
  if (!(isset($tweet_object->id_str))) { return;}

  $tweet_id = $tweet_object->id_str;

  // If there's a ", ', :, or ; in object elements, serialize() gets corrupted 
  // You should also use base64_encode() before saving this
  $raw_tweet = base64_encode(serialize($tweet_object));
	
  $field_values = 'raw_tweet = "' . $raw_tweet . '", ' .
    'tweet_id = ' . $tweet_id;
  $db->insert('json_cache',$field_values);
}

// A database connection is established at launch and kept open permanently
require_once('db_lib.php');
$oDB = new db;

$json = file_get_contents("output.json");
$jsond = json_decode($json);

$count = count($jsond->statuses);

//$first = $jsond->statuses[1];
//var_dump($first);
//enqueueStatus($first, $oDB);


for ($i=0; $i < $count; $i++) { 
	$tweet = $jsond->statuses[$i];
	enqueueStatus($tweet, $oDB);
	echo $i;
}

echo "<br>";
echo "here";
 ?>