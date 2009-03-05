<?php require_once('../Connections/conn.php'); ?>
<?php
$colname_rsPoi = "%";
if (isset($_GET['province'])) {
  $colname_rsPoi = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}
mysql_select_db($database_conn, $conn);
$query_rsPoi = sprintf("SELECT * FROM us_xml_yahoo WHERE province LIKE '%s' AND gotpoi = 1", $colname_rsPoi);
$rsPoi = mysql_query($query_rsPoi, $conn) or die(mysql_error());
$row_rsPoi = mysql_fetch_assoc($rsPoi);
$totalRows_rsPoi = mysql_num_rows($rsPoi);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Poi Found</title>
<style type="text/css">
<!--
body {
	font-family: Verdana;
	font-size: 11px;
}
-->
</style>
</head>

<body>
<h1>Poi Found</h1>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td><strong>id</strong></td>
    <td><strong>hotel_id</strong></td>
    <td><strong>data</strong></td>
    <td><strong>ftype</strong></td>
    <td><strong>baseurl</strong></td>
    <td><strong>firsturl</strong></td>
    <td><strong>gotpoi</strong></td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsPoi['id']; ?></td>
      <td><?php echo $row_rsPoi['hotel_id']; ?></td>
      <td><?php print_r(unserialize($row_rsPoi['data'])); ?></td>
      <td><?php echo $row_rsPoi['ftype']; ?></td>
      <td><a href="<?php echo $row_rsPoi['baseurl']; ?>" target="_blank"><?php echo $row_rsPoi['baseurl']; ?></a></td>
      <td><a href="<?php echo $row_rsPoi['firsturl']; ?>" target="_blank"><?php echo $row_rsPoi['firsturl']; ?></a></td>
      <td><?php echo $row_rsPoi['gotpoi']; ?></td>
    </tr>
    <?php } while ($row_rsPoi = mysql_fetch_assoc($rsPoi)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($rsPoi);
?>
