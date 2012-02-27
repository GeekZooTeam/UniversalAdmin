<?php


class Smarty3
{
    private static $instance = null;

    public static function instance()
    {
        if (self::$instance === null) {
            // set smarty config
            self::$instance = new Smarty();
        }

        return self::$instance;
    }
}

function gz_smarty_prefilter() {
    
}

?>