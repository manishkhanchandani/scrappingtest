<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_conn = "remote-mysql4.servage.net";
$database_conn = "scrapping1";
$username_conn = "scrapping1";
$password_conn = "xoriant123";
$conn = mysql_connect($hostname_conn, $username_conn, $password_conn) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($database_conn, $conn);
?>
<?php
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
//include('../Connections/conn.php');
// Database configuration

function mail_attachment ($from , $to, $subject, $message, $attachment, $filename){
$fileatt_type = "application/octet-stream"; // File Type 
$fileatt_name = $filename;
$email_from = $from; // Who the email is from 
$email_subject =  $subject; // The Subject of the email 
$email_txt = $message; // Message that the email has in it 
$email_to = $to; // Who the email is to
$headers = "From: ".$email_from;
$semi_rand = md5(time()); 
	$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
    
	$headers .= "\nMIME-Version: 1.0\n" . 
            "Content-Type: multipart/mixed;\n" . 
            " boundary=\"{$mime_boundary}\""; 
$email_message .= "This is a multi-part message in MIME format.\n\n" . 
                "--{$mime_boundary}\n" . 
                "Content-Type:text/html; charset=\"iso-8859-1\"\n" . 
               "Content-Transfer-Encoding: 7bit\n\n" . 
	$email_txt . "\n\n"; 
	$data = $attachment;
	$data = chunk_split(base64_encode($data)); 
	$email_message .= "--{$mime_boundary}\n" . 
                  "Content-Type: {$fileatt_type};\n" . 
                  " name=\"{$fileatt_name}\"\n" . 
                  //"Content-Disposition: attachment;\n" . 
                  //" filename=\"{$fileatt_name}\"\n" . 
                  "Content-Transfer-Encoding: base64\n\n" . 
                 $data . "\n\n" . 
                  "--{$mime_boundary}--\n";
$ok = @mail($email_to, $email_subject, $email_message, $headers); 

	if($ok) { 
		//echo 'sent';
	} else { 
		//die("Sorry but the email could not be sent. Please go back and try again!"); 
	} 
}
function backup($s, $m, $tbl, $send_mail, $email="", $database, $datas, $structure) {
	//$return = "DROP DATABASE `".$database."`;\nCREATE DATABASE `".$database."`;\n\n USE `".$database."`;\n\n ";
	//$return = "";
	$date = date("Y-m-d_h-m-s");
	if($tbl) {
		foreach($tbl as $value) {
			$dir = "backupsql/".$value;				
				
			echo $dir;
			echo "<br><br>";
			
			if(!is_dir($dir)) {
				mkdir($dir, 0777);
				chmod($dir, 0777);
			}
			$key = $value;
			// getting structure
			echo $query = "show create table `".$key."`";
				echo "<br>";
			$rs = mysql_query($query) or die('error'.mysql_error());
			$rec = mysql_fetch_array($rs);
			$tableStructure .= $rec[1].";\n\n";
			
			$fp = fopen($dir."/".$key."_dbstructure.sql","w");
			fwrite($fp, $tableStructure);
			fclose($fp);
			if($structure==1) {
				$complete = $tableStructure."\n\n";
			}
				
			$string = $rec[1].";";
			//$return .= $rec[1].";\n\n";
			//$return .= "\n\n";	
			$rsCnt = mysql_query('select count(*) as cnt from '.$key) or die('error');
			$recCnt = mysql_fetch_array($rsCnt);
			$cnt = $recCnt['cnt'];
			
			$max = 10000;
			if($m) $max = $m;
			if(!$s) $startCounter = 0; else $startCounter = $s;
			
			$totalPages = ceil($cnt/$max)-1;
			echo "Start: ".$startCounter." , max: $max , cnt: $cnt Total pages: $totalPages<hr>";
			for($counter=$startCounter;$counter<=$totalPages;$counter++) {
				echo $counter;
				echo "<br>";
				echo $start = $max*$counter;
				echo "<hr>";
				echo $sql='select * from `'.$key.'` LIMIT '.$start.', '.$max;
				echo "<br>";
				flush();
				$result = mysql_query($sql) or die('error');
				
				$data = '';
				$fulldata = "";
				while($rec = mysql_fetch_array($result)) {
					$query = "insert into ".$key." set ";
					$i = 0;
					$subquery = '';
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$subquery .= "`".$meta->name."`='".addslashes(stripslashes(trim($rec[$meta->name])))."', ";
						$i++;
					}
					$query = $query.substr($subquery,0,-2);
					$data .= $query.";\n";
					$fulldata .= $query.";\n";
				}		
				//$return .= $data;
				//$return .= "\n\n";	
				$fp = fopen($dir."/".$key."_dbdata_".$counter.".sql","w");
				fwrite($fp, $fulldata);
				fclose($fp);
				echo $dir."/".$key."_dbdata_".$counter.".sql is done<br>";
				echo $counter;
				echo "<hr>";
				flush();
				//if($datas==1) {
					//$complete .= $fulldata;
				//}
					
			}
		}
	}
	//$fp = fopen($dir."/".$database."_db.sql","w");
	//fwrite($fp, $complete);
	//fclose($fp);
				
	//if($send_mail==1) {
		//mail_attachment ("system@".str_replace("www.","",$_SERVER['HTTP_HOST']) , $email, "Database Backup", "Attached is database backup", $complete, $date."_db.sql");
	//}	
	echo "Done";	
	return true;
}

$tblg = $_GET['t']; if(!$tblg) { echo 'choose table'; exit; }
$s = $_GET['s']; 
$m = $_GET['m'];
/*
$sql = "SHOW TABLES FROM ".$database_conn;
$result = mysql_query($sql);

while ($row = mysql_fetch_row($result)) {
	$tbls[] = $row[0];
}
*/
$tbls = array($tblg);
$email = "naveenkhanchandani@gmail.com";
$return = backup($s, $m, $tbls, $send_mail=1, $email, $database_conn, 1, 1);
//echo nl2br(htmlentities($return));
?>