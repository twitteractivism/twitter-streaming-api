<?php
/**
* left_sidebar.php
* Part of Admin panel
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
$allData = explode('/',$_SERVER['REQUEST_URI']);
 $end = end($allData);
?>
<?php

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	if(!isset($_SESSION['user_id']))

	{
		die();
	}
	$allData = explode('/',$_SERVER['REQUEST_URI']);
	 $end = end($allData);
?>
<div class="span3">
	<div class="well" style="padding: 8px 0;">
		<ul class="nav nav-list">
			<li class="nav-header">
				Recall by Tweet
			</li>
			<li class="<?php if($end=="index.php"){ echo 'active';  } ?>">
				<a href="index.php"><i class="icon-white icon-home"></i>Dashboard</a>
			</li>
			<li class="<?php if($end=="keyword_list.php"){ echo 'active';  } ?>">
				<a href="keyword_list.php"><i class="icon-list-alt"></i>Keywords</a>
			</li>
			<li class="<?php if($end=="add_keyword.php"){ echo 'active';  } ?>">
				<a href="add_keyword.php"><i class="icon-list-alt"></i>Add New Keyword</a>
			</li>
			<li class="<?php if($end=="add_outwords.php"){ echo 'active';  } ?>">
				<a href="add_outwords.php"><i class="icon-list-alt"></i>Add Out Words</a>
			</li>
			<li class="<?php if($end=="searchusers.php"){ echo 'active';  } ?>">
				<a href="searchusers.php"><i class="icon-list-alt"></i>Search User Timeline</a>
			</li>
			<li class="<?php if($end=="showtweetstest.php"){ echo 'active';  } ?>">
				<a href="showtweetstest.php?keyword=%23tripletalaq"><i class="icon-list-alt"></i>Test oembed</a>
			</li>
			
		</ul>
	</div>
</div>