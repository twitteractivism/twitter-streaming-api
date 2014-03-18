<?php
/**
* 140dev_config.php
* Constants for the entire 140dev Twitter framework
* You MUST modify these to match your server setup when installing the framework
* 
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/

// OAuth settings for connecting to the Twitter streaming API
// Fill in the values for a valid Twitter app
define('TWITTER_CONSUMER_KEY','greY2OW1YysbWDOMJanFg');
define('TWITTER_CONSUMER_SECRET','8Bfmv6Ch7CCrUes8OZqQqcJRSJaUPPaYAz9r9fM4A');
define('OAUTH_TOKEN','65203002-pcRWfuj7Lruhfhm3jkB2eQiiTpKiDyzgpQ3b58VIK');
define('OAUTH_SECRET','UbAZQpBOt2Q3ckHkeHLYvInrXyXk1iStC3RloZfruvDtH');

// Settings for monitor_tweets.php
define('TWEET_ERROR_INTERVAL',10);
// Fill in the email address for error messages
define('TWEET_ERROR_ADDRESS','*****');
?>