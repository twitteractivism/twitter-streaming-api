#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define GET_FILE "get_tweets_keyword.php"
#define PARSE_FILE "parse_tweets_keyword.php"
#define MONITOR_FILE "monitor.php"

int isProcessRunning(char *pname)
{
	FILE *bash;
	char path[1024];
	char data[1024];
	
	memset(path, 0, 1024);
	memset(data, 0, 1024);
		//put your username as per your server
	strcpy(path, "ps -u username xuwww|grep ");
	strcat(path, pname);
	strcat(path, "|grep -v grep");
	
	bash = popen(path, "r");
	while (fgets(data, sizeof(data)-1, bash));
	pclose(bash);	
	
	if(strlen(data) > 0)
	{
		return 1;
	}
	else
	{
		return 0;
	}	
}

int main()
{
	char cmd[1024];
	FILE *f1, *f2, *f3;
	//insert absolute path to the folder db and recalladmin in your server
	chdir("/path to the folder/db");
    f1 = fopen("/path to the folder/recalladmin/RUNNING", "r");
    if(f1 != 0)
    {
		if(!isProcessRunning(GET_FILE))
		{
		//insert absolute path to the folder db in your server
			stpcpy(cmd, "nohup php /path to folder/db/");
			strcat(cmd, GET_FILE);
			strcat(cmd, " >/dev/null 2>&1 &");
			system(cmd);
		}
		
		if(!isProcessRunning(PARSE_FILE))
		{
		//insert absolute path to the folder db in your server
			stpcpy(cmd, "nohup php /path to folder/db/");
			strcat(cmd, PARSE_FILE);
			strcat(cmd, " >/dev/null 2>&1 &");
			system(cmd);
		}
		//insert absolute path to the folder recalladmin in your server
	    f2 = fopen("/path to folder/recalladmin/MONITORED", "r");
	    if(f2 != 0)
	    {
			if(!isProcessRunning(MONITOR_FILE))
		    {
			//insert absolute path to the folder recalladmin in your server
				f3 = fopen("/path to folder/recalladmin/MONITOR_TEMP", "w");
				fclose(f3);
				//insert absolute path to the folder recalladmin in your server
			    stpcpy(cmd, "nohup php /path to folder/recalladmin/");
			    strcat(cmd, MONITOR_FILE);
			    strcat(cmd, " >/dev/null 2>&1 &");
			    system(cmd);
				sleep(1);
				//insert absolute path to the folder recalladmin in your server
				unlink("/path to folder/recalladmin/MONITOR_TEMP");
		    }
        }
    }	   
	return 0;
}