<?php
/**
* keyword_list.php
* Part of Admin panel for displaying / adding / removing keyword
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
include('header.php');
?>
<div class="row">
<!--<link href="css/datatables.min.css" rel="stylesheet">--> 
<!--<link href="https://cdn.datatables.net/v/dt/dt-1.10.13/datatables.min.css" rel="stylesheet">-->
<div class="col-xs-4">
<?php include('left_sidebar.php');
$kewyWordQ = $db->query("SELECT * FROM collection_words "); 
?>
</div>
<div class="span9 col-xs-8">
					<h1>
						Keywords List
					</h1>
					<?php 
				if(isset($_SESSION['success'])){ ?>
				<div class="alert alert-success"><?php echo $_SESSION['success']; ?></div>
				<?php } unset($_SESSION['success']); ?>
					<table id="example" class="table table-bordered table-striped" cellspacing="0" width="92%">
						<thead>
							<tr>
										<th>KeyWord</th>
										<th>Created Date</th>
										<th>Status</th>
										<th>Action</th>

							</tr>
						</thead>
						<tbody>
							<?php 
							while($row = $kewyWordQ->fetch(PDO::FETCH_ASSOC)){?>
                                 <tr>
									<td>
										<?php echo $row['words'];?> 
									</td>
									<td>
										<?php echo $row['time'];?>
									</td>
									<td>
										<?php echo ($row['is_active']==1) ? 'Active' : 'Inactive'; ?>
									</td>
									<td>
										 
										<a href="delete_keyword.php?id=<?php echo $row['id'];?>" onclick="return confirm('are you sure?');">Delete</a> |
										 <a href="change_status.php?id=<?php echo $row['id'];?>&status=<?php echo $row['is_active']; ?>">Change Status</a> 
									</td>

									</tr>
                           <?php }  ?>

						</tbody>
					</table>
				</div>
			</div>
		</div>
		<style>
			div#example_length{display:none; }
		</style>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/site.js"></script>
		<script src="js/datatables.min.js"></script>
		<!--<script>
			$(document).ready(function() {
				$('#example').DataTable();
			});
		</script>-->
		
	</body>
</html>