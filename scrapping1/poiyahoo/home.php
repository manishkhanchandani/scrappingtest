<?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsView = "SELECT count(province) as cnt, province  FROM us_xml_yahoo Group by province";
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Parsing</title>
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
<h1>Parsing </h1>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="center"><strong>Parsing 1 </strong></td>
    <td align="center"><strong>Parsing2</strong></td>
    <td align="center"><strong>Parsing 3</strong></td>
    <td align="center"><strong>Parsing 4 </strong></td>
  </tr>
  <tr>
    <td><strong>Count</strong></td>
    <td><strong>Province</strong></td>
    <td><strong>Fetching from yahoo search </strong></td>
    <td><strong>Fetching Reviews and review count </strong></td>
    <td><strong>Getting reviews and putting in db </strong></td>
    <td><strong>Clean up </strong></td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsView['cnt']; ?></td>
      <td><?php echo $row_rsView['province']; ?></td>
      <td><a href="parse1.php" target="_blank">Parsing 1 </a> [Done]</td>
      <td background="parse2.php"><a href="parse1.php" target="_blank">Parsing 2 </a> [Done]</td>
      <td background="parse2.php"><strong>Parsing 3 </strong> [Pending]</td>
      <td background="parse2.php"><a href="parse4.php" target="_blank">Clean up</a> [In Progress]</td>
    </tr>
    <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($rsView);
?>
