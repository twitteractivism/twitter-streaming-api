<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include("inc/variable.php"); ?>
<title> Download tweets in parts</title>
<meta name="keywords"content="">

<meta name="description"content="<?php echo Cause; ?> - All sent twitters having particular keywords">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<link href="stylephp.css" rel="stylesheet" type="text/css" />
<style>
.login_panel.login_panel_tweetlist a {
    margin-right: 15px;
}
</style>
</head>
<body>
<div class="header">
<?php include("inc/variable.php"); ?>

<?php $downloadLimit = 5000; ?>
<h1>Download Tweets by parts (<?php echo $downloadLimit; ?>)</h1>

</div>
<?php
if(!isset($_GET['keyword']) and ($_GET['keyword']== '')){
	header('location:tweetlist.php');
}

include("db/db_config.php");
include("db/db_lib.php");

$keywords = $_GET['keyword'];
$keyWordsArr = explode(" ", urldecode($keywords));

$query = "";
if(count($keyWordsArr) > 1){
	$query = "SELECT * FROM tweets WHERE tweet_text LIKE '%".implode("%' AND tweet_text LIKE '%", $keyWordsArr)."%'";
}else{
	$query = "SELECT * FROM tweets WHERE tweet_text LIKE '%$keyWordsArr[0]%'";
}

$con = mysqli_connect($db_host,$db_user,$db_password,$db_name);
if (mysqli_connect_errno()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$keyData = mysqli_query($con, $query);
$total = mysqli_num_rows($keyData);
$totalTab = ceil($total/$downloadLimit);

$query = $query." order by created_at desc";

?>
<div class="login_panel login_panel_tweetlist">
	<?php for($i=0;$i<$totalTab;$i++){
		$startLimit = $i*$downloadLimit;
		$query2 = $query." limit $startLimit, $downloadLimit";
		$excelquery = urlencode($query2);
	?>
	<a href="#" onclick= <?php echo 'window.open("xlsdownloadstatus.php?query='.$excelquery.'&sz=big&pid='.($i+1).'") return false;'; ?>>Part<?php echo $i+1;?></a>
	<?php $query2 =''; } ?>
</div>
</body>
</html>