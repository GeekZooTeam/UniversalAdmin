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
        $data = _model('menu')->getOption();
        Smarty3::instance()->assign('data', $data);
        Smarty3::instance()->display('menu.html');
    }

    function delete()
    {
        $id = _GET('id', 0);
        if ($id) {
            _model('menu')->delete(array('parent_id'=>$id));
            _model('menu')->delete(array('id'=>$id));
        }
        redirect('?url=menu');
    }

    function update()
    {
        $info = _model('menu')->get(_GET('id', 0));
        Smarty3::instance()->assign('info', $info);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_info = _POST('info', array());
            empty($new_info['is_hide']) && $new_info['is_hide'] = 0;
            if (_model('menu')->read(array('id'=>$info['id']))) {
                _model('menu')->update(array('id'=>$info['id']), $new_info);
            } else {
                $info = array_merge($info, $new_info);
                _model('menu')->create($info);
            }
            msg('更新成功!', '?url=menu');
        } else {
            $parent = _model('menu')->getParents();
            Smarty3::instance()->assign('parent', $parent);
            Smarty3::instance()->display('menu_create.html');
        }
    }

    function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $info = _POST('info', array());
            $info['view_order'] = intval($info['view_order']);
            $info['id'] = _model('menu')->getNewId();
            _model('menu')->create($info);
            msg('添加成功!', '?url=menu/create');
        } else {
            $parent = _model('menu')->getParents();
            Smarty3::instance()->assign('parent', $parent);
            Smarty3::instance()->display('menu_create.html');
        }
    }
}

?>