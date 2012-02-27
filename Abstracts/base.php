<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */


abstract class base_abs 
{
    function __construct($action, $method)
    {
        session_start();  
        
    }

    function msg($msg, $jumpurl='', $lev = 'notice', $t = 3) {
       if ($jumpurl) {
            $jumpurl = htmlspecialchars($jumpurl);
            if (substr($jumpurl, 0, 4) != 'http') {
                if ($jumpurl{0} != '/') {
                    $jumpurl = '/'.$jumpurl;
                }
                $jumpurl = SITE_URL.$jumpurl;
            }
            $ifjump = "<META HTTP-EQUIV='Refresh' CONTENT='$t; URL=$jumpurl'>";
            Smarty3::instance()->assign('jumpurl', $jumpurl);
            Smarty3::instance()->assign('lev', $lev);
            Smarty3::instance()->assign('ifjump', $ifjump);
        }

        Smarty3::instance()->assign('msg', $msg);
        Smarty3::instance()->display('message.html');
        exit;
    }
    
    function redirect($url = '') {
        $site = SITE_URL;
        if (!$url) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $site;
        }

        if (substr($url, 0, 4) != 'http') {
            if ($url{0} != '/') {
                $url = '/'.$url;
            }
            $url = $site.$url;
        }
        header('Location: ' . $url);
        exit;
    }
    
}

function get_attribute_value($res_id, $attribute_name)
{
    static $attribute = array();

    if (!isset($attribute[$res_id])) {
        $attribute[$res_id] = _model('attribute')->get_attribute_value($res_id);
    }
    
    return isset($attribute[$res_id][$attribute_name]) ? $attribute[$res_id][$attribute_name] : '';
}
?>