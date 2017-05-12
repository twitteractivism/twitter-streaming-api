<?php
/**
* monitor.php
* Part of Admin panel for monitoring
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/ 
$prevState = false;
$curState = false;
$to = "example@gmail.com"; //Replace with your email id
$sub = "Server Notification";
$msg = "";

date_default_timezone_set('Asia/Kolkata');

while(True){
	$output = "";
	// replace username with username of your server where your files are hosted
    exec("ps -u username uwww|grep _keyword.php|grep -v grep", $output);
	$procArr = [];
	foreach($output as $process)
	{
		$out = preg_replace('!\s+!', ' ', $process);
		$arr = explode(" ", $out);		
		$procArr[] = basename($arr[11]);
	}
	
	if(count($procArr) < 2){
		$timestamp = date('d/m/Y h:i:s A');
		if(($curState == $prevState) && !file_exists("MONITORED") && file_exists("RUNNING")){
			$msg = "Service got INTERRUPTED at " + $timestamp + "\r\n";
			$msg += "Following file(s) got killed:\r\n";
			$file = fopen("admin_log.txt", "a+");			
			fprintf($file,"[%s] INTERRUPTED", $timestamp);
			if(count($procArr) == 1){
				if($procArr[0] == "get_tweets_keyword.php"){
					fprintf($file, " - parse_tweets_keyword.php");
					$msg .= "parse_tweets_keyword.php";
				}else{
					fprintf($file," - get_tweets_keyword.php");
					$msg .= "get_tweets_keyword.php";
				}
			}else{
				fprintf($file," - get_tweets_keyword.php parse_tweets_keyword.php");
				$msg .= "get_tweets_keyword.php\r\nparse_tweets_keyword.php";
			}
			fprintf($file,"\r\n");
			fclose($file);			
			mail($to, $sub, $msg);
			$curState = true;
			fclose(fopen("MONITORED", "w"));
		}
	}else{
		if(file_exists("RUNNING")){
			$curState = false;
			$prevState = false;
			if(file_exists("MONITORED")){
				unlink("MONITORED");
			}
		}
	}	
	sleep(5);	
}
?>