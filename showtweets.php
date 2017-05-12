<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--/**
* showtweets.php
* For displaying details of retrieved tweets for keywords chosen by admin
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/-->
<head>
<?php include("inc/variable.php"); ?>
<title> Show Tweets by keywords, verifiable by any citizen</title>
<meta name="keywords"content="">

<meta name="description"content="<?php echo Cause; ?> - All sent twitters having particular keywords">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href="stylephp.css" rel="stylesheet" type="text/css" />

<style>
ul.setPaginate {
    margin: 0px;
    padding: 0px;
    height: 100%;
    overflow: hidden;
    font: 12px 'Tahoma';
    list-style-type: none;
}

ul.setPaginate li.setPage{
	padding:15px 10px;
	font-size:14px;
	}
	
	ul.setPaginate li.dot{padding: 3px 0;}
	ul.setPaginate li{
	float:left;
	margin:0px;
	padding:0px;
	margin-left:5px;
	}
	ul.setPaginate li a
	{
	/*background: none repeat scroll 0 0 #ffffff;
	border: 1px solid #cccccc;*/
	color: #999999;
	display: inline-block;
	/*font: 15px/25px Arial,Helvetica,sans-serif;*/
	margin: 5px 3px 0 0;
	padding: 0 5px;
	text-align: center;
	text-decoration: none;
	}	

	ul.setPaginate li a:hover,
	ul.setPaginate li a.current_page
	{
	background: none repeat scroll 0 0 #0d92e1;
	border: 1px solid #000000;
	color: #ffffff;
	text-decoration: none;
	}

	ul.setPaginate li a{
	color:black;
	display:block;
	text-decoration:none;
	padding:5px 8px;
	text-decoration: none;
	} 
.pagination_div {
    width: 100%;
    float: left;
}
.navi2 {
    float: left;
    width: 70%;
}
.go_to_page {
    float: left;
    width: 15%;
	padding-top:10px;
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
@media (max-width: 1800px) and (min-width:1100px){
#customers td, #customers th {
  padding: 7px 2px 5px 7px;
}

}
h3.reg-paging a {
  padding-right: 5px;
  padding-left: 5px;
}

span.current {
  margin-right: 7px;
   margin-left: 9px;
}

@media (max-width: 480px) {
    #customers.tweetlist td {
  padding-left: 42%;
}
}
@media (max-width: 320px) and (min-width: 240px) {
    .login_panel.login_panel_tweetlist {
    top: 190px;
}
#customers.tweetlist td {
	font-size: 15px;
	padding-left: 25%;
}
#customers td:before {
padding: 1px;
width: 24%;
font-size : 12px;
}

</style>

<script language="javascript">
function toggle() {
	var ele = document.getElementById("foo");
	var text = document.getElementById("displayText");
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "See More...";
  	}else {
		ele.style.display = "block";
		text.innerHTML = "Show Less";
	}
} 

</script>
<script type="text/javascript" src="js/gs_sortable.js" async></script>
<?php include("inc/variable.php"); ?>
<script type="text/javascript">
<!--
var TSort_Data = new Array ('customers', 's', 'i', 'f');
//tsRegister();
// -->
function copyToClipboard(link) {
  window.prompt("Copy to clipboard: Ctrl+C, Enter", "<?php echo url ?>/" + link);
}
</script>
</head>
<body>
<div class="header">
<?php include("inc/variable.php"); ?>

<h1>Show Tweets by keywords, Verifiable by any citizen</h1>
</div>
<div class="login_panel login_panel_tweetlist">
<?php

mb_internal_encoding("UTF-8");
mb_http_output( "UTF-8" );    
ob_start("mb_output_handler");
if(!isset($_GET['keyword']) and ($_GET['keyword']== '')){
	header('location:tweetlist.php');
}
$keywords = $_GET['keyword'];
$searchKey = @$_GET['search_keywords'];

function linkify_tweet($tweet){
	//Convert urls to <a> links
	$tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);
	//Convert hashtags to twitter searches in <a> links
	$tweet = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);
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
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

//set_charset when connecting with database

mysqli_set_charset( $con, 'utf8');

include_once("function.php");
//paging coding....
$iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
$palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
$berry = strpos($_SERVER['HTTP_USER_AGENT'],"BB10");
$ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
$lumia = strpos($_SERVER['HTTP_USER_AGENT'],"Nokia");
$lumia1 = strpos($_SERVER['HTTP_USER_AGENT'],"Lumia");

if ($iphone || $android || $palmpre || $ipod || $berry || $lumia || $lumia1 == true) 
{
	$setLimit  = 20;
}
else
{
	$setLimit = 50;
}

$total = 0;
$keyWordsArr = explode(" ", urldecode($keywords));

$query = "";
if(count($keyWordsArr) > 1){
	$query = "SELECT tweet_id FROM tweets WHERE tweet_text LIKE '%".implode("%' AND tweet_text LIKE '%", $keyWordsArr)."%'";
}else{
	$query = "SELECT tweet_id FROM tweets WHERE tweet_text LIKE '%$keyWordsArr[0]%'";
}

