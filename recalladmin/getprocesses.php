<?php
/**
* getprocesses.php
* used to get all the running processes list. (get_tweet_keyword & parse_tweet_keyword)
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
$output = "";
// replace username with username of your server where your files are hosted
exec("ps -u username xuwww|grep '_keyword.php\|monitor.php'|grep -v grep", $output); 

if(count($output) > 0)
{
	echo count($output);
	foreach($output as $process)
	{
		$out = preg_replace('!\s+!', ' ', $process);		
		$arr = explode(" ", $out);
		echo ','.$arr[1].','.$arr[11];
	}
}
else
{
	echo '0,';
}		
?>