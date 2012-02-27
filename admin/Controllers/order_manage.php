<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

// if (!isset($_GET['mid'])) {
//     $_GET['mid'] = 3;
// }
// 
// require ROOT_PATH.'/Controllers/resource.php';


class Action extends admin_abs
{
    function delete()
    {
         if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             $id_array = array_filter(_POST('check', array()));
             $rid = end($id_array);
             $res_info = _model('resource')->read(array('id'=>$rid));
             foreach ($id_array as $key => $value) {

                 $info = _M('guest')->getInfo($value);


                 if ($info['订单状态']) {
                     _model('resource')->delete(array('id'=>$info['订单状态']));
                     _model('attribute_value')->delete(array('res_id'=>$info['订单状态']));
                 }
                 if ($info['调查表']) {
                     _model('resource')->delete(array('id'=>$info['调查表']));
                     _model('attribute_value')->delete(array('res_id'=>$info['调查表']));
                 }

                 _model('resource')->delete(array('id'=>$value));
                 _model('attribute_value')->delete(array('res_id'=>$value));
                 
             }
             redirect('?url=order_manage');
         } else {
             $id = _GET('id', 0);
             if ($id && $res_info = _model('resource')->read(array('id'=>$id))) {

                 $info = _M('guest')->getInfo($id);
                 
                 if ($info['订单状态']) {
                     _model('resource')->delete(array('id'=>$info['订单状态']));
                     _model('attribute_value')->delete(array('res_id'=>$info['订单状态']));
                 }
                 if ($info['调查表']) {
                     _model('resource')->delete(array('id'=>$info['调查表']));
                     _model('attribute_value')->delete(array('res_id'=>$info['调查表']));
                 }

                 _model('resource')->delete(array('id'=>$id));
                 _model('attribute_value')->delete(array('res_id'=>$id));
                 
                 redirect('?url=order_manage');
             }
         }
    }

    function index()
    {
        $mid = 3;
        $search = _GET('search', array());
        $search = array_filter($search);
        
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
        Smarty3::instance()->display('order_manage_list.html');
    }
    
    function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res_id =  _POST('rid', 0);
            $attribute = _POST('attribute', array());
            $attribute2 = _POST('attribute2', array());
            
            $add_time  = time();
            // $title     = _POST('title', '');
            $mid = 3;

            $info = _M('guest')->getInfo($res_id);
            _M('guest')->update($res_id, $attribute);
            
            if ($info['订单状态']) {
                _M('order_list')->update($info['订单状态'], $attribute2);
            } else {
                $attribute2['处理人'] = $attribute['操作人'];
                $attribute2['客户信息'] = $res_id;
                $id = _M('order_list')->create($attribute2);
                _M('guest')->update($res_id, array('订单状态'=>$id));
            }

            msg('更新成功','?url=order_manage');
        } else {
           $res_id = _GET('id', 0);
           if ($res_id && $res_info = _model('resource')->read(array('id'=>$res_id))) {
               $attribute = _M('guest')->getAttribute();
               foreach ($attribute as $key => $value) {
                    if (in_array($value['name'], array('订单状态', '调查表', '是否打印'))) {
                        unset($attribute[$key]);
                        continue;
                    }
                    if ($value['type'] == 'model') {
                      $param = $attribute[$key]['value'];
                      $t = _M($param[0])->getList();
                      $tmp = array();
                      foreach ($t as $val) {
                          $tmp[$val['id']] = get_attribute_value($val['id'], $param[1]);
                      }
                      $attribute[$key]['value'] = $tmp;
                    }
               }
               
               $info = _M('guest')->getInfo($res_id);
               if ($info['订单状态']) {
                   Smarty3::instance()->assign('res_id_2', $info['订单状态']);
               }

               $attribute2 = _M('order_list')->getAttribute();
               foreach ($attribute2 as $key => $val) {
                    if (in_array($value['name'], array('处理人', '客户信息', '订单状态'))) {
                        unset($attribute2[$key]);
                        continue;
                    }
               }
               Smarty3::instance()->assign('attribute2', $attribute2);
               
               Smarty3::instance()->assign('res_info', $res_info);
               Smarty3::instance()->assign('attribute', $attribute);
               Smarty3::instance()->assign('mid', $res_info['model_id']);
               Smarty3::instance()->display('order_manage_update.html');
           } else {
                msg('参数有误或不存在这个内容');
           }
        }
    }
    
    function export()
    {
        $mid = 3;
        $search = _GET('search', array());
        $search = array_filter($search);

        $order = array('add_time'=>'DESC');
        $model_info = _model('model')->read(array('id'=>$mid));
        $data = _M($model_info['name'])->getList($search, $order);
        
        foreach ($data as &$val) {
            if ($val['订单状态']) {
                $t = _M('order_list')->getInfo($val['订单状态']);
                $val['套系价格'] = $t['套系价格'];
            } else {
                $val['套系价格'] = '-';
            }
        }
        
        $attribute_search = _M($model_info['name'])->getAttribute();

        foreach ($attribute_search as $key => $value) {
             if ($value['type'] =='file') {
                 unset($attribute_search[$key]);
             }
        }
        $attribute_search[] = array('name'=>'套系价格');

        
        $attribute = $attribute_search;//需要进行搜索的字段

        Smarty3::instance()->assign('attribute', $attribute);
        Smarty3::instance()->assign('data', $data);

        // 
        header('Content-type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d').'.xls"');
        Smarty3::instance()->display('exprot_xls.html');
    }

}
?>