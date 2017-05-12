<?php
/**
* parse_tweets_keyword.php
* Populate the database with new tweet data from the json_cache table
* Latest copy of this code: http://140dev.com/free-twitter-api-source-code-library/
* @author Adam Green <140dev@gmail.com>
* @license GNU Public License
* @version BETA 0.30
*/
set_time_limit(0);
require_once('140dev_config.php');
require_once('db_lib.php');
$oDB = new db;

// This should run continuously as a background process
while (True) {

  // Gather exclusion words into an array once per parsing cycle
  $query = "SELECT words, type
      FROM exclusion_words
      WHERE words <> ''";
  $result = $oDB->select($query);
  $exclusion_words = array();
  while($row = mysqli_fetch_assoc($result)) {
    $exclusion_words[strtolower($row['words'])] = $row['type'];
  }
	
  // Gather collection words into an array 
  $query = "SELECT words, type, out_words
      FROM collection_words
      WHERE words <> ''";
  $result = $oDB->select($query);
  $collection_words = array();
  while($row = mysqli_fetch_assoc($result)) {
    $collection_words[strtolower($row['words'])] = array( 'type' => $row['type'],
      'out_words' => strtolower($row['out_words']));
  }

  // Process all new tweets
  $query = 'SELECT cache_id, raw_tweet ' .
    'FROM json_cache';
  $result = $oDB->select($query);
  while($row = mysqli_fetch_assoc($result)) {
		
    $cache_id = $row['cache_id'];
    // Each JSON payload for a tweet from the API was stored in the database  
    // by serializing it as text and saving it as base64 raw data
    $tweet_object = unserialize(base64_decode($row['raw_tweet']));
		
    // Delete cached copy of tweet
    $oDB->select("DELETE FROM json_cache WHERE cache_id = $cache_id");
		
		// Limit tweets tgo a single language,
		// such as 'en' for English
		//if ($tweet_object->lang <> 'en') {continue;}
		
		// The streaming API sometimes sends duplicates, 
    // Test the tweet_id before inserting
    $tweet_id = $tweet_object->id_str;
    if ($oDB->in_table('tweets','tweet_id=' . $tweet_id )) {continue;}
	
	// Get the tweet text for collection and exclusion words testing
    if (isset($tweet_object->retweeted_status)) {
      // This is a retweet, so we need the original tweet text for testing
      // Retweet text may be clipped to allow for RT @[screen_name]:
      $test_text = $tweet_object->retweeted_status->text;
    } else {
      $test_text = $tweet_object->text;
    }
		
    // Reject tweets that don't match any collection words rules
    // Record details of tweets that do match any of them
    $match_collection_words = array();
    foreach($collection_words as $words => $rules) {
      // If valid collection words are found
      if (find_collection_words($words,$test_text,$rules['type'],$rules['out_words'])) {
        // Record the words for insertion into tweet_words table
        $match_collection_words[] = $words;
      }
    }
    // Skip this tweet if no valid matches found
    if (!$match_collection_words) {continue;}		
		
    // Reject tweets that contain exclusion words
    foreach($exclusion_words as $words => $type) {
      // if a match is found, use continue 2 to 
      // exit foreach loop and jump to top of while loop
      if (find_exclusion_words($words,$test_text,$type)) {continue 2;}
    }
		
    // Gather tweet data from the JSON object
    // $oDB->escape() escapes ' and " characters, and blocks characters that
    // could be used in a SQL injection attempt
   
		if (isset($tweet_object->retweeted_status)) {
      // This is a retweet
      // Use the original tweet's entities, they are more complete
      $entities = $tweet_object->retweeted_status->entities;
			$is_rt = 1;
	  } else {
	 	  $entities = $tweet_object->entities;
		  $is_rt = 0;
	  }
		
		$tweet_text = $oDB->escape($tweet_object->text);	
    $created_at = $oDB->date($tweet_object->created_at);
    if (isset($tweet_object->geo)) {
      $geo_lat = $tweet_object->geo->coordinates[0];
      $geo_long = $tweet_object->geo->coordinates[1];
    } else {
      $geo_lat = $geo_long = 0;
    } 
    $user_object = $tweet_object->user;
    $user_id = $user_object->id_str;
    $screen_name = $oDB->escape($user_object->screen_name);
    $name = $oDB->escape($user_object->name);
    $profile_image_url = $user_object->profile_image_url;

		
    // Add a new user row or update an existing one
    $field_values = 'screen_name = "' . $screen_name . '", ' .
      'profile_image_url = "' . $profile_image_url . '", ' .
      'user_id = ' . $user_id . ', ' .
      'name = "' . $name . '", ' .
      'location = "' . $oDB->escape($user_object->location) . '", ' . 
      'url = "' . $user_object->url . '", ' .
      'description = "' . $oDB->escape($user_object->description) . '", ' .
      'created_at = "' . $oDB->date($user_object->created_at) . '", ' .
      'followers_count = ' . $user_object->followers_count . ', ' .
      'friends_count = ' . $user_object->friends_count . ', ' .
      'statuses_count = ' . $user_object->statuses_count . ', ' . 
      'time_zone = "' . $user_object->time_zone . '", ' .
      'last_update = "' . $oDB->date($tweet_object->created_at) . '"' ;			

    if ($oDB->in_table('users','user_id="' . $user_id . '"')) {
      $oDB->update('users',$field_values,'user_id = "' .$user_id . '"');
    } else {			
      $oDB->insert('users',$field_values);
    }
		
    // Add the new tweet
	
    $field_values = 'tweet_id = ' . $tweet_id . ', ' .
        'tweet_text = "' . $tweet_text . '", ' .
        'created_at = "' . $created_at . '", ' .
        'geo_lat = ' . $geo_lat . ', ' .
        'geo_long = ' . $geo_long . ', ' .
        'user_id = ' . $user_id . ', ' .				
        'screen_name = "' . $screen_name . '", ' .
        'name = "' . $name . '", ' .
        'profile_image_url = "' . $profile_image_url . '", ' .
        'is_rt = ' . $is_rt;
			
    $oDB->insert('tweets',$field_values);
	
	// Record all collection words found in this tweet
    foreach ($match_collection_words as $words) {
    			
      $where = 'tweet_id=' . $tweet_id . ' ' .
        'AND words ="' . $words .'"';		
				
      if(! $oDB->in_table('tweet_words',$where)) {
			
        $field_values = 'tweet_id=' . $tweet_id . ', ' .
        'words="' . $words . '"';	

        $oDB->insert('tweet_words',$field_values);
      }
    }
		
    // The mentions, tags, and URLs from the entities object are also
    // parsed into separate tables so they can be data mined later
    foreach ($entities->user_mentions as $user_mention) {
		
      $where = 'tweet_id=' . $tweet_id . ' ' .
        'AND source_user_id=' . $user_id . ' ' .
        'AND target_user_id=' . $user_mention->id;		
					 
      if(! $oDB->in_table('tweet_mentions',$where)) {
			
        $field_values = 'tweet_id=' . $tweet_id . ', ' .
        'source_user_id=' . $user_id . ', ' .
        'target_user_id=' . $user_mention->id;	
				
        $oDB->insert('tweet_mentions',$field_values);
      }
    }
    foreach ($entities->hashtags as $hashtag) {
			
      $where = 'tweet_id=' . $tweet_id . ' ' .
        'AND tag="' . $hashtag->text . '"';		
					
      if(! $oDB->in_table('tweet_tags',$where)) {
			
        $field_values = 'tweet_id=' . $tweet_id . ', ' .
          'tag="' . $hashtag->text . '"';	
				
        $oDB->insert('tweet_tags',$field_values);
      }
    }
    foreach ($entities->urls as $url) {
		
      if (empty($url->expanded_url)) {
        $url = $url->url;
      } else {
        $url = $url->expanded_url;
      }
			
      $where = 'tweet_id=' . $tweet_id . ' ' .
        'AND url="' . $url . '"';		
					
      if(! $oDB->in_table('tweet_urls',$where)) {
        $field_values = 'tweet_id=' . $tweet_id . ', ' .
          'url="' . $url . '"';	
				
        $oDB->insert('tweet_urls',$field_values);
      }
    }		
  } 
		
  // You can adjust the sleep interval to handle the tweet flow and 
  // server load you experience
  sleep(1);
}
// Return 1 if match is found
// Return 0 if no match, or match containing out word
function find_collection_words($words,$tweet_text,$type,$out_words) {
  // Remove extra spaces from words and tweet text
  $words = trim(preg_replace('/\s+/',' ', $words));
  $tweet_text = trim(preg_replace('/\s+/',' ', $tweet_text));
  $out_words = trim(preg_replace('/\s+/',' ', $out_words));
	
  // Escape any characters in collection words that may 
  // conflict with a regex pattern used by preg_match
  $words = preg_quote($words, '/');	

  $match = 0;
  if ($type=='phrase') {
    // Exact match of collection phrase is required
    $match = preg_match('/\b' . $words . '\b/i',$tweet_text);
  } else {
    // Break apart the words on space boundaries 
    // and check for each of them separately
    $words_array = explode(' ',$words);
    foreach($words_array as $word) {
      if (!preg_match('/' . $word . '/i',$tweet_text)) {
        // One of the words is missing, so we're done
        return 0;
      } 
    }
    $match = 1;
  }

  if($match && !empty($out_words)) {
    // Check for out words
    // Break apart the out words on comma boundaries 
    // and check for each of them separately
		
    $out_words_array = explode(',',$out_words);
    foreach($out_words_array as $out_word) {

      // Escape any characters in out_word that may 
      // conflict with a regex pattern used by preg_match
      $out_word = preg_quote($out_word, '/');

      if (preg_match('/' . $out_word . '/i',$tweet_text)) {
        // One of the out_words is found, so we're done
        return 0;
      } 
    }
  }
	
  return $match;
}

// Return 1 if match is found, 0 if not
function find_exclusion_words($words,$tweet_text,$type) {
  // Remove extra spaces from words and tweet text
  $words = trim(preg_replace('/\s+/',' ', $words));
  $tweet_text = trim(preg_replace('/\s+/',' ', $tweet_text));

  // Escape any characters in the exclusion word that may 
  // conflict with a regex pattern used by preg_match
  $words = preg_quote($words, '/');
	
  if ($type == 'partial') {
    return preg_match('/' . $words . '/i',$tweet_text);
  } elseif ($type='exact') {
    return preg_match('/\b' . $words . '\b/i',$tweet_text);
  }
}
?>