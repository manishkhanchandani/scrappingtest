<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');


$colname_rsPoi = "-1";
if (isset($_GET['province'])) {
  $colname_rsPoi = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}
mysql_select_db($database_conn, $conn);
$query_rsPoi = sprintf("SELECT * FROM us_xml_yahoo WHERE province LIKE '%s' AND gotpoi = 0 and hotel_id != 0", $colname_rsPoi);
$rsPoi = mysql_query($query_rsPoi, $conn) or die(mysql_error());
$row_rsPoi = mysql_fetch_assoc($rsPoi);
$totalRows_rsPoi = mysql_num_rows($rsPoi);
?>
<?php if ($totalRows_rsPoi > 0) { 
$excel = "name\tcity\tstate\tphone\turl\tlink to check\ttype\tpostalcode\r\n";
do { 
	$data = unserialize($row_rsPoi['data']);
	$excel .= $data['name']."\t".$data['city']."\t".$data['state']."\t".$data['phone']."\t".$data['url']."\t".$row_rsPoi['baseurl']."\t".$data['type']."\t".$data['postalCode']."\r\n";

} while ($row_rsPoi = mysql_fetch_assoc($rsPoi)); 
$filename = $_GET['province']."_nopoifound_".date('Y_m_d_h_i_s').".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");
print "$excel"; 
exit;
} else {
echo 'no record';
}
?>