<?php
/**
* out_wordssave.php
* Part of Admin panel for displaying / adding / removing outwords for a keyword combination
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
include('config.php');

session_start();

$kewword = $_POST['keywords'];
$outwords= $_POST['outwords'];

if(!empty($outwords))
{
	$sql = "update collection_words set out_words ='$outwords' where id =$kewword";

	$check = $db->query($sql);	
}
else
{
	$sql = "update collection_words set out_words =NULL where id =$kewword";

	$check = $db->query($sql);	
    
}

	if($check){

	  $_SESSION['success'] = 'Out Word has been added successfully.';

	  header('location:keyword_list.php');

	}

	else{

		$_SESSION['error'] = 'Internal errors.';

		header('location:add_keyword.php');

	}

?>