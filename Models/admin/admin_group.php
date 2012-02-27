<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */

class admin_group_model extends Model
{

    function get_permission($ids)
    {
        $table = $this->table;
        if (is_array($ids)) {
            $ids = implode(',', $ids);
        }
        $group = $this->db->getAll("SELECT * FROM $table WHERE id in($ids)");
        $tmp = array();
        foreach ($group as $key => $value) {
            if (!empty($value['append_group_id'])) {
                $tmp[] = $value['append_group_id'];
            }
        }
        if (!empty($tmp)) {
            $append_group = implode(',', $tmp);
            $append_group = $this->db->getAll("SELECT * FROM $table WHERE id in($append_group)");
            $group = array_merge($group , $append_group);
        }
        //转换权限的格式
        $permission = array();
        $out_data = array(); 
        foreach ($group as $key => $value) {
            if ($value['permission']) {
                $permission[] = $this->format_permission($value['permission']);
            }
        }
        //输出权限
        if ($permission) {
            foreach ($permission as $key => $value) {
                foreach ($value as $k => $v) {
                    foreach ($v as $l) {
                        $out_data[$k][] = $l;
                    }
                }
            }
        }
        //过滤重复
        foreach ($out_data as $key => $value) {
           $out_data[$key] = array_unique($value);
        }
        return $out_data;
    }
    
    function format_permission($permission)
    {
        $temp = array();
        $permission = explode(',', $permission);
        foreach ($permission as $key => $value) {
           $t = explode(':', $value);
           $temp[$t[0]] = explode('|', $t[1]);
        }
        return $temp;
    }
    
}


?>