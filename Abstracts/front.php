<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

require_once ROOT_PATH.'/Abstracts/base.php';

abstract class front_abs extends base_abs
{
    function __construct($action, $method)
    {
        parent::__construct($action, $method);
        
        $model_arr = _model('model')->getList("WHERE name!='user'");
        Smarty3::instance()->assign('menu', $model_arr);
        Smarty3::instance()->assign('action', $action);
    
        if (empty($_SESSION['user'])) {
            $this->redirect('login');
        }
    }
}






?>