<?php 

require_once('140dev_config.php');
require_once('db_lib.php');
$oDB = new db;

/* if (PURGE_INTERVAL == 0) {
  print "PURGE_INTERVAL is set to 0 days in 140dev_config.php";
  exit;
}*/
/* DELETE FROM tweet_mentions WHERE tweet_mentions.tweet_id NOT IN (SELECT f.tweet_id FROM tweets f);
DELETE FROM tweet_tags WHERE tweet_tags.tweet_id NOT IN (SELECT f.tweet_id FROM tweets f);
DELETE FROM tweet_urls WHERE tweet_urls.tweet_id NOT IN (SELECT f.tweet_id FROM tweets f);
DELETE FROM tweet_words WHERE tweet_words.tweet_id NOT IN (SELECT f.tweet_id FROM tweets f);
DELETE FROM users WHERE users.user_id NOT IN (SELECT f.user_id FROM tweets f); */
// Delete old tweets
// Alternate delete query 
//SELECT * FROM `tweets` WHERE `tweet_text` LIKE '%pmoindia%' (replace pmoindia with the keyword tweets you want to delete)
$query = 'DELETE FROM tweets 
	WHERE created_at < now() - interval ' . PURGE_INTERVAL . ' day';
$oDB->select($query);

// Delete all related data that no longer has a matching tweet
// Alternate query as follows 
// DELETE FROM tweet_mentions
// WHERE tweet_mentions.tweet_id NOT IN (SELECT f.tweet_id FROM tweets f)
$query = 'DELETE FROM tweet_mentions 
	WHERE NOT EXISTS (
		SELECT NULL 
		FROM tweets
		WHERE tweets.tweet_id = tweet_mentions.tweet_id)';
$oDB->select($query);

$query = 'DELETE FROM tweet_tags 
	WHERE NOT EXISTS (
		SELECT NULL FROM tweets
	    WHERE tweets.tweet_id = tweet_tags.tweet_id)';
$oDB->select($query);

$query = 'DELETE FROM tweet_urls 
	WHERE NOT EXISTS (
		SELECT NULL FROM tweets
	    WHERE tweets.tweet_id = tweet_urls.tweet_id)';
$oDB->select($query);

$query = 'DELETE FROM tweet_words
	WHERE NOT EXISTS (	
		SELECT NULL 
		FROM tweets
		WHERE tweets.tweet_id = tweet_words.tweet_id)';
$oDB->select($query);

$query = 'DELETE FROM users
		WHERE NOT EXISTS 
        (SELECT NULL FROM tweets
        WHERE tweets.user_id = users.user_id)';
$oDB->select($query);

?>