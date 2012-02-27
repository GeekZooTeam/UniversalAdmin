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
        Smarty3::instance()->display('test.html');
        
    }

    function xxx()
    {
    }
    // function play()
    // {
    //     $html = _GET('play');
    //     Smarty3::instance()->display($html.'html');
    // }
}

?>