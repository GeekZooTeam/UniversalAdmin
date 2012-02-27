<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

// http://example.com/Services/images/200/1.png;
// http://example.com/Services/images/_200/1.png;
// http://example.com/Services/images/200_300/1.png;
// http://example.com/Services/images/cropped_200/1.png;
// http://example.com/Services/images/cropped_200_300/1.png;
// http://example.com/Services/images/fixed_200/1.png;
// http://example.com/Services/images/fixed_200_300/1.png;

if (@$_SERVER['HTTP_IF_MODIFIED_SINCE'] && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) - time() < 60)) {
    header("HTTP/1.1 304 Not Modified", true);
    exit;
}
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

define('ROOT_PATH', dirname(dirname(__FILE__)));

require ROOT_PATH.'/Library/__init__.php';

$file = trim(@$_GET['file']);
if (empty($file)) {
    die("empty \$_GET['file']");
}
$tmp = explode('/', $file);

$param = explode('_', array_shift($tmp));

$file = ROOT_PATH.'/'.implode('/', $tmp);

if (!file_exists($file)) {
    die("file is not exists");
}

$im = new Imagick();

try {
    $im->readImage($file);
} catch (Exception $e) {
    print_r($e);
    exit;
}

if ($param[0] == 'cropped' || $param[0] == 'fixed') {
    $width = intval(@$param[1]);
    $height = empty($param[2]) ? $width : intval($param[2]);
} else {
    $width = empty($param[0]) ? null :intval(@$param[0]);
    $height = empty($param[1]) ? null : intval($param[1]);
}

$width > 1000 && $width = 1000;
$height > 1000 && $height = 1000;

if (empty($width)) {
    die("width == 0");
}


if ($param[0] == 'cropped' || $param[0] == 'fixed') {
    $m = $param[0] == 'cropped' ? 'cropThumbnailImage' : 'thumbnailImage';
    if (empty($height)) {
        die("height == 0");
    }
    $im->$m($width, $height);
} elseif ($height === null) {
    $im->thumbnailImage($width, $height);
} else {
    if (empty($height)) {
        die("height == 0");
    }
    $im->thumbnailImage($width, $height, true);
}

header("Content-Type: image/{$im->getImageFormat()}");
header("Expires: Fri, 12 Nov 2010 10:42:29 GMT");
echo $im;
exit;
?>