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
       
       $resource_list = $this->action.'_list.html';

       if (!Smarty3::instance()->templateExists($resource_list)) {
           $resource_list = 'resource_list.html';
       }
       
       $mid = _GET('mid', 0);
       $res_id = _GET('res_id', 0);
       
       if ($res_id) {
            $mid = get_info('resource', $res_id, 'model_id');
       }

       if ($mid) {
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

           
           if ($res_id) {
               $data = _model('resource')->getList(array('id'=>$res_id), new Pager(20));
           } else {
               // if ($search) {
               //      foreach ($attribute_search as $key => $val) {
               //          if ($val['type'] == 'model' && isset($search[$val['name']])) {
               //              $t = _M($val['value'][0])->read(array($val['value'][1]=>$search[$val['name']]));
               //              $search[$val['name']] = $t['id'];
               //          }
               //      }
               // }
               $data = _M($model_info['name'])->getList($search, $order, new Pager(20));            
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
           Smarty3::instance()->display($resource_list);
       } else {
           $model_list = _model('model')->getList();
           Smarty3::instance()->assign('data', $model_list);
           Smarty3::instance()->display('resource_pickmodel.html');
       }
   }

   function create()
   {
       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $attribute = _POST('attribute', array());
          $mid       = _POST('mid', 0);
          foreach ($attribute as $key => $val) {
            if (is_array($val)) {
                $attribute[$key] = implode(',',  $val);
            }
          }
          $m = _model('model')->read(array('id' => $mid));
          $result = _M($m['name'])->create($attribute);
          if ($result === false) {
              msg('添加失败');
          }
          if (is_array($result)) {
              msg(key($result).'已经存在');
          }

          msg('添加成功','?url=resource/create&mid='.$mid);
       } else {
            $mid = _GET('mid', 0);
            if ($mid && $model_info = _model('model')->read(array('id'=>$mid))) {
                $attribute = _model('attribute')->getList(array('model_id'=>$mid), ' ORDER BY view_order ASC ');
                foreach ($attribute as $key => $value) {
                     if ($value['type'] == 'select' || $value['type'] == 'checkbox' || $value['type'] == 'model') {
                         $attribute[$key]['value'] = array_map('trim', explode("\n", $value['value']));
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
                Smarty3::instance()->assign('mid', $mid);
                Smarty3::instance()->assign('attribute', $attribute);
                Smarty3::instance()->display('resource_create.html');
            }
       }
   }

   function update()
   {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $res_id =  _POST('rid', 0);
            $attribute = _POST('attribute', array());
            $add_time  = time();
            // $title     = _POST('title', '');
            $mid       = _POST('mid', 0);
            //判断唯一
            foreach ($attribute as $key => $value) {
                $check = _model('attribute')->check_unique($key, $value);
                if ($check && $check['res_id'] != $res_id) {
                    msg(get_info('attribute', $key, 'name').'已经存在');
                }
            }
            //
            $t_attribute = _model('attribute')->getList(array('model_id'=>$mid), ' ORDER BY view_order ASC ');
            // if (!$title) {
            //  msg('标题不能为空');
            //  }
            // _model('resource')->update(array('id'=>$res_id), array('title'=>$title));

            foreach ($t_attribute as $key => $value) {
                if ($value['type'] == 'file') {
                    if (isset($_FILES["attribute_".$value['id']]) && is_uploaded_file($_FILES["attribute_".$value['id']]["tmp_name"])) {
                        $attribute[$value['id']] = Storage::add($_FILES["attribute_".$value['id']]["tmp_name"], $_FILES["attribute_".$value['id']]["name"]);
                    }
                }
                
                if ($value['type'] == 'checkbox') {
                    if (!empty($attribute[$value['id']])) {
                        $attribute[$value['id']] = implode(',',  $attribute[$value['id']]);
                    }
                }
            }
            foreach ($attribute as $key => $value) {
                 if (!$r = _model('attribute_value')->read(array('res_id'=>$res_id, 'attribute_id'=>$key))) {
                     _model('attribute_value')->create(array('res_id'=>$res_id, 'attribute_id'=>$key, 'value'=>$value));
                 } else {
                     _model('attribute_value')->update(array('res_id'=>$res_id, 'attribute_id'=>$key), array('value'=>$value));
                 }
            }
            msg('更新成功','?url=resource&mid='.$mid);
        } else {
           $res_id = _GET('id', 0);
           if ($res_id && $res_info = _model('resource')->read(array('id'=>$res_id))) {
               $attribute = _model('attribute')->getList(array('model_id'=>$res_info['model_id']), ' ORDER BY view_order ASC ');
               foreach ($attribute as $key => $value) {
                    if ($value['type'] == 'select' || $value['type'] == 'checkbox' || $value['type'] == 'model') {
                       $attribute[$key]['value'] = array_map('trim', explode("\n", $value['value']));
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
               Smarty3::instance()->assign('res_info', $res_info);
               Smarty3::instance()->assign('attribute', $attribute);
               Smarty3::instance()->assign('mid', $res_info['model_id']);
               Smarty3::instance()->display('resource_update.html');
           } else {
                msg('参数有误或不存在这个内容');
           }
        }
   }

   function delete()
   {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_array = array_filter(_POST('check', array()));
            $rid = end($id_array);
            $res_info = _model('resource')->read(array('id'=>$rid));
            foreach ($id_array as $key => $value) {
                _model('resource')->delete(array('id'=>$value));
                _model('attribute_value')->delete(array('res_id'=>$value));
            }
            redirect('?url=resource&mid='.$res_info['model_id']);
        } else {
            $id = _GET('id', 0);
            if ($id && $res_info = _model('resource')->read(array('id'=>$id))) {
                _model('resource')->delete(array('id'=>$id));
                _model('attribute_value')->delete(array('res_id'=>$id));
                redirect('?url=resource&mid='.$res_info['model_id']);
            }
        }
   }
   
   function del_att_value()
   {
       $res_id = _GET('res_id', 0);
       $attribute_id = _GET('attribute_id', 0);
       _model('attribute_value')->update(array('res_id'=>$res_id, 'attribute_id'=>$attribute_id), array('value'=>''));
       echo '删除成功';
   }
   
   function export()
   {
        $mid = _GET('mid', 0);
        if ($mid) {
            $search = _GET('search', array());
            $search = array_filter($search);

            $order = array('add_time'=>'DESC');
            $model_info = _model('model')->read(array('id'=>$mid));
            $data = _M($model_info['name'])->getList($search, $order);
            $attribute_search = _model('attribute')->getList(array('model_id'=>$mid), 'ORDER BY view_order ASC');

            foreach ($attribute_search as $key => $value) {
                 if ($value['type'] =='file') {
                     unset($attribute_search[$key]);
                 }
                 if ($value['type'] == 'checkbox' || $value['type'] == 'select' || $value['type'] == 'model') {
                     $attribute_search[$key]['value'] = explode("\n",  $value['value']);
                     $attribute_search[$key]['value'] = array_map('trim', $attribute_search[$key]['value']);
                 }
                 if ($value['type'] == 'model') {
                   $param = $attribute_search[$key]['value'];
                   $t = _M($param[0])->getList();
                   $tmp = array();
                   foreach ($t as $val) {
                       $tmp[$val['id']] = get_attribute_value($val['id'], $param[1]);
                   }
                   $attribute_search[$key]['value'] = $tmp;
                 }
            }

            $attribute = $attribute_search;//需要进行搜索的字段

            Smarty3::instance()->assign('attribute', $attribute);
            Smarty3::instance()->assign('data', $data);

            // 
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="'.date('Y-m-d').'.xls"');
            Smarty3::instance()->display('exprot_xls.html');
        }
   }
   
}
?>