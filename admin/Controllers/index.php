<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

class Action extends admin_abs
{
    function index()
    {
        // Smarty3::instance()->assign('test', ' ');

        Smarty3::instance()->display('index.html');
    }
    
    // function delete()
    // {
    //     if (!$this->can_delete) {
    //         // NO permission
    //     }
    // }
    // 
    // function edit()
    // {
    //     if (!($this->permission & CAN_EDIT)) {
    //         // NO permission
    //     }
    // }
    
    function xxx()
    {
        Smarty3::instance()->display('compose.html');
    }
}


?>