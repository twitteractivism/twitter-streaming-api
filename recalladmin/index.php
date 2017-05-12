<?php
/**
* index.php
* Part of Admin panel
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
include('header.php');
?>
<div class="row">
	<?php include('left_sidebar.php');
		$getUQ = $db->query("SELECT COUNT(*) FROM tweets "); 
		$twettCout = $getUQ->fetch(PDO::FETCH_NUM);
		$kewWordQ = $db->query("SELECT COUNT(*) FROM collection_words ");
		$KC = $kewWordQ->fetch(PDO::FETCH_NUM);
	?>
	
	<div class="span9">
					<h1>
						Dashboard
					</h1>
					
					<div class="well summary">
						<ul>
							<li>
								<a href="#"><span class="count"><?php echo $twettCout[0];?></span> Total Tweets </a>
							</li>
							<li>
								<a href="#"><span class="count"><?php echo $KC[0];?></span> Total Keywords</a>
							</li>
							
						</ul>
					</div>
					<h2>
						Processes
					</h2>
					<div class="well summary">
					<table style="width:100%" id="processList" border="1">
						<tr>
							<th>PID</th>
							<th>Name</th>
						</tr>
					</table>
					</div>
				</div>
			</div>
		</div>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/site.js"></script>		
	</body>
</html>