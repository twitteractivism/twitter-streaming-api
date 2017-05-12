<?php
if(file_exists("xls")){
	echo "1";
	unlink("xls");
}else{
	echo "0";
}
?>