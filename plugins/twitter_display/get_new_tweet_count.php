<?php 
/**
* get_new_tweet_count.php
* Return the count of new tweets as text/html
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/

require_once('twitter_display_config.php' );
require_once('../../db/140dev_config.php');
require_once('../../db/db_lib.php'); 
$oDB = new db;

// This is called by site.js with an argument of first containing a tweet_id:
// get_new_tweet_count.php?first=[tweet_id]
// The count of any tweets with a higher tweet_id is returned

// Fail if no first argument is received
if (!isset($_GET['first'])) {
  return;
}

$query = 'SELECT tweet_id ' .
  'FROM tweets ' .  
  'WHERE tweet_id > "' . $_GET['first'] . '"';
 
$result = $oDB->select($query);
$new_tweet_count = mysqli_num_rows($result);
if ($new_tweet_count==0) {
  print '';
} elseif ($new_tweet_count==1) {
  print NEW_TWEET_MESSAGE;
} else {
  print $new_tweet_count . NEW_TWEETS_MESSAGE;
}

?>