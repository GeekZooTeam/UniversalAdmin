<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */


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



function trim_right($str)
{
    $len = strlen($str);
    /* 为空或单个字符直接返回 */
    if ($len == 0 || ord($str{$len - 1}) < 127) {
        return $str;
    }
    /* 有前导字符的直接把前导字符去掉 */
    if (ord($str{$len - 1}) >= 192) {
       return substr($str, 0, $len - 1);
    }
    /* 有非独立的字符，先把非独立字符去掉，再验证非独立的字符是不是一个完整的字，不是连原来前导字符也截取掉 */
    $r_len = strlen(rtrim($str, "\x80..\xBF"));
    if ($r_len == 0 || ord($str{$r_len - 1}) < 127) {
        return sub_str($str, 0, $r_len);
    }

    $as_num = ord( ~ $str{$r_len - 1});
    if ($as_num > (1 << (6 + $r_len - $len))) {
        return $str;
    } else {
        return substr($str, 0, $r_len - 1);
    }
}

function substr_fix($string, $len = 4) {
    $old = $string;
    $len *= 2;
    if (strlen($string) <= $len) {
        return $string;
    }
    $chinese = "(?:[".chr(228)."-".chr(233)."][".chr(128)."-".chr(191)."][".chr(128)."-".chr(191)."])";
    preg_match_all("/$chinese|\S|\s/", $string, $out);
    $string = '';
    foreach ($out[0] as $key => $val) {
        $len = strlen($val) == 1 ? $len - 1 : $len - 2;
        if ($len < 0) {
            break;
        }
        $string .= $val;
    }
    $string = trim_right($string);
    
    if ($string == $old) {
        return $string;
    }
    return $string;
}

function gen_theme_url($url) {
    return 'Templates/'.Config::get('theme').'/'.$url;
}

function get_info($table, $id, $key) {
    static $info = array();
    if (!isset($info[$id.'_'.$table])) {
        $info[$id.'_'.$table] = _model($table)->read(array('id'=>$id));
    }

    if (!isset($info[$id.'_'.$table][$key])) {
        return '';
    }

    return $info[$id.'_'.$table][$key];
}

function get_attribute_value($res_id, $attribute_name)
{
    static $attribute = array();

    if (!isset($attribute[$res_id])) {
        $attribute[$res_id] = _model('attribute')->get_attribute_value($res_id);
    }
    
    return isset($attribute[$res_id][$attribute_name]) ? $attribute[$res_id][$attribute_name] : '';
}

function send_email($to, $subject, $content) {
	try {
    
		$mail             = new PHPMailer(true); // defaults to using php "mail()"
	    $mail->CharSet    = "utf-8"; // 设置字符集编码
	    $mail->Encoding   = "base64";//设置文本编码方式

	    $mail->SetFrom('admin@pinte.com');

	    $mail->AddReplyTo("no-reply@pinte.com");

	    $mail->AddAddress($to);

	    $mail->Subject    = $subject;

	    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";

	    $mail->MsgHTML($content);

	    $mail->Send();
    } catch (Exception $e) {
        return false;
    }

    return true;
}


?>