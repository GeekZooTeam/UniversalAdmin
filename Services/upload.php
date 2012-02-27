<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */
$upload_dir = dirname(dirname(__FILE__)).'/attachments';

$upload_name = 'Filedata';

if (!isset($_FILES[$upload_name])) {
	HandleError("No upload found in \$_FILES for " . $upload_name);
} else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
	HandleError('File upload faild.');
} else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
	HandleError("Upload failed is_uploaded_file test.");
} else if (!isset($_FILES[$upload_name]['name'])) {
	HandleError("File has no name.");
}

$file = $_FILES[$upload_name]["tmp_name"];

$t = explode('.', $_FILES[$upload_name]['name']);
$ext = end($t);

$folder = $upload_dir.date('/Y/m/d', time());

if (!file_exists($folder) && !@mkdir($folder, 0777, true)) {
	HandleError("Not Writeable Dir.");
}

$target = $folder.'/'.md5_file($file).filesize($file).'.'.$ext;

if (!file_exists($target)) {
    if (!rename($file, $target)) {
    	HandleError("Cant Write File.");
    }
    chmod($target, 0777);
}

echo  substr($target, strlen($upload_dir));
exit;


function HandleError($message) {
	die('0'.$message);
}

?>