Twitter Activism Branch of twitter live streaming 140dev project
===

This is adaptation of work of Shri Adam Green for purposes of Twitter Activism where using Twitter streaming Phirehose library and API, new tweets are streamed, retrieved, stored and displayed. Twitter Activism can be used for retrieving tweets and displaying on webpage their counts and details. These tweet counts and details can be analysed and used by activists. If contacts are there in the twitter profiles, activists can contact and cross-verify data. One working demo can be seen at [recallbytweet.com/tweetcount.php](https://www.recallbytweet.comm/tweetcount.php "Citizen Verifiable Twitter System ") and [recallbytweet.com](http://recallbytweet.com "Citizen Verifiable System")

Although we have tried to remove any coding mistakes, but if there is any mistake in code or if other developers make further improvements, please mail the same to tcpdemo2@gmail.com  

<b>Other Contributors -</b>
ParthaSarathiMishra , 
Varun Saini and Team ,
Bittoo Suryavanshi ,
Kmoksha Rishi ,
Asish Adeshara

Please read - 

1. <b>Basic installation instructions</b> - <br>
============================ <br>
	http://140dev.com/free-twitter-api-source-code-library/twitter-database-server/install/

2. <b>Installation Instructions for new version</b> - <br>
=========================================== <br>
	http://140dev.com/twitter-api-programming-blog/streaming-api-keyword-collection-enhancements-part-1/

	http://140dev.com/twitter-api-programming-blog/streaming-api-enhancements-part-2-keyword-collection-database-changes/

	http://140dev.com/twitter-api-programming-blog/streaming-api-enhancements-part-3-collecting-tweets-based-on-table-of-keywords/

	http://140dev.com/twitter-api-programming-blog/streaming-api-enhancements-part-4-parsing-tweets-for-keywords/

	http://140dev.com/twitter-api-programming-blog/streaming-api-enhancements-part-5-purging-old-tweets-and-related-data/


<b>Note</b> -
   
   1. VPS server is needed for running in background get_tweets_keyword.php and parse_tweets_keyword.php tweets 

	2. Open recalladmin/config.php , db/config2.php and fill in the user name, password, and database name for the MySQL database you just created

	3. Insert your own email id and username where files are inserted in files - recalladmin/process.php , recalladmin/getprocesses.php , recalladmin/notify.php , recalladmin/monitor.php and recalladmin/monitorhandle.php

	4. In database - admin table - Insert fields your paramaters for `email id`, admin panel `username` (admin default url is yourwebsite.com/recalladmin) and MD5 hash of your chosen password (using online md5 hash generator example - http://passwordsgenerator.net/md5-hash-generator/)
