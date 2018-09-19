<?php
/*
* File to register cron job run log to cronlog folder
*/
require_once('db_lib.php');
$myfile = fopen("cronlog/" . date('Y-m') ."_cronjob.txt", "a+") or die("Unable to open file!");
$txt = "Starting time cron_cache_tweets.php " . date('Y-m-d H:i:s') . "\n\n";
echo fwrite($myfile, $txt);
$oDB = new db;
$oDB->delete('cache_tweets');
$oDB->delete('cache_tweetlist');
$txt = "Ending time cron_cache_tweets.php " . date('Y-m-d H:i:s') . "\n\n";
echo fwrite($myfile, $txt);
fclose($myfile);