<?php require_once('../Connections/conn.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsPoi = 10;
$pageNum_rsPoi = 0;
if (isset($_GET['pageNum_rsPoi'])) {
  $pageNum_rsPoi = $_GET['pageNum_rsPoi'];
}
$startRow_rsPoi = $pageNum_rsPoi * $maxRows_rsPoi;

$colname_rsPoi = "%";
if (isset($_GET['province'])) {
  $colname_rsPoi = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}
mysql_select_db($database_conn, $conn);
$query_rsPoi = sprintf("SELECT * FROM us_xml_yahoo WHERE province LIKE '%s' AND gotpoi = 0 and hotel_id != 0", $colname_rsPoi);
$query_limit_rsPoi = sprintf("%s LIMIT %d, %d", $query_rsPoi, $startRow_rsPoi, $maxRows_rsPoi);
$rsPoi = mysql_query($query_limit_rsPoi, $conn) or die(mysql_error());
$row_rsPoi = mysql_fetch_assoc($rsPoi);

if (isset($_GET['totalRows_rsPoi'])) {
  $totalRows_rsPoi = $_GET['totalRows_rsPoi'];
} else {
  $all_rsPoi = mysql_query($query_rsPoi);
  $totalRows_rsPoi = mysql_num_rows($all_rsPoi);
}
$totalPages_rsPoi = ceil($totalRows_rsPoi/$maxRows_rsPoi)-1;

$queryString_rsPoi = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsPoi") == false && 
        stristr($param, "totalRows_rsPoi") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsPoi = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsPoi = sprintf("&totalRows_rsPoi=%d%s", $totalRows_rsPoi, $queryString_rsPoi);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Poi Not Found</title>
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
<h1>Poi Not Found</h1>
<?php if ($totalRows_rsPoi > 0) { // Show if recordset not empty ?>
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
  <p> Records <?php echo ($startRow_rsPoi + 1) ?> to <?php echo min($startRow_rsPoi + $maxRows_rsPoi, $totalRows_rsPoi) ?> of <?php echo $totalRows_rsPoi ?>
  <table border="0" width="50%" align="center">
    <tr>
      <td width="23%" align="center"><?php if ($pageNum_rsPoi > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsPoi=%d%s", $currentPage, 0, $queryString_rsPoi); ?>">First</a>
          <?php } // Show if not first page ?>      </td>
      <td width="31%" align="center"><?php if ($pageNum_rsPoi > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsPoi=%d%s", $currentPage, max(0, $pageNum_rsPoi - 1), $queryString_rsPoi); ?>">Previous</a>
          <?php } // Show if not first page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_rsPoi < $totalPages_rsPoi) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsPoi=%d%s", $currentPage, min($totalPages_rsPoi, $pageNum_rsPoi + 1), $queryString_rsPoi); ?>">Next</a>
          <?php } // Show if not last page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_rsPoi < $totalPages_rsPoi) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsPoi=%d%s", $currentPage, $totalPages_rsPoi, $queryString_rsPoi); ?>">Last</a>
          <?php } // Show if not last page ?>      </td>
    </tr>
      </table>
  <?php } // Show if recordset not empty ?></p>
<?php if ($totalRows_rsPoi == 0) { // Show if recordset empty ?>
  <p>No Record Found. </p>
  <?php } // Show if recordset empty ?><p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsPoi);
?>
