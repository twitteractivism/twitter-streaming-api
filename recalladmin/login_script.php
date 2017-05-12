<?php
/**
* login_script.php
* Part of Admin panel for logging in
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
    require_once('config.php');

	if(isset($_POST['login_btn'])){
		$username = trim($_POST['username']);
		$pass = trim($_POST['password']);
		$password = md5($pass);
		$getUQ = $db->query("SELECT * from admin where username = '$username' and password = '$password' ");
		$Data = $getUQ->fetch(PDO::FETCH_ASSOC);		
		if(!empty($Data)){			
			session_start();
			$_SESSION['user_id'] = $Data['id'];
			header('location:index.php');
			$file = fopen("logindata", "w");
			fprintf($file, "%s", $_SERVER['REMOTE_ADDR']);
			fclose($file);
		}else{
			$_SESSION['login_error'] = 'Invalid username or Password!';
			header('location:login.php');
		}
	}
?>