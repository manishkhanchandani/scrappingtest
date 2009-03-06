<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn = "remote-mysql4.servage.net";
$database_conn = "scrapping1";
$username_conn = "scrapping1";
$password_conn = "xoriant123";
$conn = mysql_connect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_conn, $conn);
?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsHotel = "SELECT a.id as pid, b.province From poi_detail as a Left Join us_xml_tadv as b ON a.xml_id = b.id WHERE a.province = '' or a.province is null";
$rsHotel = mysql_query($query_rsHotel, $conn) or die(mysql_error());
$row_rsHotel = mysql_fetch_assoc($rsHotel);
$totalRows_rsHotel = mysql_num_rows($rsHotel);
do { 
	$sql = "update poi_detail set province = '".$row_rsHotel['province']."' where id = '".$row_rsHotel['pid']."'";
	$string .= $sql.";\n";

} while ($row_rsHotel = mysql_fetch_assoc($rsHotel)); 
echo $totalRows_rsHotel;
$fp = file_put_contents("poi2.txt", $string);
mysql_free_result($rsHotel);
?>