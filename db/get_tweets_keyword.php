<?php
/**
* get_tweets_keyword.php
* Collect tweets from the Twitter streaming API
* This must be run as a continuous background process
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/
set_time_limit(0);
require_once('140dev_config.php');

require_once('../libraries/phirehose/Phirehose.php');
require_once('../libraries/phirehose/OauthPhirehose.php');
class Consumer extends OauthPhirehose
{
  // A database connection is established at launch and kept open permanently
  public $oDB;
  public function db_connect() {
    require_once('db_lib.php');
    $this->oDB = new db;
  }
	
  // This function is called automatically by the Phirehose class
  // when a new tweet is received with the JSON data in $status
  public function enqueueStatus($status) {
    $tweet_object = json_decode($status);
		
		// Ignore tweets without a properly formed tweet id value
    if (!(isset($tweet_object->id_str))) { return;}
		
    $tweet_id = $tweet_object->id_str;

    // If there's a ", ', :, or ; in object elements, serialize() gets corrupted 
    // You should also use base64_encode() before saving this
    $raw_tweet = base64_encode(serialize($tweet_object));
		
    $field_values = 'raw_tweet = "' . $raw_tweet . '", ' .
      'tweet_id = ' . $tweet_id;
    $this->oDB->insert('json_cache',$field_values);
  }
  // This function is called automatically by the Phirehose class
  // every 5 seconds. It can be used to reset the collection array	
  public function checkFilterPredicates() {
    $this->setTrack($this->get_keywords());
  }
		
  // Build an array of keywords for tweet collection
   public function get_keywords(){
	$query2 = "SELECT words
  	FROM collection_words
        WHERE words <> '' AND  is_active = 1";
	$result = $this->oDB->select($query2);
	if (mysqli_num_rows($result)==0) {
      // Exit if no collection words found	
      print "ERROR: No keywords found in collection_words table";
      exit;
    } else if (mysqli_num_rows($result)>400) {
      // Exit if keyword count exceeds API limit of 400
      print "ERROR: More than 400 keywords in collection_words table";
      exit;
    }
		
    // Create a keyword list
    $keyword_array = array();
    while ($row=mysqli_fetch_assoc($result)) {
       array_push($keyword_array, $row['words']);
    }		
    return $keyword_array;
  }
  
}

// Open a persistent connection to the Twitter streaming API
$stream = new Consumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);

// Establish a MySQL database connection
$stream->db_connect();

// The keywords for tweet collection are entered here as an array
// More keywords can be added as array elements
// For example: array('recipe','food','cook','restaurant','great meal')
// Set Dynamic Keyword


$dataB = $stream->get_keywords();
$stream->setTrack($dataB);

// Start collecting tweets
// Automatically call enqueueStatus($status) with each tweet's JSON data
$stream->consume();


?>
