<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */
 


class Action
{
    function __call($id, $param = array())
    {
        if (@$_SERVER['HTTP_IF_MODIFIED_SINCE'] && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) - time() < 60)) {
            header("HTTP/1.1 304 Not Modified", true);
            exit;
        }

        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        
        $param = explode('_', $id);
        $id = array_shift($param);
        if (empty($param)) {
            Storage::OutputData($id);
        }
        list($width, $height) = $param;
        $width = intval($width);
        $height = intval($height);
        $width > 1000 && $width = 1000;
        $height > 1000 && $height = 1000;
        
        $im = new Imagick();
        $im->readImageBlob(Storage::readData($id));
        if (empty($height)) {
            //if ($im->getImageWidth() > $width) {
            $im->thumbnailImage($width, null);
            //}
        } else {
            $im->cropThumbnailImage($width, $height);
        }
        header("Content-Type: image/{$im->getImageFormat()}");
        header("Expires: Fri, 12 Nov 2010 10:42:29 GMT");
        echo $im;
        exit;
    }
}

?>