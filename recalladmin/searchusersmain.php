<?php
/**
* searchusersmain.php
* Program to retrieve users timeline and insert matching tweets having keyword / keyword combination chonsen by admin
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
//Enter absolute path to db folder
chdir('/path/to/folder/db');

include '../log-register.php';
session_start();
if(!isset($_SESSION['user_id']))
{
  die();
}
?>

<?php

print_r($_POST);
mb_internal_encoding('UTF-8');
$user_id1='';
$user_name1='';

if(!empty($_POST['userid']))
{
    $user_id1=$_POST['userid'];
}
else
{
    $user_name1=$_POST['username'];
}

set_time_limit(0);

require_once('../db/140dev_config.php');
require_once('../db/db_lib.php');

$oDB = new db;

// To get all keywords in an array
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
    } 
	else if (mysqli_num_rows($result)>400) {
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

  $resultarray= get_keywords();

  //print_r($resultarray);

  $count=count($resultarray);

  include '../inc/twitterc.php';
  require_once('../TwitterAPIExchange.php');

  $count= count($resultarray);
  $noofapicallsmade=0;
  
  echo "<b>Starting time " . date('Y-m-d H:i:s') . "</b></br>";
  $myfile = fopen("cronlog/" . date('Y-m') ."_cronjob.txt", "a+") or die("Unable to open file!");
  $txt = "Starting time searchusers.php  " . date('Y-m-d H:i:s') . "\n\n";
  fwrite($myfile, $txt);
  print_r($resultarray);
  foreach($resultarray as $item)
  {
    echo "count is $count " . "<br/>";
    $max_id=null;
    $totaltweets=0;

    while(1){
   //For output
    echo "Item is $item" ."<br/><br/>";
    $url="https://api.twitter.com/1.1/statuses/user_timeline.json";
        $requestMethod = "GET";
        if(!empty($_POST['userid']))
        {
                if($max_id==null)
                {
                    $getfield = "?user_id=$user_id1&exclude_replies=false&count=200";
                }
                else
                {
                    $getfield = "?user_id=$user_id1&exclude_replies=false&max_id=$max_id&count=200";
                }

                //For output
                echo $getfield . "<br/>";
        }
        else
        if(!empty($_POST['username']))
        {
                if($max_id==null)
                {
                    $getfield = "?screen_name=$user_name1&exclude_replies=false&count=200";
                }
                else
                {
                    $getfield = "?screen_name=$user_name1&exclude_replies=false&max_id=$max_id&count=200";
                }

                //For output
                echo $getfield;
        }

        $twitter = new TwitterAPIExchange($settings);
        $string = json_decode($twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest(),$assoc=true);

        //print_r($string);

        echo "Item $item processing <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";

    if(isset($string['errors']))
    {
            echo "Twitter api error";
            die();
    }

    //setting max id to get next page
    $max_id1=$max_id;

    foreach($string as $item2)
    {   
        $max_id=$item2['id_str'];
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
        $tweet_id=$item2['id_str'];
        $tweet_text=$item2['text'];
        $created_at=$item2['created_at'];
        $pos = stripos($tweet_text,$item);
        echo "Tweet no. " . ++$totaltweets ." for item $item  " . $tweet_text . " Created at " . $created_at ."<br/>";

        if($pos>0)
        {
            echo $tweet_id . " one tweet found in $item <br/>";
            $count1=1;
        }

        //echo "Count 1 is $count1 ". "<br/>";
        if($count1==1)
        {
            $tweet_id=$item2['id_str'];
			
            //For output
            $max_id=$tweet_id;

            $query="select * from tweets where tweet_id=$tweet_id";

            $result = $oDB->select($query);
            //print_r($result);
            //storing the data in tables
                $tweet_text=$item2['text'];
                $created_at = $oDB->date($item2['created_at']);

                //For output
                echo $tweet_text . " " . $created_at;

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

                //$oDB->insert('tweets',$field_values);

            if (mysqli_num_rows($result)==0) 
            {
                 echo "<font style=\"color:#A52A2A\">";
                 echo "Inserting " . $tweet_id. "<br/>";
                 echo "</font>";
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
                $tweet_text=$item2['text'];
                $created_at = $oDB->date($item2['created_at']);

                //For output
                echo $tweet_text . " " . $created_at;

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
                'location = "' . $oDB->escape($item2['user']['location']) . '", ' . 
                'url = "' . $oDB->escape($item2['user']['url']) . '", ' .
                'description = "' . $oDB->escape($item2['user']['description']) . '", ' .
                'created_at = "' . $oDB->date($item2['user']['created_at']) . '", ' .
                'followers_count = ' . $item2['user']['followers_count'] . ', ' .
                'friends_count = ' . $item2['user']['friends_count'] . ', ' .
                'statuses_count = ' . $item2['user']['statuses_count'] . ', ' . 
                'time_zone = "' . $item2['user']['time_zone'] . '", ' .
                'last_update = "' . $oDB->date($item2['created_at']) . '"' ;

                if ($oDB->in_table('users','user_id="' . $user_id . '"')) 
				{   
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

                foreach ($entities['user_mentions'] as $user_mention) 
				{
					$where = 'tweet_id=' . $tweet_id . ' ' .
					  'AND source_user_id=' . $user_id . ' ' .
					  'AND target_user_id=' . $user_mention['id'];		

					if(! $oDB->in_table('tweet_mentions',$where)) 
					{
					  $field_values = 'tweet_id=' . $tweet_id . ', ' .
					  'source_user_id=' . $user_id . ', ' .
					  'target_user_id=' . $user_mention['id'] .', '.
					  'created_at = "' . $created_at . '"';
					  $oDB->insert('tweet_mentions',$field_values);
					}
				}
                  foreach ($entities['hashtags'] as $hashtag) 
				{
                    $where = 'tweet_id=' . $tweet_id . ' ' .
                      'AND tag="' . $hashtag['text'] . '"';		
                    if(! $oDB->in_table('tweet_tags',$where)) 
					{
                      $field_values = 'tweet_id=' . $tweet_id . ', ' .
                      'tag="' . $hashtag['text'] . '", ' .
                      'created_at = "' . $created_at . '"';
                      $oDB->insert('tweet_tags',$field_values);
                    }
                }
                  foreach ($entities['urls'] as $url) 
				{
                    if (empty($url['expanded_url'])) 
					{
                      $url = $url['url'];
                    } else {
                      $url = $url['expanded_url'];
                    }
                    $where = 'tweet_id=' . $tweet_id . ' ' .
                      'AND url="' . $url . '"';		
                    if(! $oDB->in_table('tweet_urls',$where)) 
					{
                      $field_values = 'tweet_id=' . $tweet_id . ', ' .
                     'url="' . $url . '", ' .
                      'created_at = "' . $created_at . '"';
                      $oDB->insert('tweet_urls',$field_values);
                    }
                }
				  $words=$item;
				$where = 'tweet_id=' . $tweet_id . ' ' .
				'AND words ="' . $words .'"';		
				if(! $oDB->in_table('tweet_words',$where)) 
				{
				$field_values = 'tweet_id=' . $tweet_id . ', ' .
				'words="' . $words . '", ' .
				'created_at = "' . $created_at . '"';
				$oDB->insert('tweet_words',$field_values);
				}
            }  
        }
    }

    if($max_id==$max_id1)    
    {
        --$count;
        echo "Max id is $max_id and Max id1 is $max_id1" . "<br/>";
        break;
    }
    echo "<br><b>No. of api calls made " . ++$noofapicallsmade . "<br/></b>";

    sleep(2);
    }
    
    if($count==0)
    {
        echo "count is $count " . "<br/>";
        echo "<b>Ending time " . date('Y-m-d H:i:s') . "</b></br>";
        $txt = "Ending time searchusers.php " . date('Y-m-d H:i:s') . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);    
        echo "Done";
        die();
    }
  }

echo "<b>Ending time " . date('Y-m-d H:i:s') . "</b></br>";
$txt = "Ending time searchusers.php " . date('Y-m-d H:i:s') . "\n\n";
fwrite($myfile, $txt);
fclose($myfile);    
echo "Done";

//sleep(5);
   

