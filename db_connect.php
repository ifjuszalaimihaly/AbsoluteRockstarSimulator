<?php
$host="host";
$username="username";
$dbpassword="password";
$database="database";
$mysqli=new mysqli($host,$username,$dbpassword,$database);
echo ($mysqli->connect_error);
$mysqli->set_charset("utf8");
?>
