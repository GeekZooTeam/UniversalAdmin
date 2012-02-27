<?php


function get_ext_array() {
    $ext = array();
    $ext_array = file(ROOT_PATH.'/library/icons/ext.conf');
    foreach ($ext_array as $val) {
        if ($val = trim($val)) {
            $array = array_values(array_filter(explode("\t", $val)));
            if (@$array[1]) {
                $t = array_filter(explode(' ', $array[1]));
                foreach ($t as $v) {
                    $v = ltrim($v, '.');
                    $ext[$v] = $array[0];
                }
            }
        }
    }

    return $ext;
}

function redirect($info = '') {
    $xx = parse_url($_SERVER['HTTP_REFERER']);
    $url = $xx['scheme'].'://'.$xx['host'].$xx['path'];
    $param = array();
    if (isset($xx['query'])) {
        parse_str($xx['query'], $param);
    }
    $info && $param['info'] = $info;
    $param && $url .= '?'.http_build_query($param);
    //$url = http_build_url($_SERVER['HTTP_REFERER'], array('query'=>'info='.$info));
    header('Location: '.$url);
    exit;
}

function delete($dir) {
    if (is_file($dir)) {
        return unlink($dir);
    }
    $path = new DirectoryIterator($dir);
    foreach ($path as $val) {
        if (!$val->isDot()) {
            delete($val->getPathname());
        }
    }

    return rmdir($dir);
}
?>