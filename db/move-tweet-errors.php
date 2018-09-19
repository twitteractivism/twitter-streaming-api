<?php
/*
* File to move logs to db/backuptweetlogs folder and delete log files older than 90 days
*/
require_once('db_lib.php');
	// Change directory
    chdir(''); // Enter absolute path to present directory

	//Get all the contents of the file error_log
    $fileContents = file_get_contents('error_log'); 

    //Output to new file
    $fh = fopen("./backuptweetlogs/".date('Y-m-d') ."_error_log", "a+") or die("Unable to open file!");

	echo  fwrite($fh,$fileContents)."<br>";
    
    fclose($fh);
	
	//empty contents of file error_log
	echo file_put_contents("error_log", "")."<br>";
	
	//Get all contents of file error_log.txt
	$fileContents2 = file_get_contents('error_log.txt');

    //Output to new file in backuptweetlogs folder
    $fh2 = fopen("./backuptweetlogs/".date('Y-m-d') ."_error_log.txt", "a+") or die("Unable to open file!");
	
	echo  fwrite($fh2,$fileContents2)."<br>";

    fclose($fh2);
	
	//empty contents of file error_log.txt
	echo file_put_contents("error_log.txt", "")."<br>";
	
	/**** Delete files older than 90 days in 
	backuptweetlogs folder ****/
	
	$paths = array('./backuptweetlogs/','./cronlog/','../../error_log_folder/');
	$days = 90;  
	
	foreach ($paths as $path) {
		//$path = './backuptweetlogs/';  
		
		// Open the directory  
		if ($handle = opendir($path))  
		{  
			// Loop through the directory  
			while (false !== ($file = readdir($handle)))  
			{  
				// Check the file we're doing is actually a file  
				if (is_file($path.$file))  
				{  
					// Check if the file is older than X days old  
					if (filemtime($path.$file) < ( time() - ( $days * 24 * 60 * 60 ) ) )  
					{  
						// Do the deletion  
						unlink($path.$file);  
					}  
				}  
			}  
		} 
	}
?>