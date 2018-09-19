<?php
/**
* searchusers.php
* Program to retrieve users timeline and insert matching tweets having keyword / keyword combination chonsen by admin
* @author Twitter Activist <tcpdemo2@gmail.com>
* @license GNU Public License
* @version 1.0
*/
include('header.php');
session_start();
if(!isset($_SESSION['user_id']))
{
    die();
}
?>
<!doctype HTML>
<html>
    <head>
        <title>Searchusers</title>
        <script>
            function check()
            {
                if(document.getElementById('userid').value.length>0 && document.getElementById('username').value.length>0)
                {
                    alert('Fill only one');
                    return false;
                }
                if(document.getElementById('userid').value=="" && document.getElementById('username').value=="")
                {
                    alert('Please fill at least one');
                    return false;
                }
            }
        </script>
    </head>
    <body>
        <form method="post" action="searchusers1.php" onsubmit="return check();">
            User id <br/><br/>
            <input type="text" name="userid" id="userid" />
            <br/><br/>Or<br/><br/>
            User Screen Name<br/><br/>
            <input type="text" name="username" id="username" />
            <br/><br/>
            <input type="submit"/>
        </form>
    </body>
</html>
