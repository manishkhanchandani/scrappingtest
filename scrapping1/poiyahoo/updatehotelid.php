<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
?>
<?php
$colname_rsHotel = "%";
if (isset($_GET['province'])) {
  $colname_rsHotel = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}
mysql_select_db($database_conn, $conn);
$query_rsHotel = sprintf("SELECT us_xml_yahoo.id, us_xml_yahoo.hotel_id, us_xml_yahoo.`data` FROM us_xml_yahoo WHERE province LIKE '%s' AND hotel_id = 0", $colname_rsHotel);
$rsHotel = mysql_query($query_rsHotel, $conn) or die(mysql_error());
$row_rsHotel = mysql_fetch_assoc($rsHotel);
$totalRows_rsHotel = mysql_num_rows($rsHotel);
do { 
	$data = unserialize($row_rsHotel['data']);
	print_r($data['id']);
	$sql = "update us_xml_yahoo set hotel_id = '".$data['id']."' where id = '".$row_rsHotel['id']."'";
	$string .= $sql.";\n";

} while ($row_rsHotel = mysql_fetch_assoc($rsHotel)); 
$fp = file_put_contents("update.txt", $string);
mysql_free_result($rsHotel);
?>