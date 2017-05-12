<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Downloading...</title>
</head>
<body onload="load()">
<h3 id="txt">Please wait while the download is ready...</h3>
<script>
var file = "<?php 
                if(!isset($_GET['sz'])){
					echo "x";
				} else{
					if($_GET['sz'] == 'small'){
						echo "tweetdownloadexcel";
					}else if($_GET['sz'] == 'big'){
						echo "tweet_download_parts2";
					}
				}
			?>";
function load(){
	if(file == "x"){
		alert("Error");
	}else{
		window.location.href = file + ".php?query=<?php
				$q = urlencode($_GET['query']);
				if($_GET['sz'] == 'big'){
					$q .= '&pid=' . $_GET['pid'];
				}
				echo $q;
				?>";
		setTimeout(download, 100);
	}
}
function download(){
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if ((this.readyState == 4) && (this.status == 200)) {
			var resp = this.responseText.trim();
			if(resp == "1"){
				document.getElementById("txt").innerHTML = "Download Finished";
				setTimeout(finished, 1000);
			}else{
				setTimeout(download, 200);
			}
		}
	}
	xhttp.open("GET", "isxlsdownloadcomplete.php", true);
	xhttp.send();
}
function finished(){	
	window.close();	
}
</script>
</body>
</html>