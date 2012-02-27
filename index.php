<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */
define('ROOT_PATH', dirname(__FILE__));
define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST']);

require_once ROOT_PATH.'/Library/__init__.php';
require_once ROOT_PATH.'/config.php';

// Cookie::setDomain('.xxxx.com');
Database::$instance = Database::connect(Config::get('db'));
Database::$instance->initialization = array(
    'SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary',
    "SET sql_mode=''"
);

Model::$dir = ROOT_PATH.'/Models/admin';
Model::$dbPrefix = array('admin_'=>'*');


require_once ROOT_PATH.'/sys/sys.php';
if (!defined('SYS_IS_LOAD')) {
	die('系统文件损坏');
}

Smarty3::instance()->setTemplateDir(ROOT_PATH.'/Templates');
Smarty3::instance()->compile_dir = ROOT_PATH.'/tmp/compile';
Smarty3::instance()->error_reporting = E_ALL ^ E_NOTICE;
//Smarty3::instance()->allow_php_tag = true;

try {
    $result = Controller::dispatch(@$_SERVER['REDIRECT_URL'], ROOT_PATH.'/Controllers');
} catch (Exception $e) {
    // out put 404 page
    if ($e->getCode() == 404) {
        header("HTTP/1.0 404 Not Found");
        msg('页面不存在!');
    }
    die($e);
}

if ($result && Controller::$format == 'json') {
    echo json_encode($result);
}
$_SESSION['LAST_URL'] = SITE_URL.$_SERVER['REQUEST_URI'];


?>
