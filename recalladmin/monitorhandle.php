<?php
/**
* monitorhandle.php
* used to start/stop monitor.php
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
if(isset($_GET['cmd'])){
	$output = "";
	// replace username with username of your server where your files are hosted
    exec("ps -u username xuwww|grep monitor.php|grep -v grep", $output);
	
	if($_GET['cmd'] == 'status'){
		if(count($output) > 0){
			echo '1';		
		}else{
			echo '0';
		}
	}else if($_GET['cmd'] == 'stop'){
		$status = '1';
		foreach($output as $process)
		{
			$out = preg_replace('!\s+!', ' ', $process);
			$arr = explode(" ", $out);
			$ret = posix_kill($arr[1], 9);
			if($ret == False)
			{
				$status = '0';
			}
		}
		echo $status;
	}else if($_GET['cmd'] == 'isStarted'){
		if(file_exists('start_log.txt')){
			$file = fopen("start_log.txt", "r");
			$logData = fgets($file);
			fclose($file);
			echo $logData;
		}else{
			echo 1;
		}
	}
}
?>