<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */

class Action extends admin_abs
{   
    function index()
    {
        $data = _model('admin_group')->getList('ORDER BY id DESC');
        Smarty3::instance()->assign('data', $data);
        Smarty3::instance()->display('group_list.html');
    }
    
    function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = _POST('group_name');
            $permission = _POST('permission', array());
            $append_group_id = array_filter(_POST('append_group_id', array()));
            $insert = array();
            if (empty($name)) {
                msg("请填写名称");
            }
            $tmp = array();
            foreach ($permission as $key => $value) {
                $tmp[] = $key.':'.implode('|', $value);
            }
            $insert['name'] = $name;
            $tmp = implode(',', $tmp);
            $insert['permission'] = $tmp;
            $insert['append_group_id'] = implode(',', $append_group_id);
            $id = _model('admin_group')->create($insert);
            if ($id) {
                msg('添加成功', '?url=group');
            } else {
                msg('添加失败');
            }
        } else {
            $group_list = _model('admin_group')->getList('ORDER BY id DESC');
            Smarty3::instance()->assign('group_list', $group_list);
            Smarty3::instance()->display('group_add.html');
        }
    }
    
    function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = _POST('group_name');
            $id = _POST('id');
            $permission = _POST('permission', array());
            $append_group_id = array_filter(_POST('append_group_id', array()));
            $insert = array();
            if (empty($name)) {
                msg("请填写名称");
            }
            $tmp = array();
            foreach ($permission as $key => $value) {
                $tmp[] = $key.':'.implode('|', $value);
            }
            $insert['name'] = $name;
            $tmp = implode(',', $tmp);
            $insert['permission'] = $tmp;
            $insert['append_group_id'] = implode(',', $append_group_id);
            _model('admin_group')->update(array('id'=>$id), $insert);
            msg('更新成功', '?url=group');

        } else {
            $id = _GET('id', 0);
            if (!$id || !$data = _model('admin_group')->read(array('id'=>$id))) {
                msg('参数错误或不存在');
            }
            if (!empty($data['append_group_id'])) {
                $data['append_group_id'] = explode(',', $data['append_group_id']);
            } else {
                $data['append_group_id'] = array();
            }
            if (!empty($data['permission'])) {
                $temp = explode(',', $data['permission']);
                $data['permission'] = array();
                foreach ($temp as $value) {
                     $tmp = explode(':', $value);
                     $data['permission'][$tmp[0]] = explode('|', $tmp[1]);
                }
            }
            $group_list = _model('admin_group')->getList('WHERE id!='.$id.' ORDER BY id DESC');
            Smarty3::instance()->assign('group_list', $group_list);
            Smarty3::instance()->assign('data', $data);
            Smarty3::instance()->display('group_update.html');
        }
    }
    
    function delete()
    {

        $id = _GET('id', 0);
        if ($id && $info = _model('admin_group')->read(array('id'=>$id))) {
            _model('admin_group')->delete(array('id'=>$id));
            redirect('?url=group');
        }
    }


}



?>