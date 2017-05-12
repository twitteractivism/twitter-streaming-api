<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<!--/**
* tweetlist.php
* For listing ALL tweets for keywords chosen by admin
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/-->
<head>
<?php include("inc/variable.php"); ?>
<title> <?php echo Cause; ?> Tweets by keywords, verifiable by any citizen</title>

<meta name="keywords"content="Verifiable citizen opinion, Verifiable voter ID opinion, cancellation code-sms, voter ID">
<meta name="description"content="<?php echo Cause; ?> - All sent twitters having particular keywords">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	
<!-- Add path to your styling css -->
<link href="stylephp.css" rel="stylesheet" type="text/css" />

<style>
/* for bootstrap */

.login_panel.login_panel_tweetlist form {
    float: right;
}
input[type="submit"] {
    margin-top: -10px;
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
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "Show Less";
	}
} 
</script>

<script type="text/javascript" src="js/gs_sortable.js" async></script>
<script type="text/javascript">
<!--
var TSort_Data = new Array ('customers', 's', 'i', 'f');
tsRegister();
// -->
</script>

</head>

<body>

<div class="header">
<?php include("inc/variable.php"); ?>
<h1><?php echo url; ?></h1>
<h1>Tweets retrieved by keywords, Verifiable by any citizen</h1>
</div>

<div class="login_panel login_panel_tweetlist">
<?php
mb_internal_encoding("UTF-8");
mb_http_output( "UTF-8" );    
ob_start("mb_output_handler");

// $keywords = $_GET['keyword'];

function linkify_tweet($tweet) {

  //Convert urls to <a> links
  $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);

  //Convert hashtags to twitter searches in <a> links
  $tweet = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

  //Convert attags to twitter profiles in <a> links
  $tweet = preg_replace("/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);

  return $tweet;

}


echo '<a href="index.php">Return to homepage</a>';

include("db/db_config.php");
include("db/db_lib.php");

$con=mysqli_connect($db_host,$db_user,$db_password,$db_name);

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  //set_charset when connecting with database
mysqli_set_charset( $con, 'utf8');

// paging coding....

 $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
$android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
$palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
$berry = strpos($_SERVER['HTTP_USER_AGENT'],"BB10");
$ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
$lumia = strpos($_SERVER['HTTP_USER_AGENT'],"Nokia");
$lumia1 = strpos($_SERVER['HTTP_USER_AGENT'],"Lumia");


if ($iphone || $android || $palmpre || $ipod || $berry || $lumia || $lumia1 == true) 
{

$row_per_page = 20;

}
else
{
$row_per_page = 50;
}

// by default we will show 1st page
$pageNum = 1;

$qry = "select count(*) as total from tweets AS s INNER JOIN users AS p ON s.screen_name=p.screen_name";
$res = mysqli_query($con,$qry);
$dd = mysqli_fetch_array($res);
$total_data = $dd['total'];

$max_page = ceil($total_data/$row_per_page);


if(isset($_GET['page']))
{
	$pageNum = $_GET['page'];
}

// getting offset
$offset = ($pageNum - 1) * $row_per_page;

$sql="SELECT s.tweet_id,s.created_at,s.tweet_text,p.location,p.description,p.screen_name  from tweets AS s INNER JOIN users AS p ON s.screen_name=p.screen_name order by s.created_at desc limit $offset, $row_per_page"; 

if($pageNum == "alldata")
{
$sql="SELECT s.tweet_id,s.created_at,s.tweet_text,p.location,p.description,p.screen_name  from tweets AS s INNER JOIN users AS p ON s.screen_name=p.screen_name order by s.created_at desc";

}
$result = mysqli_query($con,$sql);

echo '<h2>List of retrieved Tweets</h2><br>'; 

// echo '</a>';

echo "<div class='back'><a name='top'></a></div>";

echo "<table id='customers' class='sortable tweetlist'>
	<thead>
		<tr>
			<th>Tweet Link &#x25BE;</th>
			<th class='wrap'>Tweet &#x25BE;</th>
			<th class='nowrap'>Sender Name &#x25BE;</th>
			<th class='wrap'>Sender Profile &#x25BE;</th>
			<th>Time &#x25BE;</th>
			<th>Location &#x25BE;</th>
		</tr>
	<thead>";


 $self_page = $_SERVER['PHP_SELF'];
echo "<h3 class='reg-paging' align='center'>";

