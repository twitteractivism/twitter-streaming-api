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
<meta name="keywords"content="Verifiable citizen opinion, Verifiable voter ID opinion, cancellation code-sms, voter ID">
<meta name="description"content="<?php echo Cause; ?> - All sent twitters having particular keywords">
<meta name="author"content="Rajneesh Dwivedi">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!-- Add path to your styling css -->
<link href="stylephp.css" rel="stylesheet" type="text/css" /> 
 <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css" /> 
<style>
	table.dataTable tbody tr {
    background-color: #f0f8ff;
}
.dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
    margin-bottom: 5px;
}
	@media (max-width: 750px) {
	/*
		Label the data
		*/
		#customers.tweetcount td:nth-of-type(1):before { content: "Keyword(s)"; }
		#customers.tweetcount td:nth-of-type(2):before { content: "Tweets"; }
		#customers.tweetcount td:nth-of-type(3):before { content: "Unique Users"; }
		#customers.tweetcount td:nth-of-type(4):before { content: "Present Status"; }
		
table.dataTable tbody tr {
    background-color: #ffffff;
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

$kewyWordQ = $db->query("SELECT * FROM collection_words "); 

?>
<a href="index.php">Return to homepage</a>
<br>

<div class="regissue">
<h1 class="regissue-heading">Tweets Counts retrieved by keywords with tweet and unique user details </h1>
<div style="/* width:850px; *//*  margin-left:30px; */">
	<table class="table tweetcount" id="customers" width="100%">
                <thead>
                    <tr>
                        <th>Keyword(s)</th>
                        <th>Tweets</th>
						<th>Unique Users</th>
						<th>Present Status</th>
                    </tr>
                </thead>
               <tbody>
				<?php				
					while($row = $kewyWordQ->fetch(PDO::FETCH_ASSOC)){						
						$keyWord = $row['words'];
						$keyWordsArr = explode(" ", $row['words']);
						
						$query = "";
						if(count($keyWordsArr) > 1){
							$query = "SELECT tweet_id,screen_name,tweet_text FROM tweets WHERE tweet_text LIKE '%".implode("%' AND tweet_text LIKE '%", $keyWordsArr)."%'";
						}else{
							$query = "SELECT tweet_id,screen_name,tweet_text FROM tweets WHERE tweet_text LIKE '%$keyWordsArr[0]%'";
						}
						
						$countIds = array();
						$countNames = array();
						$keyData = $db->query($query);
						while($data = $keyData->fetch(PDO::FETCH_ASSOC)){
							$countIds[] = $data['tweet_id'];
							$countNames[] = $data['screen_name'];
						}						
					?>					
					<tr><td><?php echo $row['words'];?></td>
					<td><a href="showtweets.php?keyword=<?php echo urlencode($keyWord); ?>" target="_blank"><?php echo count(array_unique($countIds)); ?></a></td>
					<td><a href="showtweets.php?keyword=<?php echo urlencode($keyWord); ?>" target="_blank"><?php echo count(array_unique($countNames)); ?></a></td>
					<td>
						<?php echo ($row['is_active']==1) ? 'Active' : 'Inactive'; ?>
					</td>
					
					</tr>
					<?php } ?> 
					
			   </tbody>
</div>
 
</div>
</div>

</div><?php /***** login_panel_regissue *****/?>
<script src="js/datatables.min.js"></script>
<script type="text/javascript">
        $(document).ready(function(){
            $('.table').DataTable();
        });
    </script>
</body>
</html>