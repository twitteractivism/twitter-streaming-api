<?php
/**
* add_keyword.php
* Part of Admin panel for displaying / adding / removing keyword
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
include('header.php');
?>
<div class="row">
	<?php include('left_sidebar.php'); ?>
	<div class="span9">
					<h1>
						Add New Keyword
					</h1>
					<?php
				if(isset($_SESSION['error'])){ ?>
				<div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
				<?php } unset($_SESSION['error']); ?>
					<form name="form1" id="add_rmuser" class="form-horizontal" action="keyword_save.php" onsubmit="return validateForm()" method="post">
						<fieldset>
							<legend></legend>
							<div class="control-group">
								<label class="control-label" for="input01">Keyword name</label>
								<div class="controls">
									<input type="text" name = "keywords" required class="input-xlarge" id="input01" />
								</div>
							</div>						
							<div class="form-actions">
								<button name="kname" type="submit" class="btn btn-primary">Save</button> <a href="keyword_list.php" class="btn">Cancel</a>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/site.js"></script>
		<script>
			function validateForm() {
			var keyword = document.forms["form1"]["kname"].value;
				if (keyword.length > 40) {
					alert("Keyword must be with 40 characters limit");
					return false;
				}
			}
	</script>
	</body>
</html>
</div>