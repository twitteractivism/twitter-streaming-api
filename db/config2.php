<?php
//enter relevant parameters and save file
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
