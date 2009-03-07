<?php require_once('../Connections/conn.php'); ?>
<?php require_once('Yahoo.php'); ?>
<?php


if(!$_GET['province']){

	echo "Please enter a province";
	exit;
}
$Yahoo = new Yahoo;
$colname_rsPoi = "%";
if (isset($_GET['province'])) {
  $colname_rsPoi = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}

mysql_select_db($database_conn, $conn);
$query_rsPoi = sprintf("SELECT * FROM us_xml_yahoo WHERE province LIKE '%s' AND gotpoi = 0 AND flag_2nd = 0 LIMIT 1", $colname_rsPoi);
$rsPoi = mysql_query($query_rsPoi, $conn) or die(mysql_error());
$row_rsPoi = mysql_fetch_assoc($rsPoi);
$totalRows_rsPoi = mysql_num_rows($rsPoi);
do { 
	print_r($row_rsPoi);
	$id = $row_rsPoi['id'];
	$province = $row_rsPoi['province'];
	$arrData = unserialize($row_rsPoi['data']);
	if(eregi("/", $arrData['phone'])){
		$arrTempPhone = explode("/",trim($arrData['phone']));
		$phone = $arrTempPhone[0];
	} else {
		$phone = $arrData['phone'];
	}
	
	$st = trim($arrData['streetAddress1']);
	$pattern = substr($phone, -4);
	if(!$pattern) {
		$pattern = $st;
	}
	$firsturl = $Yahoo->logic1($id, $province, $arrData, $pattern);
	
	
	
	echo $id.":";
	if($firsturl) {
		echo '<strong>poi page found</strong><br />';
		//$Yahoo->updateGotPoi($id, $gotPoi=1, $baseurl, $firsturl, $reviewurl);
	} else {
		echo 'poi not found<br />';
		//$Yahoo->updateGotPoi($id, $gotPoi=0, $baseurl, '', '');
	}	  
	  
} while ($row_rsPoi = mysql_fetch_assoc($rsPoi)); 
   
   
   
mysql_free_result($rsPoi);
?>
