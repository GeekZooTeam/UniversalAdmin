<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */
class Action
{
    function index()
    {
        $file = $_FILES['Filedata'];
        if (empty($file['tmp_name'])) {
            exit('-1');
        }
        $id = Storage::add($file['tmp_name'], $file['name']);
        echo $id;
    }
}

?>