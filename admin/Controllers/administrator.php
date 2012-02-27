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
	    $group_id = _GET('group_id', 0);
	    if ($group_id) {
	        $data = _model('admin')->getList('WHERE find_in_set('.$group_id.',admin_group_id) ORDER BY id DESC', new Pager(20));
	    } else {
    	    $data = _model('admin')->getList('ORDER BY id DESC', new Pager(20));
	    }
	    Smarty3::instance()->assign($data);
	    Smarty3::instance()->display('user_list.html');
	}
	
	function create()
	{
	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	        $info = _POST('info', array());
	        $admin_group_id = array_filter(_POST('admin_group_id', array()));
	        if ($info['password'] && $info['name']) {
	            $name = _model('admin')->read(array('name'=>$info['name']));
	            if (!$name) {
	                $info['password'] = sha1($info['password']);
    	            $info['admin_group_id'] = implode(',', $admin_group_id);
        	        _model('admin')->create($info);
        	        redirect('?url=administrator');
	            } else {
	               msg('用户名存在');
	            }
	        } else {
	           msg('用户名和密码不能为空');
	        }
        } else {
            $group_list = _model('admin_group')->getList('ORDER BY id DESC');
    	    Smarty3::instance()->assign('group_list', $group_list);
    	    Smarty3::instance()->display('user_create.html');
        }
	}
	
	function update()
	{
	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	        $info = _POST('info', array());
	        $admin_group_id = array_filter(_POST('admin_group_id', array()));
	        $id = _POST('id', 0);
	        if ($info['name']) {
	            if (!$name = _model('admin')->read("WHERE id<>$id AND name='{$info[name]}'")) {
	                if ($info['password']) {
    	                $info['password'] = sha1($info['password']);
	                } else {
	                   unset($info['password']);
	                }

    	            $info['admin_group_id'] = implode(',', $admin_group_id);
        	        _model('admin')->update(array('id'=>$id), $info);
        	        redirect('?url=administrator');
	            } else {
	               msg('用户名存在');
	            }
	        } else {
	           msg('用户名和密码不能为空');
	        }
	    } else {
	        $id = _GET('id', 0);
	        if ($id && $data = _model('admin')->read(array('id'=>$id))) {
	            if ($data['admin_group_id']) {
	                $data['admin_group_id'] = explode(',', $data['admin_group_id']);
	            }
                $group_list = _model('admin_group')->getList('WHERE id!='.$id.' ORDER BY id DESC');
        	    Smarty3::instance()->assign('group_list', $group_list);
        	    Smarty3::instance()->assign('data', $data);
        	    Smarty3::instance()->display('user_update.html');
	        }
	    }
	}
	
    function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_array = array_filter(_POST('check', array()));

            foreach ($id_array as $key => $value) {
                if ($value == 1) {
                } else {
                    _model('admin')->delete(array('id'=>$value));
                }
            }
            redirect('?url=administrator');
        } else {
            $id = _GET('id', 0);
            if ($id == 1) {
                msg('创始人不可删除');
            }
            if ($id && $admin_info = _model('admin')->read(array('id'=>$id))) {
                _model('admin')->delete(array('id'=>$id));
                redirect('?url=administrator');
            }
        }
    }
}

?>