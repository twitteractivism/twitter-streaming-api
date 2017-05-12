<!DOCTYPE html>
<!-- /**
* login.php
* Part of Admin panel for logging in
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/ -->
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="css/site.css" rel="stylesheet">
		<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	</head>
	<body>
		<div id="login-page" class="container">
			<h1>Login</h1>
				<?php
				session_start();
				if(isset($_SESSION['login_error'])){ ?>
				<div class="error" style="color:red;"><?php echo $_SESSION['login_error']; ?></div>
				<?php } unset($_SESSION['login_error']); ?>
			<form id="login-form" method="post" class="well" action="login_script.php">
			<input type="text" class="span2" name = 'username' placeholder="Username" required /><br />
			<input type="password" class="span2" name = "password" placeholder="Password" required /><br />
			<!--<label class="checkbox"> <input type="checkbox" /> Remember me </label>-->
			<button type="submit" name="login_btn" class="btn btn-primary" value="login_btn" >Sign in</button>
			<!--<button type="submit" class="btn">Forgot Password</button>-->
		</form>	
		</div>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/site.js"></script>
	</body>
</html>