<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */


require_once 'Library/Cipher.php';

Cipher::setSalt('test');
$post = array();
if (isset($_POST['data'])) {
	$post = json_decode(Cipher::decrypt($_POST['data']), true);	
	///写数据
	
	@file_put_contents('./test_data.txt', var_export($post, true));
    
	
	$data = array('next_sync'=>$post['sync_time']+20, 'rm_site'=>0, 'rand'=>$post['rand']);

	echo  Cipher::encrypt(json_encode($data));
}


?>