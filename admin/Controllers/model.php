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
        $action = _GET('action', '');
        switch ($action) {
            case 'attribute':
                $mid = _GET('mid', 0);
                if (!$mid || !$model_info = _model('model')->read(array('id'=>$mid))) {
                    msg('参数有误或此模型不存在');
                }
                $data = _model('attribute')->getList(array('model_id'=>$mid), 'ORDER BY view_order ASC');
                Smarty3::instance()->assign('data', $data);
                Smarty3::instance()->assign('model_info', $model_info);
                Smarty3::instance()->display('attribute_list.html');
                break;
            default:
                $data = _model('model')->getList();
                Smarty3::instance()->assign('data', $data);
                Smarty3::instance()->display('model_list.html');
                break;
        }
    }
    
    function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = _POST('action', '');
            switch ($action) {
                case 'model':
                    $name = _POST('name');
                    $alias_name = _POST('alias_name');
                    $detail = _POST('detail');
                    if ($name) {
                       $model_info = _model('model')->read(array('name'=>$name));
                       if ($model_info) {
                           msg('已经存在此模型');
                       }
                       $id = _model('model')->create(array('name'=>$name, 'alias_name'=>$alias_name, 'detail'=>$detail));
                       if ($id) {
                         msg('添加成功跳转到给模型添加字段', '?url=model/create&action=attribute&mid='.$id);
                       }
                    } else {
                        msg('必须填写模型名称');
                    }
                    break;
                case 'attribute':
                    $info = _POST('info', array());
                    if (!$info['model_id'] || !$model_info = _model('model')->read(array('id'=>$info['model_id']))) {
                        msg('参数有误或此模型不存在');
                    }
                    if (!$info['type'] || !$info['name']) {
                        msg('属性和名称必须填写');
                    }
                    $id = _model('attribute')->create($info);
                    if ($id) {
                        msg('添加成功', '?url=model&action=attribute&mid='.$info['model_id']);
                    } else {
                        msg('添加失败');
                    }
                    break;
            }
        } else {
            $action = _GET('action', '');
            switch ($action) {
                case 'model':
                    Smarty3::instance()->display('model_create.html');
                    break;
                case 'attribute':
                    $mid = _GET('mid', 0);
                    if (!$mid || !$model_info = _model('model')->read(array('id'=>$mid))) {
                        msg('参数有误或这个模型不存在');
                    }
                    Smarty3::instance()->assign('mid', $mid);
                    Smarty3::instance()->display('attribute_create.html');
                    break;
            }
        }
    }
    
    function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = _POST('action', '');
            $id = _POST('id', 0);
            switch ($action) {
                case 'model':
                    $alias_name = _POST('alias_name', '');
                    $detail = _POST('detail');
                    
                    _model('model')->update(array('id'=>$id), array('alias_name'=>$alias_name, 'detail'=>$detail));
                    redirect('?url=model');
                    break;
                case 'attribute':
                    $info = _POST('info', array());
                    if (!$info['is_hide']) {
                        $info['is_hide'] = '0';
                    }
                    if (!$info['is_unique']) {
                        $info['is_unique'] = '0';
                    }
                    _model('attribute')->update(array('id'=>$id), $info);
                    redirect('?url=model&action=attribute&mid='.$info['model_id']);
                    break;
            }
        } else {
            $action = _GET('action', '');
            $id = _GET('id', 0);
            if (!$id) {
                msg('参数有误');
            }
            switch ($action) {
                case 'model':
                    if (!$model_info = _model('model')->read(array('id'=>$id))) {
                        msg('这个模型不存在');
                    }
                    Smarty3::instance()->assign('data', $model_info);
                    Smarty3::instance()->display('model_update.html');
                    break;
                case 'attribute':
                    $attribute_info = _model('attribute')->read(array('id'=>$id));
                    Smarty3::instance()->assign('data', $attribute_info);
                    Smarty3::instance()->display('attribute_update.html');
                    break;
            }
        }
    }
    
    function delete()
    {
        $action = _GET('action', '');
        $id = _GET('id', 0);
        switch ($action) {
            case 'model':
                if (!$id) {
                    msg('参数有误');
                }
                $info = _model('resource')->read(array('model_id'=>$id));
                if (!$info) {
                    _model('model')->delete(array('id'=>$id));
                    _model('attribute')->delete(array('model_id'=>$id));
                    msg('模型和模型属性全部删除成功','?url=model');
                } else {
                    msg('这个模型还有相关的内容没有被清理！不能直接删除模型，如果想删除模型请删除和模型有关的内容');
                }
                break;
             case 'attribute':
                if (!$id) {
                    msg('参数有误');
                }
                $attribute_info = _model('attribute')->read(array('id'=>$id));
                _model('attribute')->delete(array('id'=>$id));
                redirect('?url=model&action=attribute&mid='.$attribute_info['model_id']);
                break;
        }
        
    }
}


?>