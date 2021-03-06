<?php
/**
* tweetlist.php
* For listing ALL tweets for keywords or keyword combinations chosen by admin
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
// Alters time zone , sets character type in header and include error logging file
ini_alter('date.timezone','Asia/Calcutta');
header('Content-Type: text/html; charset=utf-8');
include 'log-register.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<!-- Include files and declare variables -->
<?php include("inc/variable.php"); 
$locationuser='';
if(isset($_GET['filter']))
{
    $locationuser=$_GET['filter'];
}
$searchKey='';
$location1='';
$page="";
$keywords="";
$tweet="";
$user="";
$img="";
$id="";
$profile="";
$location="";
$pageLimit="";
$setLimit="";
$query="";
$con="";

$platform="";
?>

<title> Show Tweets by keywords, verifiable by any citizen</title>
<script src="https://use.fontawesome.com/6d44475886.js"></script>
<meta name="keywords"content="">

<meta name="description"content="<?php echo  Cause; ?> - All sent twitters having particular keywords">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

<style>
	.login_panel.login_panel_tweetlist div {
		margin-bottom: 10px; margin-top: 0px;
	}
	a
	{
		color:#336699;
	}
	ul.setPaginate {
		margin: 0px; padding: 0px; overflow: hidden; font: 12px 'Tahoma';
		list-style-type: none;
	}

	ul.setPaginate li.setPage{
		padding:15px 10px; font-size:14px;
		}
		
	ul.setPaginate li.dot{padding: 3px 0;}
	ul.setPaginate li{
		float:left; margin:0px; padding:0px; margin-left:5px;
	}
	ul.setPaginate li a
	{
	color: #999999; display: inline-block;margin: 5px 3px 0 0;
	padding: 0 5px;	text-align: center;	text-decoration: none;
	}	

	ul.setPaginate li a:hover, ul.setPaginate li a.current_page
	{
		background: none repeat scroll 0 0 #0d92e1;
		border: 1px solid #000000; color: #ffffff; text-decoration: none;
	}

	ul.setPaginate li a{
		color:black; display:block;	text-decoration:none; padding:5px 8px;
		text-decoration: none;
	} 
	.pagination_div {
		width: 100%; float: left;
	}
	.navi2 {
		float: left; width: 70%;
	}
	.go_to_page {
		float: left; width: 15%; padding-top:10px;
	}
	.jump_to_input{width:50px;}	

	.right_search {
		float: right;
	}
	.login_panel.login_panel_tweetlist h2 {
		margin-bottom: -15px;
	}

	h3.reg-paging {
		margin: 0px 0px 22px 0px;
	}

	h3.reg-paging a {
	  padding-right: 5px; padding-left: 5px;
	}

	span.current {
	  margin-right: 7px; margin-left: 9px;
	}

	@media (max-width: 320px) and (min-width: 240px) {
		.login_panel.login_panel_tweetlist {
		top: 190px;
	  }
	}
	<!--/* // Tweet GET from API display */ -->
	.tweet-main {
		   width:50%; max-height:220px; float:left; border:1px solid lightgray; margin-top:10px; margin-left:10px; margin-right: 5px; padding-bottom:6px;height:auto;
		}
		.user-profile {
			width:30%; float:left; text-align:center;
		}
		.location {
			width:17%; float:left; text-align:center;
		}
	@media (max-width: 600px) {
		.tweet-headings {
		   display:none;
		}
		.tweet-main {
			   margin-left:0;
			   padding-left:0;
		   width:100%;
			   height:100%;
			   padding-bottom:7%;
		}
		.user-profile {
			width:85%;
		}
		.location {
			width:85%;
		}
	}
</style>
<script>
    //This function is called when form is submitted either jump to page or location is entered
    function check()
    {
        var pageval=document.getElementById('page').value;
        //Adding page value 1 to url if location is submitted
        if(pageval=='')
        {
            document.getElementById('page').value=1;
        }
    }
</script>
</head>

<body>
<div class="header">
<?php include("inc/variable.php"); ?>

<h1>Tweets list by keywords, verifiable by any citizen</h1>
</div>
<div class="login_panel login_panel_tweetlist">
<?php
if(isset($_GET['page']) && $_GET['page']=='')
{
    $page=1;
}

