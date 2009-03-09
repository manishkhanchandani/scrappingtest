<?php require_once('../Connections/conn.php'); ?>
<?php
/*
ALTER TABLE `us_xml_yahoo` ADD `sn_status` INT( 11 ) NOT NULL DEFAULT '-1';
check firsturl
sn_status: 
            -1         initial
            -2         poi not found
            0      
            10 
*/
$regexp = "<div class=\"basedon\">Read.*<a.*>(.*)Review.*<\/div>";
$st = "KS";
echo $sql = "select * from us_xml_yahoo where hotel_id != 0 and sn_status = -1 and firsturl != '' and province='".$st."' limit 10";
$rs = mysql_query($sql) or die('error in sql');            
while($rec = mysql_fetch_array($rs)) {
	$status = -1;
	$data = unserialize($rec['data']);
	echo "<br>ph ".$phone = substr($data['phone'],-4);
	echo "<br>url ".$url = $data['url'];	
	$content = file_get_contents('files/yah/first/'.$st.'/'.$rec['id'].'.html');
	if(@eregi($phone, $content)) {
		$status = 0;
	} else if(@eregi($url, $content)) {
		$status = 0;
	}
	if($status===0) {
		//     how many reviews
		if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {				
			foreach($matches as $match) {				
				$review = trim($match[1]);					
				echo $review;	
				$status = $review;			
			}			
		}
	}
	echo "Rec: ".$rec['reviewfound'];
	echo "<br>";
	echo $sql = "update us_xml_yahoo set sn_status = '$status' where id = '".$rec['id']."'";
	mysql_query($sql) or die('err: '.mysql_error());
	echo "<br>";
	$str .= "update us_xml_yahoo set sn_status = $status where id = '".$rec['id']."'";
	$str .= "\n";
} 
?>