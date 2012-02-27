<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

function get_user_group() {
    return _M('user_group')->getList();
}

if (!isset($_GET['mid'])) {
    $_GET['mid'] = 1;
}

require ROOT_PATH.'/Controllers/resource.php';

?>