function linkify_tweet($tweet){
	//Convert urls to <a> links
	$tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);
	//Convert hashtags to twitter searches in <a> links
	$tweet = preg_replace('~#(\S+)~i', "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);
	//Convert attags to twitter profiles in <a> links
	$tweet = preg_replace("/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);
	return $tweet;
}
?>

<?php 
include("db/db_config.php");

$con = mysqli_connect($db_host,$db_user,$db_password,$db_name);

//Check connection

if (mysqli_connect_errno()){
	echo  "Failed to connect to MySQL: " . mysqli_connect_error();
}

//set_charset when connecting with database
mysqli_set_charset($con, 'utf8mb4');
$con->set_charset("utf8mb4");
//include file for pagination function
include_once("function.php");
//paging coding....
$iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
$palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
$berry = strpos($_SERVER['HTTP_USER_AGENT'],"BB10");
$ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
$lumia = strpos($_SERVER['HTTP_USER_AGENT'],"Nokia");
$lumia1 = strpos($_SERVER['HTTP_USER_AGENT'],"Lumia");

//Adjusting the number of tweets to show
//If platform is mobile

if ($iphone || $android || $palmpre || $ipod || $berry || $lumia || $lumia1 == true) 
{
        $platform="phone";
	$setLimit  = 20;
	
}
//If platform is web
else
{
        $platform="web";
	$setLimit =100;
}      

$total = 0;
$keyWordsArr = explode(" ", urldecode($keywords));
$locationArr=array();
if(isset($_GET['filter']) && $_GET['filter']!=='')
{
    $locationArr=explode(" ", urldecode(trim($_GET['filter'])));
    $setLimit  = 20;
}
$query = "";

if(isset($_GET["page"]) && $_GET["page"]!==''){
	$page = (int)$_GET["page"];
}else{
	$page = 1;
}
$pageLimit = ($page * $setLimit) - $setLimit;
//If more than one keyword if is called other wise else
if(count($keyWordsArr) > 1){
	$query = "select tw.*,us.description, us.location from tweets tw join users us on tw.user_id=us.user_id WHERE tweet_text LIKE '%".implode("%' AND tweet_text LIKE '%", $keyWordsArr)."%'";
}else{
	$query = "select tw.*,us.description, us.location from tweets tw join users us on tw.user_id=us.user_id WHERE tweet_text LIKE '%$keyWordsArr[0]%'";
}
if($locationuser=='')
{
    $query = $query."order by created_at desc";
}
//if location is not entered then simple query otherwise location and description is got from user table
else
{
    if(count($locationArr) > 1){
     $query = $query. "and((location LIKe '%".implode($locationArr, " ")."%') or (description  LIKE '%".implode($locationArr, " ")."%')) " .   "order by created_at desc";
    }
    else
    {
        $query = $query. "and((location LIKE '%$locationArr[0]%') or (description LIKE '%$locationArr[0]%')) " .   "order by created_at desc";
    }
}
$excelquery = urlencode($query);
$keyData = mysqli_query($con, $query);
$total = mysqli_num_rows($keyData);

$query = $query." limit $pageLimit, $setLimit";
//print_r($locationArr);
//echo $total ."<br/>";

//echo $query;
//die();
$result = mysqli_query($con, $query);
?>

		
	
<div class="header_table">
	
	<div class="right_search">
            <?php 
            if(isset($_GET['filter']) && $_GET['filter']!='')
            {
                $result1=20*$page;
                            $result2=$result1-19;
                echo  "Showing ($result2 to $result1) of total ($total) results";
            }
            else
            if($platform=="web")
            {
                $result1=100*$page;
                            $result2=$result1-99;
                echo  "Showing ($result2 to $result1) of total ($total) results";
            }
            else
            if($platform="phone")
            {
                $result1=20*$page;
                            $result2=$result1-19;
                echo  "Showing ($result2 to $result1) of total ($total) results";
            }
            ?>
	</div>
	<div class="pagination_div">
	<div class="navi2">
		<?php
                        echo "<a href=\"tweetnewlist.php\">Latest Tweets</a><br/>";
			echo  displayPaginationBelow($setLimit,$page,$keywords,$searchKey,$total,$locationuser);
		?>
	</div>
	<div class="go_to_page">
		<form action="tweetlist.php" method="GET" onsubmit="return check();">
                        Filter<input type="text" id="location" name="filter" placeholder="Search by location or profile" value="<?php echo $locationuser ?>"/>
        		Page: <input type="text" id="page" name="page" class="jump_to_input"/>
			<input type="submit" value="jump to page" />
		</form>
	</div>
</div>
</div>


        <?php
        //Displaying horizontal headings for web
        if($platform=="web"){
            
        ?>
        <div class='tweet-headings' style='width:100%;'>
        
        <div style='background-color: #162252;color:#ffffff;width:50%;height:30px;font-size:14pt;text-align: center;padding-top:3px;float:left;'>
            Tweet
        </div>
        <div style='background-color: #162252;color:#ffffff;width:30%;height:30px;font-size:14pt;text-align: center;padding-top:3px;float:left;'>
            Sender Profile
        </div>
        <div style='background-color: #162252;color:#ffffff;width:17%;height:30px;font-size:14pt;text-align: center;padding-top:3px;padding-right:10px;float:left'>
            Location
        </div>
        </div>
        <?php
        }
        ?>
<?php
                //To connect to twitter
                require_once('TwitterAPIExchange.php');
                $query1='';
                $count1=0;
                //If location is not entered then first the page is looked in s, ohter wise in main table.
                if($locationuser=='')
                {
                    $query1="select * from cache_tweets where page=$page order by created_at desc";
                    $result1=mysqli_query($con,$query1);
                    $count1= mysqli_num_rows($result1);
                    //Fetching all records which meet the criteria
                    $count=0;
                    if($count1==0)
                    {
                        $output=array();
                        $outputuser=array();
                        while($row = mysqli_fetch_array($result))
                        {
                            $output[]=$row['tweet_id'];
                            $outputuser[]=$row['user_id'];
                            ++$count;
                            if($count==$setLimit)
                            {
                                break;
                            }
                        }
                        displaytweets($output,$outputuser);
                    }
                    else
                    {
                        $count=0;
                        while($rowto = mysqli_fetch_array($result1))
                        {
                            displaytweets2($rowto);
                            ++$count;
                            if($count==$setLimit)
                            {
                                break;
                            }
                        }

                    }
                }
                //If location is entered then 20 tweets are shown per page and direct search is made.
                else
                {
                    $count=0;
                    while($rowto = mysqli_fetch_array($result))
                    {
                        displaytweets3($rowto['tweet_id'],$rowto['user_id']);
                        ++$count;
                        if($count==20)
                        {
                            break;
                        }
                    }
                    
                    
                }
                
  ?>
  
<br><a style="float:right;margin-right:20px;margin-bottom:25px;" href="#top">Back to Top</a>

</div>

</body>
</html>
<?php
//This function is called if displaying direct from main table 100 tweets per page
function displaytweets($output,$outputuser)
{
    global $query;
    global $pageLimit;
    global $setLimit;
    global $con;
    global $page;
    global $keywords;
    global $locationuser;
    $profile1="";
    $output1=array();
    $output3=array();
    //Calling twitter api with the tweet id to get result
    $tweet_id=implode(",",$output);
    $user_id=implode(",",$outputuser);
    include("inc/twitterc.php");
    $count=1;
    $url="https://api.twitter.com/1.1/users/lookup.json";
    $requestMethod = "GET";
    $getfield = "?user_id=$user_id";
    $twitter = new TwitterAPIExchange($settings);
    $string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest(),$assoc=true);
    if(!isset($string['errors'])){
    if(!empty($string)){
    foreach($string as $item)
    {
        $output3[]=$item['id'];
        $user_id=$item['id'];
        $name=$item['name'];
        $screen_name=$item['screen_name'];
        $location=$item['location'];
        $url=$item['url'];
        $description=$item['description'];
        $created_at=$item['created_at'];
        $created_at=date('Y-m-d H:i:s', strtotime($created_at));
        $followers_count=$item['followers_count'];
        $friends_count=$item['friends_count'];
        $statuses_count=$item['statuses_count'];
        $profile_image_url=$item['profile_image_url'];
        $query="update users set screen_name=?, name=?, profile_image_url=?,location=?, url=?, description=?,
            last_update=?, followers_count=$followers_count,friends_count=$friends_count,
            statuses_count=$statuses_count where user_id=$user_id";
        $stmt=$con->prepare($query);
        $stmt->bind_param("sssssss",$screen_name,$name,$profile_image_url,$location,$url,$description,$created_at);
        $stmt->execute();
    }
    
    
    $output3=array_diff($outputuser,$output3);
	
    foreach($output3 as $item)
    {
        $query="delete from users where user_id=$item";
        $stmt=$con->prepare($query);
        $stmt->execute();
        $query="delete from tweets where user_id=$item";
        $stmt=$con->prepare($query);
        $stmt->execute();
    }
    }
    }
    $url="https://api.twitter.com/1.1/statuses/lookup.json";
    $requestMethod = "GET";
    $getfield = "?id=$tweet_id";
    $twitter = new TwitterAPIExchange($settings);
    $string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest(),$assoc=true);
    //If error is returned that means tweet id is invalid
	//print_r($string);
    if(!isset($string['errors']))
    {
        foreach($string as $item)
        {
        $output1[]=$item['id'];
        $user= $item['user']['screen_name']; // Screen name
        echo  "<div style=\"clear:both;width:100%;\">"; // Full width div clearing earlier float left or right
        //Showing tweet in half of the page, float left so rest two divs come horizontal
        echo "<div class= 'tweet-main' style=''>";
        echo  "<div style='float:left;'>";
        $img=$item['user']['profile_image_url']; // Displaying user profile image
        echo  "<img src=\"$img\" style=\"border-radius:10px;margin-left:15px;margin-top:15px;\">";
        echo  "</div>";
        //Displaying Twitter blue icon
        echo  "<div style=\"float:right;margin-right:20px;margin-top:5px;\">";
        echo  "<a href=\"https://twitter.com/intent/follow?screen_name=$user\" target=\"_blank\"> <img src=\"inc/images/Twitter_Logo_Blue.png\"></a>";
        echo  "</div>";
        //Displaying user name
        echo  "<div style=\"float:left;margin-left:20px;margin-top:15px;\">";
        $user_name=$item['user']['name'];
        echo   "<b>" . $item['user']['name'] ."</b>";
        echo  "<br/>";
        //Displaying screen name
        echo  "<font style=\"color:#336699;\">" ."@" ."</font>"  . "<a  target=\"_blank\" href=\"http://twitter.com/$user\">$user</a>";
        echo  "</div>";
        echo  "<div style=\"clear:both;margin-left:20px;margin-right:20px;\" >";
        echo  "<br/>";
        $id=$item['id'];
        $tweet = $item['text'];
        $tweet1=$tweet;
        //Convert urls to <a> links
        $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);

        //Convert hashtags to twitter searches in <a> links
        $tweet = preg_replace('~#(\S+)~i', "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

        //Convert attags to twitter profiles in &lt;a&gt; links
        $tweet = preg_replace("/@([A-Za-z0-9_\/\.]*)/", "<a target=\"_blank\" href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);
        //Displaying tweet
        echo  $tweet;
        $time=$item['created_at'];
        $time1=date('Y-m-d H:i:s', strtotime($time));
        $time=date('h:i A - d M Y', strtotime($time));
        echo  "<br/><br/>";
        //Displaying Timestamp
        echo  "<font style=\"color:#b3b3b3\"> "."<a target=\"_blank\" title=\"Link to original tweet\"  href=\"http://www.twitter.com/$user/status/$id\">" . $time .  "</a>" ."</font>";
        echo  "<br/><br/>";
        echo  "<div>";
        //Displaying Twitter intents like reply, retweet and like
        echo  "<a target='_blank' style=\"margin-left:10px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/tweet?in_reply_to=$id'><i class=\"fa fa-reply\" aria-hidden=\"true\"></i></a>";
        echo  "<a target='_blank' style=\"margin-left:20px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/retweet?tweet_id=$id'><i class=\"fa fa-retweet\" aria-hidden=\"true\"></i> {$item['retweet_count']}</a>";
        echo  "<a target='_blank' style=\"margin-left:20px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/like?tweet_id=$id'><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i> {$item['favorite_count']}</a>";
        echo  "</div>";
        echo  "</div>";
        echo  "</div>";
        echo "<div class='user-profile' style=''>";
        //If profile is empty then display - other wise display profile
        if(empty($item['user']['description']))
        {
            echo  "---";
            $profile1="---";
        }
        else
        {
            $profile = $item['user']['description'];
            $profile1=$profile;
            //Convert urls to <a> links
            $profile = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $profile);

            //Convert hashtags to twitter searches in <a> links
            $tweet = preg_replace('~#(\S+)~i', "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

            //Convert attags to twitter profiles in &lt;a&gt; links
            $profile = preg_replace("/@([A-Za-z0-9_\/\.]*)/", "<a target=\"_blank\" href=\"http://www.twitter.com/$1\">@$1</a>", $profile);

            echo  $profile;
        }
        echo  "</div>";
        echo "<div class='location' style=''>";
        //Display user location
        $location=$item['user']['location'];
        echo   $item['user']['location'];
        echo  "</div>";
        echo  "</div>";
        $dateetime=date('Y-m-d h:i:s');
        $retweet_count=$item['retweet_count'];
        $like_count=$item['favorite_count'];
        $con->set_charset("utf8mb4");
        //Binding parameters to escape characters
        $stmt=$con->prepare("insert into cache_tweets values(NULL,$page,?,?,?,?,$id,$retweet_count,$like_count,?,?,?,?,'$dateetime')");
        $stmt->bind_param("ssssssss",$keywords,$tweet1,$user,$img,$profile1,$location,$user_name,$time1);
        $stmt->execute();
        //updating tweets table with relevant information 
        $stmt=$con->prepare("update tweets set screen_name=?,name=?,profile_image_url=? where tweet_id=$id");
        $stmt->bind_param("sss",$user,$user_name,$img);
        $stmt->execute();
    }
        //storing the tweets in array which are to be deleted
        $output2=array_diff($output,$output1);
        foreach($output2 as $item)
        {
            $query="select * from tweets where tweet_id=$item";
            $result= mysqli_query($con,$query);
            $row = $result->fetch_assoc();
            $tweet_id=$row['tweet_id'];
            $tweet_text=$row['tweet_text'];
            $created_at=$row['created_at'];
            $geo_lat=$row['geo_lat'];
            $geo_long=$row['geo_long'];
            $user_id=$row['user_id'];
            $screen_name=$row['screen_name'];
            $name=$row['name'];
            $profile_image_url=$row['profile_image_url'];
            $is_rt=$row['is_rt'];
            $con->set_charset("utf8");
            //storing the data of removed tweet in table tweets_removed
            $query="insert into tweets_removed values($tweet_id,'$tweet_text','$created_at',$geo_lat,$geo_long,"
                    . "$user_id,'$screen_name','$name','$profile_image_url',"
                    . "$is_rt,' ', ' ' ,0,0)";
            mysqli_query($con, $query);
            //deleting the data from respective tables which store that tweet id
            $query="delete from tweets where tweet_id=$tweet_id";
            mysqli_query($con, $query);
            $query="select * from tweet_mentions where tweet_id=$tweet_id";
            $result= mysqli_query($con,$query);
            if(mysqli_num_rows($result)>0)
            {
                $row = $result->fetch_assoc();
                $source_uesr_id=$row['source_user_id'];
                $target_user_id=$row['target_user_id'];
                $query="update tweets_removed set source_user_id=$source_uesr_id,target_user_id=$target_user_id"
                        . "where tweet_id=$tweet_id";
                $query="delete from tweet_mentions where tweet_id=$tweet_id";
                mysqli_query($con, $query);
            }
            $query="select * from tweet_tags where tweet_id=$tweet_id";
            $result= mysqli_query($con,$query);
            if(mysqli_num_rows($result)>0)
            {
                $row = $result->fetch_assoc();
                $tag=$row['tag'];
                $query="update tweets_removed set tag='$tag' where tweet_id=$tweet_id";
                $query="delete from tweet_tags where tweet_id=$tweet_id";
                mysqli_query($con, $query);
            }
            $query="select * from tweet_urls where tweet_id=$tweet_id";
            $result= mysqli_query($con,$query);
            if(mysqli_num_rows($result)>0)
            {
                $row = $result->fetch_assoc();
                $tag=$row['url'];
                $query="update tweets_removed set url='$url' where tweet_id=$tweet_id";
                $query="delete from tweet_urls where tweet_id=$tweet_id";
                mysqli_query($con, $query);
            }
            $query="select * from tweet_words where tweet_id=$tweet_id";
            $result= mysqli_query($con,$query);
            if(mysqli_num_rows($result)>0)
            {
                $query="delete from tweet_words where tweet_id=$tweet_id";
                mysqli_query($con, $query);
            }
            $query="select * from tweet_words where tweet_id=$tweet_id";
            $result= mysqli_query($con,$query);
            if(mysqli_num_rows($result)>0)
            {
                $query="delete from tweet_words where tweet_id=$tweet_id";
                mysqli_query($con, $query);
            }
        }
        
    }
	//Twitter api returns error for rate limit
    else
    {
        $myfile = fopen(date('Y-m') ."_log_oembed.txt", "a+") or die("Unable to open file!");
        $txt = "Starting time oembed  " . date('Y-m-d H:i:s') . "\n\n";
        fwrite($myfile, $txt);
        fclose($myfile);
        foreach($string['errors'] as $item)
        {
            if($item['message']=='Rate limit exceeded')
            {
                foreach($output as $item)
                {
                    displaytweets1($item);
                }
            }
        }
    }
}
?>
<!-- Pagination -->
<div class="pagination_div">
	<div class="navi2">
		<?php
			echo  displayPaginationBelow($setLimit,$page,$keywords,$searchKey,$total,$locationuser);
		?>
	</div>
	<div class="go_to_page">
		<form action="tweetlist.php" method="GET" onsubmit="return check();">
			Page: <input type="text" name="page"  class="jump_to_input"/>
			<input type="submit" value="jump to page" />
		</form>
	</div>
