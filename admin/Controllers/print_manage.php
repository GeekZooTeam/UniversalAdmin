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
        $mid = 3;
        $search = array('是否打印'=>'否');

        $order = array('add_time'=>'DESC');
        $model_info = _model('model')->read(array('id'=>$mid));

        $attribute_search = _M($model_info['name'])->getAttribute();
        foreach ($attribute_search as $key => $value) {
             if ($value['type'] =='file') {
                 unset($attribute_search[$key]);
             }
        }

        
        $data = _M($model_info['name'])->getList($search, $order, new Pager(20));            

        foreach ($data['data'] as &$val) {
            if ($val['订单状态']) {
                $t = _M('order_list')->getInfo($val['订单状态']);
                $val['套系价格'] = $t['套系价格'];
            } else {
                $val['套系价格'] = '-';
            }
        }

        $attribute = $attribute_search;//需要进行搜索的字段
        
        foreach ($attribute as $key => $value) {
             if ($value['type'] =='text') {
                 unset($attribute[$key]);
             }
        }
        
        Smarty3::instance()->assign('attribute', $attribute);
        Smarty3::instance()->assign('attribute_search', $attribute_search);
        Smarty3::instance()->assign($data);
        Smarty3::instance()->assign('mid', $mid);
        Smarty3::instance()->assign('model_info', $model_info);
        Smarty3::instance()->display('print_manage_list.html');
    }

    function doprint()
    {
        if ($id = _GET('id', 0)) {
            _M('order_list')->update($id, array('是否打印'=>'是'));
            echo 1;
        }
    }
}


?>