<?php require_once('../Connections/conn.php'); ?>
<?php
mysql_select_db($database_conn, $conn);
$query_rsView = "SELECT count(a.province) as cnt, a.province, b.cnt as cntTotalPoi, d.cnt as cntTotalPoiFound, e.cnt as cnttotalpoinotcompleted, f.cnt as cnttotalpoicompleted, g.cnt as reviewfound FROM us_xml_yahoo as a LEFT JOIN  totalpoi as b ON a.province = b.province LEFT JOIN totalpoicompleted as c ON a.province = c.province LEFT JOIN totalpoifound as d ON a.province = d.province LEFT JOIN totalpoinotcompleted as e ON a.province = e.province LEFT JOIN totalpoicompleted as f ON a.province = f.province LEFT JOIN reviewfound as g ON a.province = g.province GROUP BY a.province order by a.province";
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Statistics</title>
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
<h1>Report of US - POI Match [Yahoo] </h1>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
    <td><strong>POI States</strong></td>
    <td><strong>Total POI count</strong></td>
    <td><strong>Poi with Reviews </strong></td>
    <td><strong>No poi Match </strong></td>
    <td><strong>Poi Without Reviews </strong></td>
    <td><strong>Total POI Found </strong></td>
  </tr>
  <?php do { ?>
    <tr>
      <td valign="top"><?php static $no=0; $no++; echo $no; ?>&nbsp;</td>
      <td valign="top"><?php echo $row_rsView['province']; ?></td>
      <td valign="top"><?php echo $row_rsView['cntTotalPoi']; $totcntPoi += $row_rsView['cntTotalPoi']; ?></td>
      <td valign="top"><a href="poifound_reviews.php?province=<?php echo $row_rsView['province']; ?>" target="_blank"><?php echo $row_rsView['reviewfound']; ?></a>
        <?php if($row_rsView['cntTotalPoi']) { ?>
        [<?php echo number_format(($row_rsView['reviewfound']/$row_rsView['cntTotalPoi'])*100,2); ?> %]
      <?php } ?></td>
      <td valign="top"><a href="nopoifound.php?province=<?php echo $row_rsView['province']; ?>"><?php echo $notfound = $row_rsView['cntTotalPoi']-$row_rsView['cntTotalPoiFound']; ?>
          <?php if($row_rsView['cntTotalPoi']>0) { ?>
[<?php echo number_format(($notfound/$row_rsView['cntTotalPoi'])*100,2); ?> %]
<?php } ?>
      </a>
      <!-- [<a href="nopoifoundexcel.php?province=<?php echo $row_rsView['province']; ?>">Excel</a>] --></td>
      <td valign="top"><a href="poifound_noreviews.php?province=<?php echo $row_rsView['province']; ?>" target="_blank"><?php echo $noreview = $row_rsView['cntTotalPoiFound']-$row_rsView['reviewfound']; ?>
          <?php if($row_rsView['cntTotalPoi']) { ?>
          [<?php echo number_format(($noreview/$row_rsView['cntTotalPoi'])*100,2); ?> %]
          <?php } ?>
      </a></td>
      <td valign="top"><?php echo $row_rsView['cntTotalPoiFound']; ?>
          <?php if($row_rsView['cntTotalPoi']>0) { ?>
[<?php echo number_format(($row_rsView['cntTotalPoiFound']/$row_rsView['cntTotalPoi'])*100,2); ?> %]
<?php } ?>
      <!--<a href="poifound.php?province=<?php echo $row_rsView['province']; ?>" target="_blank"></a> --></td>
    </tr>
    <?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><?php echo $totcntPoi; ?>&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsView);
?>
