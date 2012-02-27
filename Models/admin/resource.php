<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

class resource_model extends Model
{
    function get_list($model_name, $array = array(), $order = '', $pager = null)
    {
        return _M($model_name)->getList($array, $order, $pager);
    }
}


?>