<?php
/**
* change_status.php
* Part of Admin panel for displaying / adding / removing keyword
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
$id = $_GET['id'];
$is_active = $_GET['status'];
include('config.php');
$statusVal = ($is_active==1) ? 0 : 1 ;
$sql = "Update collection_words SET  is_active = $statusVal WHERE  id = $id ";
$db->query($sql);
session_start();
$_SESSION['success'] = 'Keyword status has been changed successfully.';
header('location:keyword_list.php');
?>