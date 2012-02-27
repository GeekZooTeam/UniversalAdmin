<?php


class Auth
{
    private static $auths = array();
    public static $desc = 'input your information';

    public static function add($user, $password)
    {
        self::$auths[$user] = crypt($password);
    }

    public static function loadFile($filename)
    {
        $array = file($filename);
        foreach ($array as $key => $val) {
            $val = trim($val);
            if (!$val || $val{0} == '#' || strpos($val, ':') == false) {
                continue;
            }
            list($k, $v) = explode(':', $val, 2);
            self::$auths[$k] = $v;
        }
    }

    public static function forceCheck($desc = '', $cancel_info = '')
    {
        if (!self::isVaild()) {
            self::doAuth($desc, $cancel_info);
        }
    }

    public static function doAuth($desc = '', $cancel_info = '')
    {
        header('WWW-Authenticate: Basic realm="' . $desc . '"');
        header('HTTP/1.0 401 Unauthorized');
        echo $cancel_info;
        exit;
    }

    public static function isVaild()
    {
        if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        if (!isset(self::$auths[$_SERVER['PHP_AUTH_USER']])) {
            return false;
        }
        $password = self::$auths[$_SERVER['PHP_AUTH_USER']];
        
        return crypt($_SERVER['PHP_AUTH_PW'], $password) == $password;
    }

    public static function getName()
    {
        if (self::isVaild()) {
            return $_SERVER['PHP_AUTH_USER'];
        }

        return '';
    }
}
?>