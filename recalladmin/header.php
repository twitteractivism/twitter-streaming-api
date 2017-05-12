<!DOCTYPE html>
<!-- /**
* header.php
* Part of Admin panel
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/ -->
<!--[if lt IE 7 ]><html lang="en" class="ie6 ielt7 ielt8 ielt9"><![endif]--><!--[if IE 7 ]><html lang="en" class="ie7 ielt8 ielt9"><![endif]--><!--[if IE 8 ]><html lang="en" class="ie8 ielt9"><![endif]--><!--[if IE 9 ]><html lang="en" class="ie9"> <![endif]--><!--[if (gt IE 9)|!(IE)]><!--> 
<html lang="en"><!--<![endif]--> 
	<head>
		<meta charset="utf-8">
		<title>Recall by Tweet</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
		<link href="css/site.css" rel="stylesheet">
		<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	</head>
	<body onload="onLoad()">
		<?php 
			session_start();
			if(!isset($_SESSION['user_id'])){
				header('location:login.php');
			}
			require_once('config.php');
			$userId = $_SESSION['user_id'];
			$getUQ = $db->query("SELECT * from admin where id = '$userId' ");
			$Data = $getUQ->fetch(PDO::FETCH_ASSOC);
			
		?>
		<div class="container">
			<div class="navbar">
				<div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </a> <a class="brand" href="#">Recall by Tweet</a>
						<div class="nav-collapse">
							<ul class="nav">
								<li class="active">
									<a href="index.php">Dashboard</a>
								</li>
								<li>
									<button id="startBtn" onclick="onStartClick()" disabled style="height:40px;">Loading...</button>
								</li>
								<li>
									<button id="monitorBtn" onclick="onMonitorClick()" style="height:40px;">START MONITORING</button>
								</li>	
								<li>
									<div>&nbsp;&nbsp;&nbsp;&nbsp;Last Start Info:</br><b id="llData"></b></div>
								</li>
							</ul>

							<ul class="nav pull-right">
								<li>
									<a href="profile.htm">Login User: <?php echo $Data['name']; ?></a>
								</li>
								<li>
									<a href="logout.php">Logout</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			
			<script>
			var state = -1;
			var prevState = -1;
			var isMonitoring = false;
			function onLoad(){				
				periodic();				
			}
			function onStartClick(){
				var btn = document.getElementById("startBtn");
				btn.disabled = true;
				var xhttp = new XMLHttpRequest();
				
				switch(state){
					case 0:
						xhttp.open("GET", "../twitter2/db/get_tweets_keyword.php", true);
						xhttp.send();
						
						var xhttp2 = new XMLHttpRequest();
						xhttp2.timeout = 500;
						xhttp2.open("GET", "../twitter2/db/parse_tweets_keyword.php", true);										
						xhttp2.send();
						
						var xhttp3 = new XMLHttpRequest();
						xhttp3.open("GET", "process.php?cmd=start", true);
						xhttp3.send();
						
						break;
						
					case 1:
						xhttp.open("GET", "process.php?cmd=stop", true);
						xhttp.send();	
						break;
						
					default:
						break;
				}			
			}
			function UpdateState() {				
				var btn = document.getElementById("startBtn");
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if ((this.readyState == 4) && (this.status == 200)) {
						var resp = this.responseText.trim();
						var notifyxhttp = new XMLHttpRequest();
						if(resp == "1"){
							state = 1;
							btn.innerHTML = 'STOP';
							notifyxhttp.open("GET", "notify.php?status=1", true);
						}else{
							state = 0;
							btn.innerHTML = 'START';							
							notifyxhttp.open("GET", "notify.php?status=2", true);
						}
						btn.disabled = false;
						if(prevState == -1){
							prevState = state;
						}
						if(prevState != state){
							notifyxhttp.send();
							prevState = state;
						}
					}
				};
				xhttp.open("GET", "process.php?cmd=status", true);
				xhttp.send();
				
				var monxhttp = new XMLHttpRequest();
				monxhttp.open("GET", "monitorhandle.php?cmd=status", true);
				monxhttp.onreadystatechange = function() {
					if ((this.readyState == 4) && (this.status == 200)) {
						var resp = this.responseText.trim();
						var mbtn = document.getElementById("monitorBtn");
						if(resp == '1'){
							isMonitoring = 1;
							mbtn.innerHTML = "STOP MONITORING";
						}else if(resp == '0'){
							isMonitoring = 0;
							mbtn.innerHTML = "START MONITORING";
						}
						mbtn.disabled = false;
					}
				}
				monxhttp.send();
				UpdateProcessTable();
				UpdateLastLoggedInfo();
			}
			function periodic(){
				UpdateState();
				setTimeout("javscript:periodic()", 1000);
			}
			function onMonitorClick(){
				var mbtn = document.getElementById("monitorBtn");
				mbtn.disabled = true;
				
				var xhttp = new XMLHttpRequest();
				
				switch(isMonitoring){
					case 0:
						xhttp.open("GET", "monitor.php", true);
						xhttp.send();
						
						var xhttp1 = new XMLHttpRequest();
						xhttp1.open("GET", "notify.php?status=3", true);
						xhttp1.send();
						
						break;
						
					case 1:
						xhttp.open("GET", "monitorhandle.php?cmd=stop", true);
						xhttp.send();
						
						var xhttp2 = new XMLHttpRequest();
						xhttp2.open("GET", "notify.php?status=4", true);
						xhttp2.send();
						
						break;
						
					default:
						break;
				}				
			}
			function UpdateProcessTable(){
				var table = document.getElementById("processList");
				
				var xhttp = new XMLHttpRequest();
				xhttp.open("GET", "getprocesses.php", true);
				xhttp.onreadystatechange = function() {
					if ((this.readyState == 4) && (this.status == 200)) {
						var resp = this.responseText.trim();					
						var arr = resp.split(",");
						var count = parseInt(arr[0]);
						
						var rowcount = table.rows.length;
						if(rowcount > 1){
							for(i = rowcount - 1; i > 0; i--){
								table.deleteRow(i);
							}
						}
						
						if(count > 0){
							for(i = 0; i < count; i++){
								var row = table.insertRow(i + 1);
								var pid = row.insertCell(0);
								var pname = row.insertCell(1);
								pid.innerHTML = arr[i*2 + 1];
								pname.innerHTML = arr[i*2 + 2];
							}
						}
					}
				}
				xhttp.send();				
			}
			function UpdateLastLoggedInfo(){
				var div = document.getElementById("llData");
				var xhttp = new XMLHttpRequest();
				
				xhttp.open("GET", "monitorhandle.php?cmd=isStarted", true);
				xhttp.onreadystatechange = function() {
					if ((this.readyState == 4) && (this.status == 200)) {
						var resp = this.responseText.trim();					
						if(resp != '1'){
							div.innerHTML = resp;
						}
					}
				}
				xhttp.send();
			}
		</script>