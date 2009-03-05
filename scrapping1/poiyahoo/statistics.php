<?php require_once('../Connections/conn.php'); ?>
<?php
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsStats = 5;
$pageNum_rsStats = 0;
if (isset($_GET['pageNum_rsStats'])) {
  $pageNum_rsStats = $_GET['pageNum_rsStats'];
}
$startRow_rsStats = $pageNum_rsStats * $maxRows_rsStats;

$colname_rsStats = "%";
if (isset($_GET['province'])) {
  $colname_rsStats = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}
mysql_select_db($database_conn, $conn);
$query_rsStats = sprintf("SELECT count(a.province) as cnt, a.province, (select count(d.id) FROM us_xml_yahoo as d WHERE d.province = a.province) as totalpoi, (select count(e.id) from us_xml_yahoo as e WHERE e.province = a.province AND e.flag = 1) as totalpoicompleted, (select count(f.id) from us_xml_yahoo as f WHERE f.province = a.province AND f.flag = 0) as totalpoinotcompleted, (select count(g.id) from us_xml_yahoo as g WHERE g.province = a.province AND g.gotpoi = 1) as totalpoifound FROM us_xml_tadv as a WHERE province LIKE '%s%%' GROUP BY a.province ORDER BY cnt", $colname_rsStats);
$query_limit_rsStats = sprintf("%s LIMIT %d, %d", $query_rsStats, $startRow_rsStats, $maxRows_rsStats);
$rsStats = mysql_query($query_limit_rsStats, $conn) or die(mysql_error());
$row_rsStats = mysql_fetch_assoc($rsStats);

if (isset($_GET['totalRows_rsStats'])) {
  $totalRows_rsStats = $_GET['totalRows_rsStats'];
} else {
  $all_rsStats = mysql_query($query_rsStats);
  $totalRows_rsStats = mysql_num_rows($all_rsStats);
}
$totalPages_rsStats = ceil($totalRows_rsStats/$maxRows_rsStats)-1;

$queryString_rsStats = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsStats") == false && 
        stristr($param, "totalRows_rsStats") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsStats = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsStats = sprintf("&totalRows_rsStats=%d%s", $totalRows_rsStats, $queryString_rsStats);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<h1>Statistics </h1>
<?php if ($totalRows_rsStats > 0) { // Show if recordset not empty ?>
  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <td><strong>cnt</strong></td>
      <td><strong>province</strong></td>
      <td><strong>totalpoi</strong></td>
      <td><strong>totalpoicompleted</strong></td>
      <td><strong>totalpoinotcompleted</strong></td>
      <td><strong>totalpoifound</strong></td>
    </tr>
    <?php do { ?>
      <tr>
        <td><?php echo $row_rsStats['cnt']; ?></td>
        <td><?php echo $row_rsStats['province']; ?></td>
        <td><?php echo $row_rsStats['totalpoi']; ?></td>
        <td><?php echo $row_rsStats['totalpoicompleted']; ?></td>
        <td><?php echo $row_rsStats['totalpoinotcompleted']; ?></td>
        <td><a href="poifound.php?province=<?php echo $row_rsStats['province']; ?>"><?php echo $row_rsStats['totalpoifound']; ?></a></td>
      </tr>
      <?php } while ($row_rsStats = mysql_fetch_assoc($rsStats)); ?>
      </table>
  <p> Records <?php echo ($startRow_rsStats + 1) ?> to <?php echo min($startRow_rsStats + $maxRows_rsStats, $totalRows_rsStats) ?> of <?php echo $totalRows_rsStats ?>
  <table border="0" width="50%" align="center">
    <tr>
      <td width="23%" align="center"><?php if ($pageNum_rsStats > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsStats=%d%s", $currentPage, 0, $queryString_rsStats); ?>">First</a>
          <?php } // Show if not first page ?>      </td>
      <td width="31%" align="center"><?php if ($pageNum_rsStats > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsStats=%d%s", $currentPage, max(0, $pageNum_rsStats - 1), $queryString_rsStats); ?>">Previous</a>
          <?php } // Show if not first page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_rsStats < $totalPages_rsStats) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsStats=%d%s", $currentPage, min($totalPages_rsStats, $pageNum_rsStats + 1), $queryString_rsStats); ?>">Next</a>
          <?php } // Show if not last page ?>      </td>
      <td width="23%" align="center"><?php if ($pageNum_rsStats < $totalPages_rsStats) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsStats=%d%s", $currentPage, $totalPages_rsStats, $queryString_rsStats); ?>">Last</a>
          <?php } // Show if not last page ?>      </td>
    </tr>
      </table>
  <?php } // Show if recordset not empty ?></p>
<?php if ($totalRows_rsStats == 0) { // Show if recordset empty ?>
  <p>No Province Found. </p>
  <?php } // Show if recordset empty ?></body>
</html>
<?php
mysql_free_result($rsStats);
?>
