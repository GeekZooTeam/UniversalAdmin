<?php



class Uploadr
{
    public static function saveFILES($files, $upload_dir = '')
    {
        $file = $files["tmp_name"];

        $t = explode('.', $files['name']);
        $ext = end($t);

        $folder = $upload_dir.date('/Y/m/d', time());

        if (!file_exists($folder) && !@mkdir($folder, 0777, true)) {
            return false;
        }

        $target = $folder.'/'.md5_file($file).filesize($file).'.'.$ext;

        if (!file_exists($target)) {
            if (!rename($file, $target)) {
                return false;
            }
            chmod($target, 0777);
        }

        return  substr($target, strlen($upload_dir));
    }
}



?>