<?php

class Config
{
    private static $array = array();

    public static function set($key, $value = '')
    {
        if (!is_array($key)) {
            $key = array($key => $value);
        }

        foreach ($key as $k => $v) {
            self::$array[$k] = $v;
        }

        return true;
    }

    public static function get($key)
    {
        if (func_num_args() > 1) {
            $key = func_get_args();
            $out = array();
            foreach ($key as $key => $val) {
                $out[] = @self::$array[$key];
            }

            return $out;
        }

        return @self::$array[$key];
    }
}


?>