<?php
/**
* process.php
* used to start/stop processes
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
if(isset($_GET['cmd']))
{
	$output = "";
	// replace username with username of your server where your files are hosted
    exec("ps -u username xuwww|grep _keyword.php|grep -v grep", $output);

	if($_GET['cmd'] == 'status')
	{
		if(count($output) > 0)
		{
			echo '1';
		}
		else
		{
			echo '0';
		}			
	}
	else if($_GET['cmd'] == 'start')
	{
		fclose(fopen("RUNNING", "w"));		
		echo '1';
	}
	else if($_GET['cmd'] == 'stop')
	{
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
		if(file_exists("RUNNING")){
			unlink("RUNNING");
		}
		echo $status;
	}
}
else
{
	echo '0';
}
?> 