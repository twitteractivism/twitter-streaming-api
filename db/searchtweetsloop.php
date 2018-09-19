<?php
/**
* searchtweetsloop.php
* Program to read tweets for past week and if any new user is found, search his timeline for chosen keywords and insert tweets having the desired keyword combinations.
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/

chdir('');//Enter absolute path to the present working directory between single quotes
include '../log-register.php';
include '../inc/twitterc.php';
//Enter consumer key and consumer secret of your twitter app
define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
?>
<?php
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "error_log");
ini_set('display_errors', 1);
$output = "";
$date=date('Y-m-d');
set_time_limit(0);
require_once('140dev_config.php');
require_once('db_lib.php');
$oDB = new db;
// To get all keywords in an array
$totaltweets=0;
$noofapicallsmadeuser=0;
function get_keywords(){
        global $oDB;
	$query2 = "SELECT words
  	FROM collection_words
        WHERE words <> '' AND  is_active = 1";
	$result = $oDB->select($query2);
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
  $countb=0;
  $resultarray= get_keywords();
  $count=count($resultarray);
  include '../inc/twitterc.php';
  require_once('TwitterAPIExchange.php');
  $count= count($resultarray);
  //Iterating through all keywords infinitely till no tweets are remaining
  //print_r($resultarray);
  //die();
  $noofapicallsmade=0;
  
  
  

/**
*	Get the Bearer Token, this is an implementation of steps 1&2
*	from https://dev.twitter.com/docs/auth/application-only-auth or https://developer.twitter.com/en/docs/basics/authentication/overview/application-only or https://gist.github.com/lgladdy/5141615
*/
function get_bearer_token(){
	// Step 1
	// step 1.1 - url encode the consumer_key and consumer_secret in accordance with RFC 1738
	$encoded_consumer_key = urlencode(CONSUMER_KEY);
	$encoded_consumer_secret = urlencode(CONSUMER_SECRET);
	// step 1.2 - concatinate encoded consumer, a colon character and the encoded consumer secret
	$bearer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
	// step 1.3 - base64-encode bearer token
	$base64_encoded_bearer_token = base64_encode($bearer_token);
	// step 2
	$url = "https://api.twitter.com/oauth2/token"; // url to send data to for authentication
	$headers = array( 
		"POST /oauth2/token HTTP/1.1", 
		"Host: api.twitter.com", 
		"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
		"Authorization: Basic ".$base64_encoded_bearer_token,
		"Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
	); 
	$ch = curl_init();  // setup a curl
	curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
	curl_setopt($ch, CURLOPT_POST, 1); // send as post
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
	curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials"); // post body/fields to be sent
	$header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$retrievedhtml = curl_exec ($ch); // execute the curl
	curl_close($ch); // close the curl
	$output = explode("\n", $retrievedhtml);
	$bearer_token = '';
	foreach($output as $line)
	{
		if($line === false)
		{
			// there was no bearer token
		}else{
			$bearer_token = $line;
		}
	}
	$bearer_token = json_decode($bearer_token);
	return $bearer_token->{'access_token'};
}
/**
* Invalidates the Bearer Token
* Should the bearer token become compromised or need to be invalidated for any reason,
* call this method/function.
*/
function invalidate_bearer_token($bearer_token){
	$encoded_consumer_key = urlencode(CONSUMER_KEY);
	$encoded_consumer_secret = urlencode(CONSUMER_SECRET);
	$consumer_token = $encoded_consumer_key.':'.$encoded_consumer_secret;
	$base64_encoded_consumer_token = base64_encode($consumer_token);
	// step 2
	$url = "https://api.twitter.com/oauth2/invalidate_token"; // url to send data to for authentication
	$headers = array( 
		"POST /oauth2/invalidate_token HTTP/1.1", 
		"Host: api.twitter.com", 
		"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
		"Authorization: Basic ".$base64_encoded_consumer_token,
		"Accept: */*", 
		"Content-Type: application/x-www-form-urlencoded"
	); 
    
	$ch = curl_init();  // setup a curl
	curl_setopt($ch, CURLOPT_URL,$url);  // set url to send to
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
	curl_setopt($ch, CURLOPT_POST, 1); // send as post
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
	curl_setopt($ch, CURLOPT_POSTFIELDS, "access_token=".$bearer_token.""); // post body/fields to be sent
	$header = curl_setopt($ch, CURLOPT_HEADER, 1); // send custom headers
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$retrievedhtml = curl_exec ($ch); // execute the curl
	curl_close($ch); // close the curl
	return $retrievedhtml;
}
/**
* Search
* Basic Search of the Search API
* Based on https://dev.twitter.com/docs/api/1.1/get/search/tweets
*/
function search_for_a_term($bearer_token, $query, $max_id, $result_type='recent', $count='100'){
	$url = "https://api.twitter.com/1.1/search/tweets.json"; // base url
	$q = urlencode(trim($query)); // query term
	$formed_url ='?q='.$q; // fully formed url
	if($result_type!='mixed'){$formed_url = $formed_url.'&result_type='.$result_type;} // result type - mixed(default), recent, popular
	if($count!='15'){$formed_url = $formed_url.'&count='.$count;} // results per page - defaulted to 15
	$formed_url = $formed_url.'&include_entities=true&max_id=' .$max_id; // makes sure the entities are included, note @mentions are not included see documentation
	$headers = array( 
		"GET /1.1/search/tweets.json".$formed_url." HTTP/1.1", 
		"Host: api.twitter.com", 
		"User-Agent: jonhurlock Twitter Application-only OAuth App v.1",
		"Authorization: Bearer ".$bearer_token
	);
	$ch = curl_init();  // setup a curl
	curl_setopt($ch, CURLOPT_URL,$url.$formed_url);  // set url to send to
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // set custom headers
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return output
	$retrievedhtml = curl_exec ($ch); // execute the curl
	curl_close($ch); // close the curl
	return $retrievedhtml;
}
// lets run a search.
$bearer_token = get_bearer_token(); // get the bearer token
//invalidate_bearer_token($bearer_token); // invalidate the token
  echo "<b>Starting time " . date('Y-m-d H:i:s') . "</b></br>";
  $myfile = fopen("cronlog/" . date('Y-m') ."_cronjob.txt", "a+") or die("Unable to open file!");
  $txt = "Starting time searchtweetsloop.php  " . date('Y-m-d H:i:s') . "\n\n";
  fwrite($myfile, $txt);
  foreach($resultarray as $item)
  {
    //For output  
    echo "Count is $count <br/>";
    echo $item . "<br/>";
    $max_id = null;
    while(1){
	// replace username with username of your server where your files are hosted
    exec("ps -u username xuwww|grep _keyword.php|grep -v grep", $output);
    //if background job get_tweets_keyword.php and parse_tweets_keyword.php are running then if is executed
    if(count($output) > 0)
    {
        $getfield = "?q=$item&result_type=recent&max_id=$max_id&count=100";
        //For output
        echo $getfield ."<br/>";
    }
    else
    {
        $getfield = "?q=$item&result_type=recent&max_id=$max_id&count=100";
        //For output
        echo $getfield ."<br/>";
    }

    $string = search_for_a_term($bearer_token, $item, $max_id);
    $string= json_decode($string,true);
    //output for user timeline
    //print_r($string);
    if(isset($string['errors']))
    {
       foreach($string['errors'] as $item)
       {
            $error=$item['message'];
            echo $error ."<br/>";
       }
       //echo "Twitter api error";
       die();
    }
    if(empty($string))
    {
        --$count;
        break;
    }
    //setting max id to get next page
    $max_id1=$max_id;
    foreach($string['statuses'] as $item1)
    {
        $tweet_id=$item1['id_str'];
        //For output for tweet id and time
        $date1=$oDB->date($item1['created_at']);
        $date1=date('Y-m-d',strtotime($date1));
        $start = strtotime($date);
        $end = strtotime($date1);
        $days_between = ceil(abs($end - $start) / 86400);
        if($days_between>6)
        {
            $countb=1;
            break;
        }
            
        echo "Tweet no. checked " . ++$totaltweets. " " . $item ." " . $tweet_id . " " . $oDB->date($item1['created_at']) ."<br/>";
        $max_id=$tweet_id;
        $query="select * from tweets where tweet_id=$tweet_id";
        $result = $oDB->select($query);
        if (mysqli_num_rows($result)==0) 
        {
            // This is a retweet
            // Use the original tweet's entities, they are more complete
            if(isset($item1['retweeted_status']))
            {
                $is_rt=1;
                $entities = $item1['retweeted_status']['entities'];
            }
            else
            {
                $is_rt=0;
                $entities = $item1['entities'];
            }
            //storing the data in tables
			echo "<font style=\"color:#A52A2A\">";
echo "Inserting tweet id " . $tweet_id . " having text " . $item1['text'] ." Created at " . $oDB->date($item1['created_at']) ."<br/>";
            echo "</font>";
            $tweet_text=$item1['text'];
            $created_at = $oDB->date($item1['created_at']);
            $geo_lat=0;
            $geo_long=0;
            $user_id=$item1['user']['id_str'];
            $name=$oDB->escape($item1['user']['name']);
            $screen_name=$oDB->escape($item1['user']['screen_name']);
            $profile_image_url=$oDB->escape($item1['user']['profile_image_url']);
            $field_values = 'tweet_id = ' . $tweet_id . ', ' .
            'tweet_text = "' . $oDB->escape($tweet_text) . '", ' .
            'created_at = "' . $oDB->date($created_at) . '", ' .
            'geo_lat = ' . $geo_lat . ', ' .
            'geo_long = ' . $geo_long . ', ' .
            'user_id = ' . $user_id . ', ' .				
            'screen_name = "' . $oDB->escape($screen_name) . '", ' .
            'name = "' . $oDB->escape($name) . '", ' .
            'profile_image_url = "' . $oDB->escape($profile_image_url) . '", ' .
            'is_rt = ' . $is_rt;
            try
            {
                $oDB->insert('tweets',$field_values);
            }
            catch(Exception $e)
            {
                $txt= $e->getMessage();
                fwrite($myfile, $txt);
            }
            // Add a new user row or update an existing one
            $field_values = 'screen_name = "' . $oDB->escape($screen_name) . '", ' .
            'profile_image_url = "' . $oDB->escape($profile_image_url) . '", ' .
            'user_id = ' . $user_id . ', ' .
            'name = "' . $oDB->escape($name) . '", ' .
            'location = "' . $oDB->escape($item1['user']['location']) . '", ' . 
            'url = "' . $oDB->escape($item1['user']['url']) . '", ' .
            'description = "' . $oDB->escape($item1['user']['description']) . '", ' .
            'created_at = "' . $oDB->date($item1['user']['created_at']) . '", ' .
            'followers_count = ' . $item1['user']['followers_count'] . ', ' .
            'friends_count = ' . $item1['user']['friends_count'] . ', ' .
            'statuses_count = ' . $item1['user']['statuses_count'] . ', ' . 
            'time_zone = "' . $item1['user']['time_zone'] . '", ' .
            'last_update = "' . $oDB->date($item1['created_at']) . '"' ;			
            
        if ($oDB->in_table('users','user_id="' . $user_id . '"')) {
          try
          {
            $oDB->update('users',$field_values,'user_id = "' .$user_id . '"');
          }
          catch(Exception $e)
            {
                $txt= $e->getMessage();
                fwrite($myfile, $txt);
            }
        } 
        else
        {
            try
            {
                $oDB->insert('users',$field_values);
            }
            catch(Exception $e)
            {
                $txt= $e->getMessage();
                fwrite($myfile, $txt);
            }
        }
            foreach ($entities['user_mentions'] as $user_mention) {
		
            $where = 'tweet_id=' . $tweet_id . ' ' .
              'AND source_user_id=' . $user_id . ' ' .
              'AND target_user_id=' . $user_mention['id'];		

            if(! $oDB->in_table('tweet_mentions',$where)) {

              $field_values = 'tweet_id=' . $tweet_id . ', ' .
            'source_user_id=' . $user_id . ', ' .
            'target_user_id=' . $user_mention['id'] .', '.
            'created_at = "' . $created_at . '"';
            try
            {
                $oDB->insert('tweet_mentions',$field_values);
            }
            catch(Exception $e)
            {
                $txt= $e->getMessage();
                fwrite($myfile, $txt);
            }
            }
          }
              foreach ($entities['hashtags'] as $hashtag) {

                $where = 'tweet_id=' . $tweet_id . ' ' .
                  'AND tag="' . $hashtag['text'] . '"';		

                if(! $oDB->in_table('tweet_tags',$where)) {

                  $field_values = 'tweet_id=' . $tweet_id . ', ' .
                'tag="' . $hashtag['text'] . '", ' .
                'created_at = "' . $created_at . '"';
                  try
                  {
                    $oDB->insert('tweet_tags',$field_values);
                  }
                catch(Exception $e)
                {
                    $txt= $e->getMessage();
                    fwrite($myfile, $txt);
                }
                }
              }
              foreach ($entities['urls'] as $url) {

                if (empty($url['expanded_url'])) {
                  $url = $oDB->escape($url['url']);
                } else {
                  $url = $oDB->escape($url['expanded_url']);
                }

                $where = 'tweet_id=' . $tweet_id . ' ' .
                  'AND url="' . $url . '"';		

                if(! $oDB->in_table('tweet_urls',$where)) {
                  $field_values = 'tweet_id=' . $tweet_id . ', ' .
                  'url="' . $url . '", ' .
                   'created_at = "' . $created_at . '"';
                  try
                  {
                    $oDB->insert('tweet_urls',$field_values);
                  }
                catch(Exception $e)
                {
                    $txt= $e->getMessage();
                    fwrite($myfile, $txt);
                }
                }

                  }
                  $words=$item;
                  $where = 'tweet_id=' . $tweet_id . ' ' .
                  'AND words ="' . $words .'"';		
				
                  if(! $oDB->in_table('tweet_words',$where)) {
			
                $field_values = 'tweet_id=' . $tweet_id . ', ' .
                'words="' . $words . '", ' .
                'created_at = "' . $created_at . '"';
                try
                {
                    $oDB->insert('tweet_words',$field_values);
                }
                catch(Exception $e)
                {
                    $txt= $e->getMessage();
                    fwrite($myfile, $txt);
                }
              }
                  $where = 'user_id=' . $user_id ;
                  
            if(! $oDB->in_table('users',$where)) {
                $field_values = 'screen_name = "' . $oDB->escape($screen_name) . '", ' .
                            'profile_image_url = "' . $oDB->escape($profile_image_url) . '", ' .
                            'user_id = ' . $user_id . ', ' .
                            'name = "' . $oDB->escape($name) . '", ' .
                            'location = "' . $oDB->escape($item1['user']['location']) . '", ' . 
                            'url = "' . $oDB->escape($item1['user']['url']) . '", ' .
                            'description = "' . $oDB->escape($item1['user']['description']) . '", ' .
                            'created_at = "' . $oDB->date($item1['user']['created_at']) . '", ' .
                            'followers_count = ' . $item1['user']['followers_count'] . ', ' .
                            'friends_count = ' . $item1['user']['friends_count'] . ', ' .
                            'statuses_count = ' . $item1['user']['statuses_count'] . ', ' . 
                            'time_zone = "' . $item1['user']['time_zone'] . '", ' .
                            'last_update = "' . $oDB->date($item1['created_at']) . '"' ;
                 if ($oDB->in_table('users','user_id="' . $user_id . '"')) {
                                echo "Updating the user data " ."<br/>";
                            $oDB->update('users',$field_values,'user_id = "' .$user_id . '"');
                            } else {			
                                //echo "Inserting the user data " . "<br/>";
                            //$oDB->insert('users',$field_values);
                            }
                $usertweets=0;
                //Max id of first tweet is null
                $max_id1=null;
                $usercount=0;
                //reading the user timeline 20 times in a loop of new user, if timeline is less, loop exits early
                while(++$usercount<=20){
                echo "<b>User iteration $usercount<br/></b>";
                $url="https://api.twitter.com/1.1/statuses/user_timeline.json";
                $requestMethod = "GET";
                if($max_id1==null)
                {
                    //first time
                    $getfield = "?user_id=$user_id&exclude_replies=false&count=200";
                }
                else
                {
                    //next time max id of last tweet returned from previous result is used as max_id
                    $getfield = "?user_id=$user_id&exclude_replies=false&max_id=$max_id1&count=200";
                }
                //For output
                echo "<b>User data $getfield " . "<br/></b>";
                $twitter = new TwitterAPIExchange($settings);
                $string1 = json_decode($twitter->setGetfield($getfield)
                ->buildOauth($url, $requestMethod)
                ->performRequest(),$assoc=true);
                if(empty($string1))
                {
                    echo "No data found for this user </br>";
                }
                else
                {
                    echo "Data found for user $user_id";
                }
                if(!isset($string1['errors']))
                {
                $max_id2=$max_id1;
                echo "<b>Previous max id is $max_id2<br/></b>";
                foreach($string1 as $item2)
                {   
                    echo "Tweet id of user $user_id is " . $item2['id_str'] . "<br/>";
                    $max_id1=$item2['id_str'];
                    if(isset($item2['retweeted_status']))
                    {
                        $is_rt=1;
                        $entities = $item2['retweeted_status']['entities'];
                    }
                
                    else
                    {
                        $is_rt=0;
                        $entities = $item2['entities'];
                    }
                    $count1=0;
                    $pos=0;
                    if(isset($item2['tweet_text']))
                    {
                        $tweet_text=$item2['tweet_text'];
                        $pos = stripos($tweet_text,$item);
                    }
                    if($pos>0)
                    {
                        $count1=1;
                        echo "<br/><b>User tweet no. ++$usertweets Keywrod $item found in $tweet_text " . "<br/></b>";
                    }
                    if($count1==1)
                    {
                        $tweet_id=$item2['id_str'];
                        $query="select * from tweets where tweet_id=$tweet_id";
                        $result = $oDB->select($query);
                        if (mysqli_num_rows($result)==0) 
                        {
                            // This is a retweet
                            // Use the original tweet's entities, they are more complete
                            if(isset($item2['retweeted_status']))
                            {
                                $is_rt=1;
                                $entities = $item2['retweeted_status']['entities'];
                            }
                            else
                            {
                                $is_rt=0;
                                $entities = $item2['entities'];
                            }
                            //storing the data in tables
                            $created_at = $oDB->date($item2['created_at']);
                            $tweet_text=$item2['text'];
                            $created_at = $oDB->date($item2['created_at']);
                            $geo_lat=0;
                            $geo_long=0;
                            $user_id=$item2['user']['id_str'];
                            $name=$oDB->escape($item2['user']['name']);
                            $screen_name=$oDB->escape($item2['user']['screen_name']);
                            $profile_image_url=$oDB->escape($item2['user']['profile_image_url']);
                            $field_values = 'tweet_id = ' . $tweet_id . ', ' .
                            'tweet_text = "' . $oDB->escape($tweet_text) . '", ' .
                            'created_at = "' . $oDB->date($created_at) . '", ' .
                            'geo_lat = ' . $geo_lat . ', ' .
                            'geo_long = ' . $geo_long . ', ' .
                            'user_id = ' . $user_id . ', ' .				
                            'screen_name = "' . $oDB->escape($screen_name) . '", ' .
                            'name = "' . $oDB->escape($name) . '", ' .
                            'profile_image_url = "' . $oDB->escape($profile_image_url) . '", ' .
                            'is_rt = ' . $is_rt;
                            try
                            {
                                $oDB->insert('tweets',$field_values);
                            }
                            catch(Exception $e)
                            {
                                $txt= $e->getMessage();
                                fwrite($myfile, $txt);
                            }
                            // Add a new user row or update an existing one
                            $field_values = 'screen_name = "' . $oDB->escape($screen_name) . '", ' .
                            'profile_image_url = "' . $oDB->escape($profile_image_url) . '", ' .
                            'user_id = ' . $user_id . ', ' .
                            'name = "' . $oDB->escape($name) . '", ' .
                            'location = "' . $oDB->escape($item1['user']['location']) . '", ' . 
                            'url = "' . $oDB->escape($item1['user']['url']) . '", ' .
                            'description = "' . $oDB->escape($item1['user']['description']) . '", ' .
                            'created_at = "' . $oDB->date($item1['user']['created_at']) . '", ' .
                            'followers_count = ' . $item1['user']['followers_count'] . ', ' .
                            'friends_count = ' . $item1['user']['friends_count'] . ', ' .
                            'statuses_count = ' . $item1['user']['statuses_count'] . ', ' . 
                            'time_zone = "' . $item1['user']['time_zone'] . '", ' .
                            'last_update = "' . $oDB->date($item1['created_at']) . '"' ;
                            
                            if ($oDB->in_table('users','user_id="' . $user_id . '"')) {
                                echo "Updating the user data " ."<br/>";
                            try
                            {
                                $oDB->update('users',$field_values,'user_id = "' .$user_id . '"');
                            }
                            catch(Exception $e)
                            {
                                $txt= $e->getMessage();
                                fwrite($myfile, $txt);
                            }
                            } else {			
                                echo "Inserting the user data " . "<br/>";
                                echo "Field values are "  . $field_values . "<br/>";
                            try
                            {
                                $oDB->insert('users',$field_values);
                            }
                            catch(Exception $e)
                            {
                                $txt= $e->getMessage();
                                fwrite($myfile, $txt);
                            }
                            }

                            foreach ($entities['user_mentions'] as $user_mention) {
                            $where = 'tweet_id=' . $tweet_id . ' ' .
                              'AND source_user_id=' . $user_id . ' ' .
                              'AND target_user_id=' . $user_mention['id'];		

                            if(! $oDB->in_table('tweet_mentions',$where)) {

                             $field_values = 'tweet_id=' . $tweet_id . ', ' .
                            'source_user_id=' . $user_id . ', ' .
                            'target_user_id=' . $user_mention['id'] .', '.
                            'created_at = "' . $created_at . '"';
                              try
                              {
                                $oDB->insert('tweet_mentions',$field_values);
                              }
                              catch(Exception $e)
                              {
                                  $txt= $e->getMessage();
                                  fwrite($myfile, $txt);
                              }
                            }
                          }
                              foreach ($entities['hashtags'] as $hashtag) {

                                $where = 'tweet_id=' . $tweet_id . ' ' .
                                  'AND tag="' . $hashtag['text'] . '"';		

                                if(! $oDB->in_table('tweet_tags',$where)) {

                                   $field_values = 'tweet_id=' . $tweet_id . ', ' .
                                   'tag="' . $hashtag['text'] . '", ' .
                                   'created_at = "' . $created_at . '"';
                                  try
                                  {
                                    $oDB->insert('tweet_tags',$field_values);
                                  }
                                  catch(Exception $e)
                                  {
                                      $txt= $e->getMessage();
                                      fwrite($myfile, $txt);
                                  }
                                }
                              }
                              foreach ($entities['urls'] as $url) {

                                if (empty($url['expanded_url'])) {
                                  $url = $url['url'];
                                } else {
                                  $url = $url['expanded_url'];
                                }

                                $where = 'tweet_id=' . $tweet_id . ' ' .
                                  'AND url="' . $url . '"';		

                                if(! $oDB->in_table('tweet_urls',$where)) {
                                  $field_values = 'tweet_id=' . $tweet_id . ', ' .
                                  'url="' . $url . '", ' .
                                  'created_at = "' . $created_at . '"';
                                  try
                                  {
                                    $oDB->insert('tweet_urls',$field_values);
                                  }
                                  catch(Exception $e)
                                  {
                                      $txt= $e->getMessage();
                                      fwrite($myfile, $txt);
                                  }
                                }

                                  }
                                  $words=$item;
                                $where = 'tweet_id=' . $tweet_id . ' ' .
                                'AND words ="' . $words .'"';		

                                if(! $oDB->in_table('tweet_words',$where)) {

                              $field_values = 'tweet_id=' . $tweet_id . ', ' .
                              'words="' . $words . '", ' .
                              'created_at = "' . $created_at . '"';
                              try
                              {
                                $oDB->insert('tweet_words',$field_values);
                              }
                              catch(Exception $e)
                              {
                                  $txt= $e->getMessage();
                                  fwrite($myfile, $txt);
                              }
                            }

                        }  
                    }
                }
                 echo "<b>New max id is $max_id1<br/></b>";
                 if($max_id2==$max_id1)    
                 {
                     break;
                 }
                } 
                echo "<b>Sleeping for 1 second<br/></b>";
                sleep(1);
                echo "<b>Total number of requests for GET statuses/user_timeline "  . ++$noofapicallsmadeuser ."<br/></b>"; 
                }
           }    
        }
        if($countb==1)
        {
            break;
        }
        } 
    if($max_id1==$max_id)
    {
        if($countb==1)
        {
            $countb=0;
        }
        //For output  
        echo "Max id matching one keyword is done count is $count $max_id1, $max_id, $count, $item<br/>";
        --$count;
        break;
    }
    echo "<b>No. of api calls from GET search/tweets " . ++$noofapicallsmade . "<br/></b>";
    echo "<b>Total number of requests for GET statuses/user_timeline "  . $noofapicallsmadeuser ."<br/></b>"; 
    sleep(2);
    } 
    if($count==0)
    {
        //For output exiting when all keywords are done.
        echo "Count is $count exiting<br/> $item";
        echo "<b>Ending time " . date('Y-m-d H:i:s') . "</b></br>";
        $txt = "Ending time searchtweetsloop.php " . date('Y-m-d H:i:s') . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        invalidate_bearer_token($bearer_token);
        die();
    }
    
    sleep(2);
  }