if($pageNum == "alldata")
{
	echo "<a href='".$self_page."?page=1'>Paging</a>";
	echo "  Single Page";
}
else
{
	
   if ($iphone || $android || $palmpre || $ipod || $berry || $lumia || $lumia1 == true)
{
       if($pageNum != 1)
	{
		$pre_page = $pageNum-1;
		$pre = "<a href='".$self_page."?page=$pre_page#top'>Previous</a>";
		echo $pre;
	}
}
else
{	
	if($pageNum != 1)
	{
		$pre_page = $pageNum-1;
		$pre = "<a href='".$self_page."?page=$pre_page'>Previous</a>";
		echo $pre;
	}
 }       
        /* 
			Now we apply our rules and draw the pagination object. 
			We're actually saving the code to a variable in case we want to draw it more than once.
		*/
	// How many adjacent pages should be shown on each side?
		 $adjacents = 2;
			$targetpage = "tweetlist.php"; 	//your file name  (the name of this file)
	$lpm1 = $max_page - 1;
			
        //pages	
	if ($max_page < 3 + ($adjacents * 2))	//not enough pages to bother breaking it up
	{	
		    for($i=1;$i<=$max_page;$i++)
	            {
		            if($i == $pageNum)	// if it is current page then no hyperlink
		            {
			         echo "<span class='current'> $i of $max_page</span> ";
		            }
		            else
		            {
			       echo "  <a href='".$self_page."?page=$i'>$i</a>";
		            }
	            }
				
	}
	elseif($max_page > 5 + ($adjacents * 2))	//enough pages to hide some
	{
		//close to beginning; only hide later pages
		if($pageNum < 1 + ($adjacents * 2))		
		{
		     for ($i = 1; $i < 2 + ($adjacents * 2); $i++)
		     {
				if($i == $pageNum)	// if it is current page then no hyperlink
		               {
			            echo "<span class='current'>$i of $max_page</span> ";
		               }
		               else
		               {
			           echo "  <a href='".$self_page."?page=$i'>$i</a>";
		               }
	              }
				
			           echo "<span class=\"elipses\">...</span>";
				   echo "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
				   echo "<a href=\"$targetpage?page=$max_page\">$max_page</a>";		
	         }
		 //in middle; hide some front and some back
	         elseif($max_page - ($adjacents * 2) > $pageNum && $pageNum > ($adjacents * 2))
	         {
		  		   echo "<a href=\"$targetpage?page=1\">First</a>";
			echo "<style>	
@media (max-width: 1800px) and (min-width:760px){			   
#customers td, #customers th {
  padding: 12px 2px 11px 7px;
}
}
</style>";	   
            
			for ($i = $pageNum - $adjacents; $i <= $pageNum + $adjacents; $i++)
			{
			       if($i == $pageNum)	// if it is current page then no hyperlink
		               {
			            echo "<span class='current'>$i of $max_page</span> ";
		               }
		               else
		               {
			           echo "  <a href='".$self_page."?page=$i'>$i</a>";
		               }					
			}
				   echo "<span class=\"elipses\">...</span>";
				   echo "<a href=\"$targetpage?page=$lpm1\">$lpm1</a>";
				   echo "<a href=\"$targetpage?page=$max_page\">$max_page</a>";		
	          }
				//close to end; only hide early pages
		  else
	          {
				   echo "<a href=\"$targetpage?page=1\">1</a>";
				   echo "<a href=\"$targetpage?page=2\">2</a>";
				   echo "<span class=\"elipses\">...</span>";
			 for ($i = $max_page - (2 + ($adjacents * 2)); $i <= $max_page; $i++)
			 {
						if($i == $pageNum)	// if it is current page then no hyperlink
		               {
			            echo "<span class='current'>$i of $max_page</span> ";
		               }
		               else
		               {
			           echo "  <a href='".$self_page."?page=$i'>$i</a>";
		               }					
			 }
				
		   }
			
	}	

if ($iphone || $android || $palmpre || $ipod || $berry || $lumia || $lumia1 == true)
{
	
	if($pageNum != $max_page)
	{
		$next_page = $pageNum+1;
		$next = "  <a href='".$self_page."?page=$next_page#top'>Next</a>";
		echo $next;
	}
}
else
{
      if($pageNum != $max_page)
	{
		$next_page = $pageNum+1;
		$next = "  <a href='".$self_page."?page=$next_page'>Next</a>";
		echo $next;
	}
}
	if($pageNum != "alldata")
	{
		$single = "  <a href='".$self_page."?page=alldata'>Single Page</a>";
		echo $single;
	}
        
}
echo "</h3>";

echo "<tbody>";
while($row = mysqli_fetch_array($result))
  {
  $textData = linkify_tweet($row['tweet_text']);
  echo "<tr>";
  echo "<td><a href='http://twitter.com/$row[screen_name]/status/$row[tweet_id]' 
    title='View this tweet on Twitter.com'>" . $row['tweet_id'] . "</a></td>";
    echo "<td class = 'wrap'>" .  $textData . "</td>";
   echo "<td class = 'nowrap'><a href='http://twitter.com/$row[screen_name]' 
    title='View Tweets by user'>" . $row['screen_name'] . "</a></td>";
    echo "<td class = 'wrap'>" . $row['description'] . "</td>";
echo "<td>" . $row['created_at'] . "</td>";
    echo "<td class = 'wrap'>" . $row['location'] . "</td>"; 
    echo "</tr>";
  }
  echo "</tbody>";
echo "</table>";

mysqli_close($con);
  ?>
  
<form action="tweetlist.php" method="get">
    Page: <input type="text" name="page" />
    <input type="submit" value="jump to page" />
</form>
<br><a style="float:right;margin-right:20px;margin-bottom:25px;" href="#top">Back to Top</a>
</div>

</body>
</html>