<?php
/**
* delete_keyword.php
* Part of Admin panel for displaying / adding / removing keyword
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
$id = $_GET['id'];
include('config.php');
$querystr =  $db->query("DELETE FROM collection_words WHERE id = $id");
session_start();
$_SESSION['success'] = 'Keyword has been deleted successfully.';
header('location:keyword_list.php');
?>