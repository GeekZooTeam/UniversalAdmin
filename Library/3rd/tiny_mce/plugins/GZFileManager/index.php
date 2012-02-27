<?php
//echo json_encode('确定删除文件 : ');exit;
define('ROOT_PATH', dirname(__FILE__));
define('ROOT', ROOT_PATH.'/data');

define('SITE_BASE', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/data');

if (!file_exists(ROOT)) {
    mkdir(ROOT, 0777);
}

date_default_timezone_set('Asia/Shanghai');

require_once ROOT_PATH.'/library/Helper.php';

$d = @$_GET['d'];
$a = @$_GET['a'];


$in = realpath(ROOT.$d);

$pos = strpos($in, ROOT);
if ($pos === false || $pos != 0) {
    $in = ROOT;
    $d = '';
} else {
    $d = str_replace(ROOT, '', $in);
}

// list
if (empty($a)) {

    $path = new DirectoryIterator($in);
    $folders = array();
    $files = array();
    $ext_array = get_ext_array();
    foreach ($path as $val) {
        if (!$val->isDot()) {
            $name = $val->getFilename();
            if ($val->isDir()) {
                if ($name != '.') {
                    $folders[] = array(
                        'name'  => $name,
                        'ctime' => $val->getCTime()
                        );
                }
            } else {
                $t = explode('.', $name);
                $ext = end($t);
                $icon = isset($ext_array[$ext]) ? $ext_array[$ext] : 'file-generic.png';
                $files[] = array(
                                'name'  => $name,
                                'icon'  => $icon,
                                'size'  => $val->getSize(),
                                'ctime' => $val->getCTime(),
                                'url'   => SITE_BASE.$d.'/'.$name
                            );
            }
        }
    }

// template
$private_key = rand();
setcookie('action_private_key', $private_key, time()+60*60, '/', '', false);
$info = trim(@$_GET['info']);
$count_folders = count($folders);
$count_files = count($files);
print <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{#GZFileManager_dlg.title} - {$d}</title>
    <meta name="generator" content="TextMate http://macromates.com/">
    <meta name="author" content="Geek-Zoo">

    <link rel="stylesheet" href="library/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="library/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="library/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
    <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
    <script type="text/javascript" src="library/script.js"></script>

<style type="text/css" media="screen">

td,th{
    border-right: 1px solid #ccc;
    vertical-align: middle;
    height:30px;
}
tbody td{
    border-bottom: 1px solid #ccc;
}
table {
    border-top: 1px solid #ccc;
    border-left: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
}
img {
    vertical-align:middle;
}
.bag {
    height:45px;
}

.container {
    width:95%;
}

</style>

<script type="text/javascript" charset="utf-8">
tinyMCEPopup.requireLangPack();
private_key = '$private_key';
var d = '$d';
var info = '$info';
</script>
</head>
<body>
<div class="container">
<form action="#" method="post" accept-charset="utf-8" enctype="multipart/form-data">
    <table summary="This is the summary text for this table."  border="0" cellspacing="0" cellpadding="0">
        <caption>
            <img src="library/famfamfam/folder_new.gif" id="new_folder" /> <img src="library/famfamfam/icon_attachment.gif" id="new_file" /> <em>目录结构 : $d</em>
        </caption>
        <thead>
            <tr>
                <th>文件名</th>
                <th width="20%">文件大小</th>
                <th width="20%">最后更新时间</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="3">文件夹 : $count_folders - 文件 : $count_files</td>
            </tr>
        </tfoot>
        <tbody>
EOT;
if (!empty($d)){
print <<<EOT
            <tr>
                <td>
                    <img src="library/icons/up.png"> <a href="?d={$d}/..">..</a>
                </td>
                <td> -</td>
                <td> -</td>
            </tr>
EOT;
}foreach ($folders as $folder) {
print <<<EOT
            <tr>
                <td class="folder_info">
                    <img src="library/icons/folder.png"> <a href="?d={$d}/{$folder['name']}">{$folder['name']}</a>
                </td>
                <td> -</td>
                <td class="realtime">{$folder['ctime']}</td>
            </tr>
EOT;
}foreach ($files as $file) {
print <<<EOT
                <tr>
                <td class="file_info">
                  <img src="library/icons/{$file['icon']}"> <a href="{$file['url']}" target="_blank">{$file['name']}</a> <a href="javascript:insert('{$file['url']}');">插入</a>
                </td>
                <td class="size_format">{$file['size']}</td>
                <td class="realtime">{$file['ctime']}</td>
                </tr>
EOT;
}print <<<EOT
        </tbody>
    </table>
</form>
</div>
</body>
</html>
EOT;
// template end
// new_folder
} elseif ($a == 'new_folder') {
    if (empty($_COOKIE['action_private_key']) || empty($_GET['private_key']) || $_COOKIE['action_private_key'] != $_GET['private_key']) {
        redirect('超时,请重新操作');
    }

    if ($folder_name = trim(strip_tags(@$_POST['folder_name']))) {
        if (!file_exists($in."/$folder_name")) {
            mkdir($in."/$folder_name", 0777);
        }
    }
    redirect();
} elseif ($a == 'new_file') {
    if (empty($_COOKIE['action_private_key']) || empty($_GET['private_key']) || $_COOKIE['action_private_key'] != $_GET['private_key']) {
        redirect('超时,请重新操作');
    }
    if (!isset($_FILES["file_array"])
        || !is_uploaded_file($_FILES["file_array"]["tmp_name"])
        || $_FILES["file_array"]["error"] != 0
        || empty($_FILES["file_array"]['name'])) {
        redirect("错误的上传文件!");
    }
    $info = pathinfo($_FILES["file_array"]['name']);
    
    if ($info['extension']) {
        $info['extension'] = '.'.$info['extension'];
    }
    $file = $in.'/'.$info['filename'].$info['extension'];
    $i = 0;
    while (file_exists($file)) {
        $file = $in.'/'.$info['filename'].'_'.$i++.$info['extension'];
    }
    
    if (!move_uploaded_file($_FILES["file_array"]["tmp_name"], $file)) {
        redirect("上传失败!");
    }
    redirect();
} elseif ($a == 'delete') {
    if (empty($_COOKIE['action_private_key']) || empty($_GET['private_key']) || $_COOKIE['action_private_key'] != $_GET['private_key']) {
        redirect('超时,请重新操作');
    }
    if ($in != ROOT) {
        delete($in);
    }
    redirect();
}


?>