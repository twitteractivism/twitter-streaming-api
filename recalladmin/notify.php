<?php
/**
* notify.php
* Notifies starting and stopping of Twitter Streaming API service (get_tweets_keyword.php + parse_tweets_keyword.php) to specified email id
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
$to = ""; // Enter between quotes your email id
$sub = "Server Notification";
$msg = "";
$headers = "From: "; // Enter server email id

date_default_timezone_set('Asia/Kolkata');
$timestamp = date('d/m/Y h:i:s A');

if(isset($_GET['status'])){
	switch($_GET['status']){
		case '1':
		    $msg = "Admin has STARTED the twitter streaming service at " . $timestamp . " from location " . $_SERVER['REMOTE_ADDR'];
			$file = fopen("start_log.txt", "w");			
			fprintf($file, "%s %s", $timestamp, $_SERVER['REMOTE_ADDR']);
			fclose($file);
			if(!file_exists("admin_log.txt")){
				fclose(fopen("admin_log.txt", "w"));
			}			
			$file = fopen("admin_log.txt", "a+");
			fprintf($file, "[%s][%s] SERVICE STARTED\r\n", $timestamp, $_SERVER['REMOTE_ADDR']);
			fclose($file);
			break;
			
		case '2':
		    $msg = "Admin has STOPPED the twitter streaming service at " . $timestamp;
			if(!file_exists("admin_log.txt")){
				fclose(fopen("admin_log.txt", "w"));
			}			
			$file = fopen("admin_log.txt", "a+");
			fprintf($file, "[%s][%s] SERVICE STOPPED\r\n", $timestamp, $_SERVER['REMOTE_ADDR']);
			fclose($file);
			break;
			
		case '3':
		    $msg = "Admin has STARTED the MONITORING for twitter streaming service at " . $timestamp . " from location " . $_SERVER['REMOTE_ADDR'];			
			if(!file_exists("admin_log.txt")){
				fclose(fopen("admin_log.txt", "w"));
			}			
			$file = fopen("admin_log.txt", "a+");
			fprintf($file, "[%s][%s] MONITORING STARTED\r\n", $timestamp, $_SERVER['REMOTE_ADDR']);
			fclose($file);
			break;
			
		case '4':
		    $msg = "Admin has STOPPED the MONITORING for twitter streaming service at " . $timestamp;
			if(!file_exists("admin_Log.txt")){
				fclose(fopen("admin_Log.txt", "w"));
			}			
			$file = fopen("admin_Log.txt", "a+");
			fprintf($file, "[%s][%s] MONITORING STOPPED\r\n", $timestamp, $_SERVER['REMOTE_ADDR']);
			fclose($file);
			break;
			
		default:
		    $msg = $timestamp;
			break;
	}
	echo mail($to, $sub, $msg, $headers);
	return;
}
echo 0;
?>
