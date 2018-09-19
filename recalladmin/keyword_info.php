<?php
/**
* keyword_info.php
* Part of Admin panel for calling and displaying via Ajax outwords for a keyword combination
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/

require_once 'config.php';
$id=$_GET['keyword'];
$query="select * from collection_words where id=$id";
$stmt=$db->prepare($query);
$stmt->execute();
$output = array();
foreach( $stmt as $row )
{
	$output[] = array( 'out_words' => $row['out_words']);
}
echo json_encode($output);
?>