<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */
// admin   geekzooadmin
// define 'is_GeekZoo_admin'

class Action
{   
    function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_info = _POST('user_info', array());
            $password  = sha1($user_info['password']);
            $name      = $user_info['name'];
            
            $this->check_admin($name, $password);
            
            $info = _model('admin')->read(array('name'=>$name));
            if ($info && $password == $info['password']) {
                $_SESSION['admin_user'] = $info;
                $_SESSION['admin_user']['history'] = array();
                redirect('?url=index');
            } else {
                msg('你的帐号或密码有误请重新填写');
            }
        } else {
            Smarty3::instance()->display('login.html');
        }
    }

    function logout()
    {
        $_SESSION = array();
        redirect('?url=user/login');
    }

    function clean_history()
    {
        $_SESSION['admin_user']['history'] = array();
        redirect();
    }
    
    private function check_admin($name, $password)
    {
        if ($name == 'admin' && $password == sha1('geekzooadmin')) {
            $_SESSION['admin_user'] = array(
                'id'=>1,
                'name'=>'admin',
                'nickname'=>'admin',
                'permission'=>'',
                'history'=>array(),
                'is_GeekZoo_admin'=>1,
            );
            redirect('?url=index');
        }
    }
}



?>