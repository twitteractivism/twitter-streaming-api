<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--/**
* tweetcount.php
* For displaying counts of tweets for keywords chosen by admin
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/-->
<head>
<?php include("inc/variable.php"); ?>
<title> <?php echo Cause; ?> Tweets by keywords, verifiable by any citizen</title>
<meta name="keywords"content="">
<meta name="description"content="<?php echo Cause; ?> - All sent twitters having particular keywords">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- Add path to your styling css -->
<link href="stylephp.min.css" rel="stylesheet" type="text/css" /> 
 
<style>
	#customers td, #customers th {
    padding: 8px 10px;
	}

	td, #customers th {
		text-align: center;
	}
	
	@media (max-width: 750px) {
	/*
		Label the data
		*/
		#customers.tweetcount td:nth-of-type(1):before { content: "Serial"; }
		#customers.tweetcount td:nth-of-type(2):before { content: "Keyword(s)"; }
		#customers.tweetcount td:nth-of-type(3):before { content: "Tweets"; }
		#customers.tweetcount td:nth-of-type(4):before { content: "Unique Users"; }
		#customers.tweetcount td:nth-of-type(5):before { content: "Present Status"; }
	#customers td {
		padding: 6px;
		padding-left: 50%;
	}

}
</style>
</head>

<body>

<div class="header">
<?php include("inc/variable.php"); ?>

<h1>Tweets Counts retrieved by keywords, Verifiable by any citizen</h1>
</div>
<div class="login_panel_regissue">
<div class="registerissue">
<?php
mb_internal_encoding("UTF-8");
include('db/config2.php');

$con = mysqli_connect($db_host,$db_user,$db_password,$db_name);

//Check connection

if (mysqli_connect_errno()){
	echo  "Failed to connect to MySQL: " . mysqli_connect_error();
}

//set_charset when connecting with database
mysqli_set_charset($con, 'utf8mb4');
$con->set_charset("utf8mb4");

$query = "SELECT * FROM collection_words ORDER BY `collection_words`.`words` ASC";
 $result = mysqli_query($con, $query);

?>
<a class="home-internal-top" href="https://<?php echo url; ?>/index.php">Return to homepage</a>
<br>
<!-- <br>
<div class="skip"><a href="#content">Skip to Main Content</a></div> -->
<div class="regissue">
<h1 class="regissue-heading">Tweets Counts retrieved by keywords with tweet and unique user details </h1>
<div style="/* width:850px; *//*  margin-left:30px; */">
	<table class="table tweetcount" id="customers" width="100%">
                <thead>
                    <tr>
						<th>Serial</th>
                        <th>Keyword(s)</th>
                        <th>Tweets</th>
						<th>Unique Users</th>
						<th>Present Status</th>
                    </tr>
                </thead>
               <tbody>
				<?php	
					$i = 1;
					
					while($row = mysqli_fetch_array($result)){						
						$keyWord = $row['words'];
						$keyWordsArr = explode(" ", $row['words']);
						
						$query = "";

						if(count($keyWordsArr) > 1){
						$value1 = '';
						foreach ($keyWordsArr as $value) {
						$value1 .= " +"."(".$value."*".")";
						}
				$query1 = "select COUNT(*) as 'count', COUNT(DISTINCT tw.screen_name) AS 'cnt' from tweets tw WHERE MATCH (tweet_text) AGAINST ('$value1' IN BOOLEAN MODE)";
				}else{
				$query1 = "select COUNT(*) as 'count', COUNT(DISTINCT tw.screen_name) AS 'cnt' from tweets tw WHERE MATCH (tweet_text) AGAINST ('$keyWordsArr[0]*' IN BOOLEAN MODE)";
				}			
//print_r($query1);				
				$keyData1 = mysqli_query($con, $query1);
				$total1 = mysqli_fetch_assoc($keyData1);
											
					?>					
					<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $row['words'];?></td>
					<td><a href="showtweets.php?keyword=<?php echo urlencode($keyWord); ?>" target="_blank"><?php echo $total1['count']; ?></a></td>
					<td><a href="showtweets.php?keyword=<?php echo urlencode($keyWord); ?>" target="_blank"><?php echo $total1['cnt']; ?></a></td>
					<td>
						<?php echo ($row['is_active']==1) ? 'Active' : 'Inactive'; ?>
					</td>					
					</tr>
					<?php $i++;} ?> 
					
			   </tbody>
			</table>
		</div>
 
	</div>
</div>

</div><?php /***** login_panel_regissue *****/?>






</body>
</html>