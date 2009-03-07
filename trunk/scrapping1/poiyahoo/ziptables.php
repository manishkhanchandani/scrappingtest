<?php
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('backupsql/poi_detail.zip'); // name of zip file

//$files = array('backupsql/updateme.txt','backupsql/updateme2.txt');   // files to store
//$path = "images/thumbs";
//$dir = getcwd();
//$dirname = $dir."/".$path;
$dirname = "backupsql/poi_detail";
if ($handle = opendir($dirname)) {
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
		$filetype = filetype($dirname."/".$file);
		if($filetype == "file" && substr($file,-3)=="sql") {
			// anything
			echo $files[] = $dirname."/".$file;
			echo "<br>";
		}
	}
	closedir($handle);
}

if ($obj->create($files)) {
    echo 'Created successfully!';
} else {
    echo 'Error in file creation';
}
?>