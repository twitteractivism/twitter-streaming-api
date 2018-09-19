<?php
/**
* twitter_display_config.php
* Configuration options for the Twitter display plugin
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/

// URL for site.js to call the framework with Ajax 
// You MUST fill this in with the correct URL for the 140dev code
// For example, if the code is at http://mydomain.com/twitter_display/
// change to define('AJAX_URL', 'http://mydomain.com/twitter_display/');
define('AJAX_URL', '*****');

// Number of tweets displayed when the tweet list is first displayed
// and when the View More button is clicked
define('TWEET_DISPLAY_COUNT', 30);

// Text for View More button
define ('MORE_BUTTON', 'tweets displayed - View More');

// Title for hashtag links
define('HASHTAG_TITLE', 'View tweets for tag: ');

// Title for user mention links
define('USER_MENTION_TITLE', 'View tweets by user: ');

// Title for tweet date
define('TWEET_DISPLAY_TITLE', 'View this tweet on Twitter.com');

// Number of seconds between checking server for new tweet count
define('NEW_COUNT_REFRESH', 30);

// Message for new tweet count at top of tweet list
define('NEW_TWEET_MESSAGE', 
  '1 new tweet available. <a href="index.html">Refresh</a> to see it.');	
define('NEW_TWEETS_MESSAGE', 
  ' new tweets available. <a href="index.html">Refresh</a> to see them.');		
?>