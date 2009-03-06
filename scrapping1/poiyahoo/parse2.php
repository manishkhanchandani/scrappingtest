<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
include('Yahoo.php');
$Yahoo = new Yahoo;
?>
<?php
$colname_rsView = "RI";
if (isset($_GET['province'])) {
  $colname_rsView = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}
mysql_select_db($database_conn, $conn);
$query_rsView = sprintf("SELECT * FROM us_xml_yahoo WHERE province = '%s' AND gotpoi = 1 AND reviewfound = -1 LIMIT 1", $colname_rsView);
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
//print_r($row_rsView);
//print_r ($totalRows_rsView);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Parse Home Page Reviews</title>
</head>

<body>
<?php if($totalRows_rsView==0) { ?>
no record found.
<?php } else { ?>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td>id</td>
    <td>hotel_id</td>
    <td>xmlpath</td>
    <td>data</td>
    <td>flag</td>
    <td>ftype</td>
    <td>province</td>
    <td>baseurl</td>
    <td>baseurlflag</td>
    <td>firsturl</td>
    <td>firsturlflag</td>
    <td>gotpoi</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsView['id']; ?></td>
      <td><?php echo $row_rsView['hotel_id']; ?></td>
      <td><?php echo $row_rsView['xmlpath']; ?></td>
      <td><?php echo $row_rsView['data']; ?></td>
      <td><?php echo $row_rsView['flag']; ?></td>
      <td><?php echo $row_rsView['ftype']; ?></td>
      <td><?php echo $row_rsView['province']; ?></td>
      <td><?php echo $row_rsView['baseurl']; ?></td>
      <td><?php echo $row_rsView['baseurlflag']; ?></td>
      <td><?php echo $row_rsView['firsturl']; ?></td>
      <td><?php echo $row_rsView['firsturlflag']; ?></td>
      <td><?php echo $row_rsView['gotpoi']; ?></td>
    </tr>
<?php

	$province = $row_rsView['province'];
	$id = $row_rsView['id'];
	static $index = 0;
	$index++;
	echo $index;
	echo "<hr>";
	$Yahoo->getTotalReview($province, $id);
	//$Yahoo->changeip($index);


//exit;
?>
<?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
</table>
<?php } ?>
</body>
</html>
<?php
mysql_free_result($rsView);
?>