<?php
//This function is called for oembed
function displaytweets1($tweet)
{
    include('inc/twitterc.php');
    $username='abc';
    $url = "https://publish.twitter.com/oembed";
    $requestMethod = "GET";
    $getfield = "?url=https://twitter.com/$username/status/$tweet&hide_thread=false&hide_media=true&maxwidth=550";
    $twitter = new TwitterAPIExchange($settings);
    $string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest());
    if(!isset($string->error) && !empty($string))
    {
    echo "<div style='clear:both;width:100%'>";
    echo "<div class= 'tweet-main' style='border:none;height:100%;'>";
    echo $string ->html;
    
    echo "</div>";
    echo "<div class='user-profile' style=''>";
    preg_match_all('~>\K[^<>]*(?=<)~', $string->html, $match);
    $urls = $match[0];
                            // go over all links
    $url='';
    $result='';
    foreach($urls as $url) 
    {
        $url=$url;
        $start  = strpos($url, '(');
        if($start>0)
        {
        $end    = strpos($url, ')', $start + 1);
        $length = $end - $start;
        $result = substr($url, $start + 1, $length - 1);
        }
    }
    $url = "https://api.twitter.com/1.1/users/show.json";
    $requestMethod = "GET";
    $getfield = "?screen_name=$result";
    $twitter = new TwitterAPIExchange($settings);
    $string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest());
    if(!isset($string->error) && !empty($string)){
        if(isset($string->description))
        {
            echo  $string->description;
        }
        else
        {
            echo "-----";
        }
    echo "</div>";
    echo "<div class='location' style=''>";
    if(isset($string->location))
    {
        echo $string->location;
    }
    else
    {
        echo "-----";
    }
    echo "</div>";
    echo "</div>";
    }
    }
}
?>    

