<?php
date_default_timezone_set('Asia/Kolkata');
//Enter values for your created database between single quotes
$db_host= '';
$db_name= '';
$db_user= '';
$db_pw='';
$db = null;

try
{
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pw);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: " . $e->getMessage());
}

?>
