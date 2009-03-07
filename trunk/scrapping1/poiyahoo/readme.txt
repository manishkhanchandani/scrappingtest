When developing Web applications, it's quite likely that you will encounter files in different formats -- CSV data, password files, XML-encoded content, and different forms of binary data. Your PHP script will frequently need to interact with these files, reading and writing data to and from them. Given the plethora of formats, then, it's not surprising that PHP comes with a wide variety of both built-in functions and external libraries to connect to, and work with, almost any file format you can name.

This tutorial deals with one such file format, one that application developers probably encounter almost daily: the ZIP format. This format, commonly used to transfer files over email and remote connections, makes it possible to compress multiple files into a single archive, thereby reducing their disk footprint and making it easier to move them around. PHP has the ability to read and create these ZIP files, both via its ZZipLib extension and PEAR's Archive_Zip class.

I'll assume that you have a working Apache and PHP installation and that the PEAR Archive_Zip class has been correctly installed.

Note: You can install the PEAR Archive_Zip package directly from the Web, either by downloading it or by using the instructions provided.
Creating ZIP archives

Let's begin with a simple example: dynamically creating a ZIP archive that contains a few other files. Start with the script in Listing A.
Listing A

<?php
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('test.zip'); // name of zip file

$files = array('mystuff/ad.gif',
               'mystuff/alcon.doc',
               'mystuff/alcon.xls');   // files to store

if ($obj->create($files)) {
    echo 'Created successfully!';
} else {
    echo 'Error in file creation';
}
?>

This script is quite simple, but it's worth looking at it in detail:

   1. Here, the first step is to create an instance of the Archive_Zip class, and initialize it with the path and name of the ZIP archive to be created. In this example, the archive is named test.zip and located in the current directory.
   2. Next, an array is initialized to hold a list of all the files to be compressed, together with their disk locations; these locations may be specified in either absolute or relative terms, but a key consideration is that the script must have read privileges to on those files or disk locations.
   3. Finally, the create() method is used to actually construct the archive by compressing and merging the named files. This method accepts the file list as input, and returns a Boolean value indicating whether or not the archive was successfully created. It's important to note that the script must have write privileges in the directory the file is being created in, or else the create() method will fail; this is a common error and one that trips up most new users.

Now, try running the script above, after modifying the source file list and destination file location to reflect your local system configuration. If all goes well, Archive_Zip should find the files you listed and compress them into a new ZIP archive named test.zip.
Viewing ZIP archive contents

What about looking inside an existing ZIP archive? Archive_Zip lets you do that too, via its listContent() method. Here's an example (Listing B):
Listing B

<?php
include ('Archive/Zip.php');        // imports

$obj = new Archive_Zip('test.zip'); // name of zip file

$files = $obj->listContent();       // array of file information

foreach ($files as $f) {
    foreach ($f as $k => $v) {
        echo "$k: $v\n";
    }
    echo "\n";
}
?>

The output of listContent() is a structured array of arrays, with each array element representing an individual file from the archive. Typically, each element holds information on the name of the corresponding file, its index position, its status, its size (both compressed and uncompressed) and its time of last modification. It's fairly easy to extract this information with a loop and re-format it to make it more presentable, as Listing B does. Here's a sample of the output (Listing C):
Listing C

filename: mystuff/alcon.xls
stored_filename: mystuff/alcon.xls
size: 113664
compressed_size: 35902
mtime: 1141996836
comment:
folder:
index: 0
status: ok
Adding new files to existing ZIP archives

An interesting feature of the Archive_Zip class is its ability to add new files to an existing archive via its add() method. To illustrate, let's go back to test.zip and try adding a new file to it (Listing D):
Listing D

<?php
include ('Archive/Zip.php');        // imports

if (file_exists('test.zip')) {
    $obj = new Archive_Zip('test.zip'); // name of zip file
} else {
    die('File does not exist');
}

$files = array('otherstuff/montecarlo.png');   // additional files to store

if ($obj->add($files)) {
    echo 'Added successfully!';
} else {
    echo 'Error in file addition';
}
?>

As you can see, the procedure to add a new file to an existing archive is very similar to that of creating a new archive: initialize a new Archive_Zip object pointing to the archive in question, create an array representing the list of files to be added, and pass this array to the add() method. Like create(), add()returns a Boolean signal indicating whether or not the addition succeeded. As before, a key issue to keep in mind involves privileges: remember to ensure that the script has appropriate privileges to read the source files and write the new compressed archive back to disk.
Deleting files from existing ZIP archives

Just as you can add files, so too can you delete files. The Archive_Zip class comes with a delete() method that lets you remove files from an existing archive. Listing E illustrates.
Listing E

<?php
include ('Archive/Zip.php');        // imports

if (file_exists('test.zip')) {
    $obj = new Archive_Zip('test.zip'); // name of zip file
} else {
    die('File does not exist');
}

$files = array('mystuff/ad.gif', 'otherstuff/montecarlo.png');   // files to delete

if ($obj->delete(array('by_name' => $files))) {
    echo 'Deleted successfully!';
} else {
    echo 'Error in file deletion';     
}
?>

Here, an array of files to delete is created, and then passed to the delete() method. Note the special "by_name" argument in the call to delete(): this tells Archive_Zip to delete only those files with an exact name match. If the deletion is successful, the delete() method returns true.

In addition to this type of selective assassination, the delete() method also supports large-scale nuking of files matching a specified pattern or regular expression. Both Perl and PHP regular expressions are supported, using either the "by_ereg" or "by_preg" parameters. Listing F is an example, illustrating how this method can be used to delete all *.doc files from an archive using a Perl regular expression.
Listing F

<?php
include ('Archive/Zip.php');        // imports

if (file_exists('test.zip')) {
    $obj = new Archive_Zip('test.zip'); // name of zip file
} else {
    die('File does not exist');
}

if ($obj->delete(array('by_preg' => "/.*doc$/"))) { // all DOC files
    echo 'Deleted successfully!';
} else {
    echo 'Error in file deletion';    
}
?>

As the examples above illustrate, PEAR'sArchive_Zip class is quite versatile and allows you to perform some fairly complex ZIP file interaction with just a few lines of code. Hopefully, the sample scripts above sparked some ideas about how you can use this class in your daily development activities and got you interested in experimenting with it. Have fun!