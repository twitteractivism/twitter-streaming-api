#include <stdio.h>
#include <stdlib.h>

int main()
{
	FILE *f1, *f2;
	f1 = fopen("../recalladmin/RUNNING", "r");
	if(f1 != 0)
	{
		sleep(30);
		system("nohup php get_tweets_keyword.php &");
		system("nohup php parse_tweets_keyword.php &");
		
		f2 = fopen("../recalladmin/MONITORED", "r");
		if(f2 != 0)
		{
			sleep(10);
			system("nohup php \"../recalladmin/monitor.php\" &");
		}
	}
	return 0;
}