<?php    
//This function is called if diaplaying from cache
function displaytweets2($rowto)
{
    //Calling twitter api with the tweet id to get result
    $user= $rowto['screen_name']; // Screen name
    echo "<div style=\"clear:both;width:100%;\">"; // Full width div clearing earlier float left or right
    //Showing tweet in half of the page, float left so rest two divs come horizontal
    echo "<div class= 'tweet-main' style=''>";
    echo "<div style='float:left;'>";
    $img=$rowto['image']; // Displaying user profile image
    echo "<img src=\"$img\" style=\"border-radius:10px;margin-left:15px;margin-top:15px;\">";
    echo "</div>";
    //Displaying Twitter blue icon
    echo "<div style=\"float:right;margin-right:20px;margin-top:5px;\">";
    echo "<a href=\"https://twitter.com/intent/follow?screen_name=$user\" target=\"_blank\"> <img src=\"inc/images/Twitter_Logo_Blue.png\"></a>";
    echo "</div>";
    //Displaying user name
    echo "<div style=\"float:left;margin-left:20px;margin-top:15px;\">";
    echo  "<b>" . $rowto['user_name'] ."</b>";
    echo "<br/>";
    //Displaying screen name
    echo "<font style=\"color:#336699;\">" ."@" ."</font>"  . "<a  target=\"_blank\" href=\"http://twitter.com/$user\">$user</a>";
    echo "</div>";
    echo "<div style=\"clear:both;margin-left:20px;margin-right:20px;\" >";
    echo "<br/>";
    $id=$rowto['tweet_id'];
    $tweet = $rowto['tweet'];

    //Convert urls to <a> links
    $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);

    //Convert hashtags to twitter searches in <a> links
    $tweet = preg_replace('~#(\S+)~i', "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

    //Convert attags to twitter profiles in &lt;a&gt; links
    $tweet = preg_replace("/@([A-Za-z0-9_\/\.]*)/", "<a target=\"_blank\" href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);
    //Displaying tweet
    echo $tweet;
    $time=$rowto['created_at'];
    $time=date('h:i A - d M Y', strtotime($time));
    echo "<br/><br/>";
    //Displaying Timestamp
    echo "<font style=\"color:#b3b3b3\"> "."<a target=\"_blank\" title=\"Link to original tweet\"  href=\"http://www.twitter.com/$user/status/$id\">" . $time .  "</a>" ."</font>";
    echo "<br/><br/>";
    echo "<div>";
    //Displaying Twitter intents like reply, retweet and like
    echo "<a target='_blank' style=\"margin-left:10px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/tweet?in_reply_to=$id'><i class=\"fa fa-reply\" aria-hidden=\"true\"></i></a>";
    echo "<a target='_blank' style=\"margin-left:20px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/retweet?tweet_id=$id'><i class=\"fa fa-retweet\" aria-hidden=\"true\"></i> {$rowto['retweet_count']}</a>";
    echo "<a target='_blank' style=\"margin-left:20px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/like?tweet_id=$id'><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i> {$rowto['like_count']}</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "<br/>";
    echo "</div>";
    echo "<div class='user-profile' style=''>";
    //If profile is empty then display - other wise display profile
    if(empty($rowto['description']))
    {
        echo "---";
    }
    else
    {
        $profile = $rowto['description'];

        //Convert urls to <a> links
        $profile = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $profile);

        //Convert hashtags to twitter searches in <a> links
        $profile = preg_replace('~#(\S+)~i', "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $profile);

        //Convert attags to twitter profiles in &lt;a&gt; links
        $profile = preg_replace("/@([A-Za-z0-9_\/\.]*)/", "<a target=\"_blank\" href=\"http://www.twitter.com/$1\">@$1</a>", $profile);

        echo $profile;
    }
    echo "</div>";
    echo "<div class='location' style=''>";
    //Display user location
    echo  $rowto['location'];
    echo "</div>";
    echo "</div>";

}
?>    
<?php
//This function is called when search with location is made.
function displaytweets3($tweet,$user)
{
    //Calling twitter api with the tweet id to get result
    global $con;
    include("inc/twitterc.php");
    $url="https://api.twitter.com/1.1/users/lookup.json";
    $requestMethod = "GET";
    $getfield = "?user_id=$user";
    $twitter = new TwitterAPIExchange($settings);
    $string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest(),$assoc=true);
    if(!isset($string['errors']))
    {
        foreach($string as $item)
        {
            $output3[]=$item['id'];
            $user_id=$item['id'];
            $name=$item['name'];
            $screen_name=$item['screen_name'];
            $location=$item['location'];
            $url=$item['url'];
            $description=$item['description'];
            $created_at=$item['created_at'];
            $created_at=date('Y-m-d H:i:s', strtotime($created_at));
            $followers_count=$item['followers_count'];
            $friends_count=$item['friends_count'];
            $statuses_count=$item['statuses_count'];
            $profile_image_url=$item['profile_image_url'];
            $query="update users set screen_name=?, name=?, profile_image_url=?,location=?, url=?, description=?,
                last_update=?, followers_count=$followers_count,friends_count=$friends_count,
                statuses_count=$statuses_count where user_id=$user_id";
            $stmt=$con->prepare($query);
            $stmt->bind_param("sssssss",$screen_name,$name,$profile_image_url,$location,$url,$description,$created_at);
            $stmt->execute();
        }
    }
    	//Twitter api returns error for rate limit
    else
    {
        $error='';
        foreach($string['errors'] as $item)
        {
            $error=$item['message'];
            break;
        }
        if (preg_match('/deleted/',$error))
        {
            $query="delete from users where user_id=$user";
            $stmt=$con->prepare($query);
            $stmt->execute();
        }
    }
    $count=0;
    $url = "https://api.twitter.com/1.1/statuses/show.json";
    $requestMethod = "GET";
    $getfield = "?id=$tweet";
    $twitter = new TwitterAPIExchange($settings);
    $string = json_decode($twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest(),$assoc=true);
    
    //If error is returned that means tweet id is invalid
    
    if(!isset($string['errors']))
    {
    $user= $string['user']['screen_name']; // Screen name
    echo "<div style=\"clear:both;width:100%;\">"; // Full width div clearing earlier float left or right
    //Showing tweet in half of the page, float left so rest two divs come horizontal
    echo "<div class= 'tweet-main' style=''>";
    echo "<div style='float:left;'>";
    $img=$string['user']['profile_image_url']; // Displaying user profile image
    echo "<img src=\"$img\" style=\"border-radius:10px;margin-left:15px;margin-top:15px;\">";
    echo "</div>";
    //Displaying Twitter blue icon
    echo "<div style=\"float:right;margin-right:20px;margin-top:5px;\">";
    echo "<a href=\"https://twitter.com/intent/follow?screen_name=$user\" target=\"_blank\"> <img src=\"inc/images/Twitter_Logo_Blue.png\"></a>";
    echo "</div>";
    //Displaying user name
    echo "<div style=\"float:left;margin-left:20px;margin-top:15px;\">";
    echo  "<b>" . $string['user']['name'] ."</b>";
    echo "<br/>";
    //Displaying screen name
    echo "<font style=\"color:#336699;\">" ."@" ."</font>"  . "<a  target=\"_blank\" href=\"http://twitter.com/$user\">$user</a>";
    echo "</div>";
    echo "<div style=\"clear:both;margin-left:20px;margin-right:20px;\" >";
    echo "<br/>";
    $id=$tweet;
    $tweet = $string['text'];

    //Convert urls to <a> links
    $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);

    //Convert hashtags to twitter searches in <a> links
    $tweet = preg_replace('~#(\S+)~i', "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

    //Convert attags to twitter profiles in &lt;a&gt; links
    $tweet = preg_replace("/@([A-Za-z0-9_\/\.]*)/", "<a target=\"_blank\" href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);
    //Displaying tweet
    echo $tweet;
    $time=$string['created_at'];
    $time=date('h:i A - d M Y', strtotime($time));
    echo "<br/><br/>";
    //Displaying Timestamp
    echo "<font style=\"color:#b3b3b3\"> "."<a target=\"_blank\" title=\"Link to original tweet\"  href=\"http://www.twitter.com/$user/status/$id\">" . $time .  "</a>" ."</font>";
    echo "<br/><br/>";
    echo "<div>";
    //Displaying Twitter intents like reply, retweet and like
    echo "<a target='_blank' style=\"margin-left:10px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/tweet?in_reply_to=$id'><i class=\"fa fa-reply\" aria-hidden=\"true\"></i></a>";
    echo "<a target='_blank' style=\"margin-left:20px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/retweet?tweet_id=$id'><i class=\"fa fa-retweet\" aria-hidden=\"true\"></i> {$string['retweet_count']}</a>";
    echo "<a target='_blank' style=\"margin-left:20px;color:#909090;text-decoration:none;\" href='https://twitter.com/intent/like?tweet_id=$id'><i class=\"fa fa-heart-o\" aria-hidden=\"true\"></i> {$string['favorite_count']}</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "<br/>";
    echo "</div>";
    echo "<div class='user-profile' style=''>";
    //If profile is empty then display - other wise display profile
    if(empty($string['user']['description']))
    {
        echo "---";
    }
    else
    {
        $profile = $string['user']['description'];

        //Convert urls to <a> links
        $profile = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $profile);

        //Convert hashtags to twitter searches in <a> links
        $profile = preg_replace('~#(\S+)~i', "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $profile);

        //Convert attags to twitter profiles in &lt;a&gt; links
        $profile = preg_replace("/@([A-Za-z0-9_\/\.]*)/", "<a target=\"_blank\" href=\"http://www.twitter.com/$1\">@$1</a>", $profile);

        echo $profile;
    }
    echo "</div>";
    echo "<div class='location' style=''>";
    //Display user location
    echo  $string['user']['location'];
    echo "</div>";
    echo "</div>";
    
    }
	
        	//Twitter api returns error for rate limit
    else
    {
        $error='';
        foreach($string['errors'] as $item)
        {
            $error=$item['message'];
            break;
        }
        if ($error=='Rate limit exceeded'){
        displaytweets1($tweet);
        }
    }
 
}

?>        