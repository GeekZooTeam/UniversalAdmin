<?php



class Cookie
{
    private static $expire = 1036800;
    private static $path = '/';
    private static $domain = '';
    private static $secure = false;

    public static function setDomain($domain)
    {
        self::$domain = $domain;
    }

    public static function setSecure($secure)
    {
        self::$secure = $secure ? true : false;
    }

    public static function setPath($path)
    {
        self::$path = $path;
    }

    public static function set($key, $value, $expire = null)
    {
        $expire = $expire === null ? time() + self::$expire : $expire;

        return setcookie($key, $value, $expire, self::$path, self::$domain, self::$secure);
    }

    public static function get($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
    }

    public static function del($key)
    {
        return self::set($key, '', -1);
    }

    public static function clean()
    {
        foreach ($_COOKIE as $key => $val)
        {
            self::del($key);
        }
    }
}
?>