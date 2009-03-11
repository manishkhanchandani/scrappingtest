<?php require_once('../Connections/conn.php'); ?>
<?php
ini_set('memory_limit', '500M');
ini_set('max_execution_time', '-1');
include('Yahoo.php');
$Yahoo = new Yahoo;
?>
<?php
$colname_rsView = "AK";
if (isset($_GET['province'])) {
  $colname_rsView = (get_magic_quotes_gpc()) ? $_GET['province'] : addslashes($_GET['province']);
}
mysql_select_db($database_conn, $conn);
//$query_rsView = sprintf("SELECT * FROM us_xml_yahoo WHERE province = '%s' AND id = 32646 and baseurlflag=1 and hotel_id!=0;", $colname_rsView);
$query_rsView = sprintf("SELECT * FROM us_xml_yahoo WHERE province = '%s' AND gotpoi = 0 and baseurlflag=1 and hotel_id!=0;", $colname_rsView);
$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
$row_rsView = mysql_fetch_assoc($rsView);
$totalRows_rsView = mysql_num_rows($rsView);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
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
$data = unserialize($row_rsView['data']);
$search=array('\'','-');
$replace=array(' ',' ');
$name = str_replace('\'',' ',$data['name']);
$city = $data['city'];
$state = $data['state'];
$url = $data['url'];
$phone = trim($data['phone']);
$st = trim($data['streetAddress1']);
echo $Yahoo->url = "http://travel.yahoo.com/bin/search/travel;_ylt=?p=".urlencode($name)."+".urlencode($city);
echo "<br>";
$baseurl = $Yahoo->url;
if(!$allUrls = $Yahoo->crawlSearchPage($province, $id)){
	//do this
	echo $Yahoo->url = "http://travel.yahoo.com/bin/search/travel;_ylt=?p=".urlencode($name)."+".urlencode($state);
	echo "<br>";
	if(!$allUrls = $Yahoo->crawlSearchPage($province, $id)){
		//do this
		echo $Yahoo->url = "http://travel.yahoo.com/bin/search/travel;_ylt=?p=".urlencode($name);
		echo "<br>";
		$allUrls = $Yahoo->crawlSearchPage($province, $id);
	}
}

if($allUrls){
	// assume first url is correct
	$urls = $allUrls[0];

	$reviewUrl = $Yahoo->getReviewUrl($urls['url']);

	$pattern = substr($phone, -4);
	echo $pattern."<br>";
	if(!$pattern) {
		$pattern = $st;
		echo $pattern." 2p<br>";
	}
	$firsturl = $Yahoo->crawlFirstPage($province, $id,$urls['url'], $pattern);//phone match
	if($firsturl) {
		echo 'poi page found';
		$Yahoo->updateGotPoi($id, $gotPoi=1, $baseurl, $firsturl, $reviewurl);
	} else {
		$pattern = $st;
		$firsturl = $Yahoo->crawlFirstPage($province, $id,$urls['url'], $pattern);//address match
		if($firsturl) {
			echo 'poi page found';
			$Yahoo->updateGotPoi($id, $gotPoi=1, $baseurl, $firsturl, $reviewurl);
		} else {
			if($url){
				$pattern = $url;
				$firsturl = $Yahoo->crawlFirstPage($province, $id, $urls['url'], $pattern);//url match
				if($firsturl) {
					echo 'poi page found';
					$Yahoo->updateGotPoi($id, $gotPoi=1, $baseurl, $firsturl, $reviewurl);
				} else {
					echo 'poi not found';
					$Yahoo->updateGotPoi($id, $gotPoi=0, $baseurl, '', '');
				}
			}else{
				echo 'poi not found';
					$Yahoo->updateGotPoi($id, $gotPoi=0, $baseurl, '', '');
			}
		}
	}
}else{
	echo 'poi not found';
	$Yahoo->updateGotPoi($id, $gotPoi=0, $baseurl, '', '');
}

echo "<br>";
?>
<?php } while ($row_rsView = mysql_fetch_assoc($rsView)); ?>
</table>
</body>
</html>
<?php
mysql_free_result($rsView);
?>
