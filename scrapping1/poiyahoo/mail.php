<?php
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path.$filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use diff. tyoes here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";
    if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
}

$f = $_GET['f'];
$p = $_GET['p'];
$t = $_GET['t'];
if(!$f) {
	echo 'please get f file';
	exit;
}
if(!$p) {
	echo 'please get p path';
	exit;
}
if(!$t) {
	echo 'please get t email';
	exit;
}
$subject = $f." is attached";
$message = $f." is attached on date ".date('r');



// how to use
$my_file = $f;
$my_path = $p;
$my_name = "System";
$my_mail = "admin@10000projects.info";
$my_replyto = "admin@10000projects.info";
$my_subject = $subject;
$my_message = $message;
mail_attachment($my_file, $my_path, $t, $my_mail, $my_name, $my_replyto, $my_subject, $my_message);
?>