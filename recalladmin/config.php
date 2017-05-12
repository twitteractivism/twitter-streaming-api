<?php
/**
* change_status.php
* Part of Admin panel
* Fill in these values for your database
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
date_default_timezone_set('Asia/Kolkata');
$db_host= '';
$db_name= '';
$db_user= '';
$db_pw='';
$db = null;
//date_default_timezone_set('America/Chicago');
try
{
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pw);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: " . $e->getMessage());
}

?>
