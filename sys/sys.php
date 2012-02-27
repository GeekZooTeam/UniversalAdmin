<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */

define('SYS_IS_LOAD', 'yes');
define('SYS_ROOT_PATH', dirname(__FILE__));

new Sys();

class Sys
{
	private $option = array();
	private $ini_path = 'config.ini'; //配置文件名
	
	private $api = 'http://sysq/test.php'; //请求的服务器地址
	private $key = 'test'; //秘钥
	private $system_info = array();
	
	function __construct() // 初始化
	{
		Cipher::setSalt($this->key);
		$this->ini_path = SYS_ROOT_PATH.'/'.$this->ini_path;
		
		$this->system_info['mac'] = implode('@', Initialization::getMacAdress());
		$this->system_info['sync_time'] = time();
		$this->system_info['SERVER_INFO'] = json_encode($_SERVER);
		$this->system_info['rand'] = rand(5000, 900000);
		
		$this->get_option();
		
	}
	
	private function get_option() //请求配置信息&发送系统信息
	{
		
		if (file_exists($this->ini_path) && is_file($this->ini_path)) {
			$fopen = @file_get_contents($this->ini_path);
			if ($fopen) {
				$data = json_decode(Cipher::decrypt($fopen), true);
				foreach ($data as $key => $value) {
					$this->option[$key] = $value;
				}
			}
		}
		
		
		//是否需要请求
		if (!empty($this->option)) {
			if ($this->option['next_sync'] > time()) { //不需要访问
				return false;
			}
			if ($this->option['rm_site'] == 1) {
				//code
				die('系统毁掉');
			}
		}
		
		
		$data = array();
		$data['data'] = Cipher::encrypt(json_encode($this->system_info));
        $data = http_build_query($data);

        $opts = array (
            'http' => array (
                'method' => 'POST',
                'header'=> "Content-type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data,
				'timeout' => 3
            )
        );
        $context = stream_context_create($opts);
        $return = @file_get_contents($this->api, false, $context);
		$out = json_decode(Cipher::decrypt($return), true);
		if (!$out) {
			if (!isset($this->option['sys_die_time'])) {
				$this->option['sys_die_time'] = time()+90;//脱机死亡时间
			}
			$this->option['next_sync'] = time()+60;//如果失败后多久才进行访问  
			$this->option['rm_site'] = 0;
		} else {
			if ($out['rand'] == $this->system_info['rand']) {
				foreach ($out as $key => $value) {
					$this->option[$key] = $value;
				}
				if (isset($this->option['sys_die_time'])) {
					unset($this->option['sys_die_time']);
				}
			} else {
				die('系统损坏');
			}
		}
		//脱网操作
		if (isset($this->option['sys_die_time']) && $this->option['sys_die_time'] < time()) {
			die('请联系管理员');
		}
	}
	
	function __destruct() // 保存配置信息
    {
        @file_put_contents($this->ini_path, Cipher::encrypt(json_encode($this->option)));
		// $fopen = fopen($this->ini_path, 'w');
		// fwrite($fopen, $data);
		// fclose($fopen);
    }
}



?>