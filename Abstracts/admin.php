<?php

/*
 * This file is part of the Geek-Zoo Projects.
 * 
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */


class admin_abs 
{

    function __construct($action, $method)
    {
        if (!isset($_SESSION['admin_user'])) {
            redirect('?url=user/login');
        }

        // $menu = _model('menu')->getMenu();
        // 
        // $menu['网站管理']['用户管理'] = 'model&action=attribute&mid=1';

        $opt = _model('menu')->getOption();
        // 载入新的菜单
        if ($opt && $opt->valid()) {
            $menu = array();
            $kk = '';
            foreach ($opt as $key => $val) {

                if (!$val['parent_id']) {
                    if (!empty($val['is_hide'])) {
                        $kk = '';
                    } else {
                        $kk = $val['name'];
                        $menu[$kk] = array();
                    }
                } else {
                    if (!empty($val['is_hide'])) {
                        continue;
                    } else {
                        if ($kk) {
                            $menu[$kk][$val['name']] = $val['url'];
                        }
                    }
                }

            }
        }

        // history
        $history = $_SESSION['admin_user']['history'];
        $path = $action;
        if ($method != 'index') {
            $path .= "/{$method}";
        }

        foreach ($menu as $val) {
            foreach ($val as $k => $v) {
                if ($v == $path) {
                    $add = array($k=>$v);
                    if (($key = array_search($add, $history)) !== false) {
                        unset($history[$key]);
                    }
                    array_unshift($history, $add);
                    break;
                }
            }
        }

        while (count($history) > 5) {
            array_pop($history);
        }
        reset($history);
        $_SESSION['admin_user']['history'] = $history;
        
        Smarty3::instance()->assign('history', $history);

        if ($action == 'index' && $method == 'index') {
            reset($menu);
            $url = current(current($menu));
            redirect('?url='.$url);
        }

        if ($_SESSION['admin_user']['id'] != 1) {
            
            if (empty($_SESSION['admin_user']['admin_group_id'])) {
                msg('你没有任何权限操作', '?url=user/logout');
            }
            
            $user_info =  _model('admin')->read(array('id'=>$_SESSION['admin_user']['id']));
            $permission = _model('admin_group')->get_permission($user_info['admin_group_id']);
            
            if (empty($permission)) {
                msg('你没有任何权限操作', '?url=user/logout');
            }
            ///根据权限处理菜单  去掉无权限的菜单

            $permission_menu = array_keys($permission);
						
            if ($permission_menu) {
								//如果没有列表INDEX去掉菜单
								foreach ($permission as $key => $value) {
										if (!in_array('index', $value)) {
												$k = array_search($key, $permission_menu);
		                    unset($permission_menu[$k]);
										}
								}
								//
                foreach ($menu as $key => $value) {
                    foreach ($value as $k=>$v) {
                        if (!in_array($v, $permission_menu)) {
                            unset($menu[$key][$k]);
                        }
												
                    }
                }
                $menu = array_filter($menu);
            } else {
                $menu = array();
            }
            ///
            //判断权限
            if (!in_array($action, $permission_menu)) {
                msg('无权限');
            }
            if (!in_array($method, $permission[$action])) {
                msg('无权限');
            }
            //
        }


        $this->action = $action;
        Smarty3::instance()->assign('admin_action', $action);
        Smarty3::instance()->assign('menu', $menu);
        Smarty3::instance()->assign('menu_action', $action);
    }

}

?>