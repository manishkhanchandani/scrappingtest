<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn = "220.227.198.8";
$database_conn = "scrapping";
$username_conn = "user";
$password_conn = "password";
$conn = mysql_connect($hostname_conn, $username_conn, $password_conn) or die(mysql_error().'<meta http-equiv="refresh" content="5" />');
mysql_select_db($database_conn, $conn) or die(mysql_error().'<meta http-equiv="refresh" content="5" />');
?>