if(isset($searchKey) and ($searchKey != '')){
	$src = urldecode($searchKey);
	$query = $query." AND tweet_text LIKE '%$src%'";
}

$keyData = mysqli_query($con, $query);
$total = mysqli_num_rows($keyData);

if(isset($_GET["page"])){
	$page = (int)$_GET["page"];
}else{
	$page = 1;
}
$pageLimit = ($page * $setLimit) - $setLimit;

if(count($keyWordsArr) > 1){
	$query = "SELECT * FROM tweets WHERE tweet_text LIKE '%".implode("%' AND tweet_text LIKE '%", $keyWordsArr)."%'";
}else{
	$query = "SELECT * FROM tweets WHERE tweet_text LIKE '%$keyWordsArr[0]%'";
}

if(isset($searchKey) and ($searchKey != '')){
	$src = urldecode($searchKey);
	$query = $query." AND tweet_text LIKE '%$src%'";
}else{
	$searchKey = '';
}

$query = $query."order by created_at desc";

$excelquery = urlencode($query);
$query = $query." limit $pageLimit, $setLimit";
$result = mysqli_query($con, $query);
?>

	<a href="index.php">Return to homepage</a>
	
	<?php if($total<=5000){?>
	<div align="right"><a href = "#" onclick= <?php echo 'window.open("xlsdownloadstatus.php?query='.$excelquery.'&sz=small"); return false;'; ?> style="text-align:left"><img src="inc/images/excel.png" target="_blank">&nbsp;Download as Excel</a></div>
	<div align="right"><a href = "#" onclick= <?php echo 'copyToClipboard("xlsdownloadstatus.php?query='.$excelquery.'&sz=small")'; ?> style="text-align:left"><i>Copy Download To Clipboard</i></a></div>
	<?php }else{ ?>
	<div align="right"><a href = "tweet_download_parts.php?keyword=<?php echo urlencode($keywords);?>"  style="text-align:left"  target="_blank"><img src="inc/images/excel.png" target="_blank">&nbsp;Download as Excel</a></div>
	<div align="right"><a href = "#" onclick='copyToClipboard("tweet_download_parts.php?keyword=<?php echo urlencode($keywords);?>")' style="text-align:left"><i>Copy Download To Clipboard</i></a></div>
	<?php } ?>
	
<div class="header_table">
	
	<div class="right_search">
		<form method="GET" action="showtweets.php">
			<input type="hidden" name="keyword" value="<?php echo @$_GET['keyword'];?>" />
			<input type="text" Placeholder="Enter Phrase for Search" name="search_keywords" value="<?php echo @$_GET['search_keywords'];?>" />
			<input type="Submit" value="Search" />
		</form>
	</div>
	<div class="pagination_div">
	<div class="navi2">
		<?php
			echo displayPaginationBelow($setLimit,$page,$keywords,$searchKey,$total);
		?>
	</div>
	<div class="go_to_page">
		<form action="showtweets.php" method="GET">
			<input type="hidden" name="keyword" value="<?php echo @$_GET['keyword'];?>" />
			<input type="hidden" name="search_keywords" value="<?php echo @$_GET['search_keywords'];?>" />
			Page: <input type="text" name="page" <?php echo @$_GET['page'];?> class="jump_to_input"/>
			<input type="submit" value="jump to page" />
		</form>
	</div>
</div>
</div>

	<table id='customers' class='sortable tweetlist'>
		<tr>

		<th>Tweet Link &#x25BE;</th>

		<th>Tweet &#x25BE;</th>

		<th class='nowrap'>Sender Name &#x25BE;</th>

		<th class='wrap'>Sender Profile &#x25BE;</th>

		<th>Time &#x25BE;</th>

		<th>Location &#x25BE;</th>

		</tr>

<?php
$self_page = $_SERVER['PHP_SELF'];
?>
	<h3 class='reg-paging' align='center'></h3>
<?php
		while($row = mysqli_fetch_array($result))
		{
			$textData = linkify_tweet($row['tweet_text']);
			echo "<tr>";
			echo "<td><a target=\"_blank\" href='http://twitter.com/$row[screen_name]/status/$row[tweet_id]'

			title='View this tweet on Twitter.com'>" . $row['tweet_id'] . "</a></td>";

			echo "<td>" . $textData . "</td>";
			echo "<td class = 'nowrap'><a href='http://twitter.com/$row[screen_name]' 
			title='View Tweets by user'>" . $row['screen_name'] . "</a></td>";
			$description = "";
			$location = "";
			if($result2 = mysqli_query($con, "SELECT screen_name,description,location FROM users WHERE screen_name='".$row['screen_name']."'")){
				if($row2 = mysqli_fetch_array($result2)){
					$description = $row2['description'];
					$location = $row2['location'];
				}
			}
			echo "<td class = 'wrap'>" . $description . "</td>";
			echo "<td>" . $row['created_at'] . "</td>";
			echo "<td>" . $location . "</td>"; 
			echo "</tr>";
		}
		echo "</table>";
		mysqli_close($con);
  ?>
  
<br><a style="float:right;margin-right:20px;margin-bottom:25px;" href="#top">Back to Top</a>

</div>

</body>
</html>