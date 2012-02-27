<?php

Config::set(array(

    'db'     => array('mysql', 'localhost', 'xuanyano_geekzoo', 'geekzoo', 'xuanyano_site'),

    'menu' => array(
        '系统功能' => array(

            'menu' => array(
                'name' => '菜单管理',
                'action' => array(
                    'index'  => '菜单列表',
                    'create' => '菜单添加',
                    'update' => '菜单更新',
                    'delete' => '菜单删除'
                )
            ),

            'model' => array(
                'name' => '模型管理',
                'action' => array(
                    'index'  => '模型列表',
                    'create' => '模型添加',
                    'update' => '模型更新',
                    'delete' => '模型删除'
                )
            ),
    
            'resource' => array(
                'name' => '内容列表',
                'action' => array(
                    'index'  => '内容列表',
                    'create' => '内容添加',
                    'update' => '内容更新',
                    'delete' => '内容删除'
                )
            )

        ),

        '管理员' => array(

            'administrator' => array(
                'name' => '用户列表',
                'action' => array(
                    'index'  => '管理员列表',
                    'create' => '管理员添加',
                    'update' => '管理员更新',
                    'delete' => '管理员删除'
                )
            )
        ),

        '用户组' => array(

            'group' => array(
                'name' => '组管理',
                'action' => array(
                    'index'  => '用户组列表',
                    'create' => '用户组添加',
                    'update' => '用户组更新',
                    'delete' => '用户组删除'
                )
            )
        ),

    ),

    // 'menu'   => array(
    //         '网站管理' => array(
    //                 '模型管理' => 'model',
    //                 '菜单管理' => 'menu'
    //             ),
    //         '内容管理' => array(
    //                 '内容列表' => 'resource'
    //             ),
    //         '管理员' => array(
    //                 '用户列表' => 'administrator'
    //             )
    //      ),

    'theme' => 'new'

));


?>