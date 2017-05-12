<?php
/**
* keyword_save.php
* Part of Admin panel for displaying / adding / removing keyword
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
include('config.php');
session_start();
$kewword = $_POST['keywords'];
$created_date = date('Y-m-d h:i:s') ;
$chekKQ = $db->query("SELECT COUNT(id) FROM collection_words WHERE words = '$kewword' "); 
$countget = $chekKQ->fetch(PDO::FETCH_NUM);
	if($countget[0] > 0){
		$_SESSION['error'] = 'This keyword already added.';
		header('location:add_keyword.php');
	}else{
	$sql = "insert into collection_words(words,time) VALUES ('$kewword','$created_date' )";
	$check = $db->query($sql);	
	if($check){
	  $_SESSION['success'] = 'Keyword has been added successfully.';
	  header('location:keyword_list.php');
	}
	else{
		$_SESSION['error'] = 'Internal errors.';
		header('location:add_keyword.php');
	}
}
?>