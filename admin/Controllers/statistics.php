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
				$time = _GET('t', 0);//1为周 2为月 3为年
				if (!in_array($time, array('0','1','2'))) {
					die('0');
				}
				if ($time == 0) {
					$tody = date('N');
					$time_start = date('Y-m-d', strtotime("-$tody day"));
					$time_end = date('Y-m-d');
					
					
					$last_1_start = date('Y-m-d', strtotime("-".($tody+6)." day"));
					$last_1_end = date('Y-m-d', strtotime("-$tody day"));

					$last_2_start = date('Y-m-d', strtotime("-".($tody+6+7)." day"));
					$last_2_end = date('Y-m-d', strtotime("-".($tody+6+1)." day"));
				}
				
				if ($time == 1) {
					$time_start = date('Y-m-1');;
					$time_end = date('Y-m-d');
					
					$last_1_start = date('Y').'-'.(date('m')-1).'-1';
					$last_1_end = date('Y').'-'.(date('m')-1).'-'.date('t', strtotime(date('Y').'-'.(date('m')-1)));

					$last_2_start = date('Y').'-'.(date('m')-2).'-1';
					$last_2_end = date('Y').'-'.(date('m')-2).'-'.date('t', strtotime(date('Y').'-'.(date('m')-2)));
				}
				
				if ($time == 2) {
					$time_start = date('Y-1-1');
					$time_end = date('Y-m-d');
					
					$last_1_start = (date('Y')-1).'-1-1';
					$last_1_end = (date('Y')-1).'-12-31';

					$last_2_start = (date('Y')-2).'-1-1';
					$last_2_end = (date('Y')-2).'-12-31';
				}
				
				$last1 = array();//上1
				$last2 = array();//上2
				
				$user_group = _M('user_group')->getList();
				$user = _M('user')->getList();
				foreach ($user_group as $key => $value) {
					$user_group[$key]['coin_count'] = 0;
					$user_group[$key]['s_num'] = 0;
					$user_group[$key]['t_num'] = 0;
					$user_group[$key]['j_num'] = 0;
					$user_group[$key]['w_num'] = 0;
					$user_group[$key]['user'] = array();
					foreach ($user as $k=>$v) {
						$user[$k]['odd_conut'] = _model('statistics')->get_user_count($time_start, $time_end, $v['id']);//单数
						$user[$k]['coin_count'] = (int)_model('statistics')->get_user_sum($time_start, $time_end, $v['id']);//销售额
						
						
						$last1['odd_conut'][] = _model('statistics')->get_user_count($last_1_start, $last_1_end, $v['id']);//单数
						$last2['odd_conut'][] = _model('statistics')->get_user_count($last_2_start, $last_2_end, $v['id']);//单数
						
						$last1['coin_count'][] = (int)_model('statistics')->get_user_sum($last_1_start, $last_1_end, $v['id']);//销售额
						$last2['coin_count'][] = (int)_model('statistics')->get_user_sum($last_2_start, $last_2_end, $v['id']);//销售额
						
						
						if ($v['销售组'] == $value['id']) {
							$user_group[$key]['coin_count'] += $user[$k]['coin_count'];
							$user_group[$key]['s_num'] += $user[$k]['odd_conut']['s_num'];
							$user_group[$key]['t_num'] += $user[$k]['odd_conut']['t_num'];
							$user_group[$key]['j_num'] += $user[$k]['odd_conut']['j_num'];
							$user_group[$key]['w_num'] += $user[$k]['odd_conut']['w_num'];
							$user_group[$key]['user'][] = $user[$k];
						}
					}
				}
				
				
				$out_last1 = array();
				$out_last1['odd_conut']['s_num'] = $out_last1['odd_conut']['t_num'] = $out_last1['odd_conut']['j_num'] = $out_last1['odd_conut']['w_num'] = 0;
				foreach ($last1['odd_conut'] as $key => $value) {
						$out_last1['odd_conut']['s_num'] += $value['s_num'];
						$out_last1['odd_conut']['t_num'] += $value['t_num'];
						$out_last1['odd_conut']['j_num'] += $value['j_num'];
						$out_last1['odd_conut']['w_num'] += $value['w_num'];
				}
				$out_last1['coin_count'] = array_sum($last1['coin_count']);
				
				
				
				$out_last2 = array();
				$out_last2['odd_conut']['s_num'] = $out_last2['odd_conut']['t_num'] = $out_last2['odd_conut']['j_num'] = $out_last2['odd_conut']['w_num'] = 0;
				foreach ($last2['odd_conut'] as $key => $value) {
						$out_last2['odd_conut']['s_num'] += $value['s_num'];
						$out_last2['odd_conut']['t_num'] += $value['t_num'];
						$out_last2['odd_conut']['j_num'] += $value['j_num'];
						$out_last2['odd_conut']['w_num'] += $value['w_num'];
				}
				$out_last2['coin_count'] = array_sum($last2['coin_count']);
				
				
				// print_r($user_group);
				// print_r($out_last1);
				// print_r($out_last2);
        Smarty3::instance()->assign('out_last2', $out_last2);
        Smarty3::instance()->assign('out_last1', $out_last1);
        Smarty3::instance()->assign('user_group', $user_group);
        Smarty3::instance()->display('statistics_list.html');
    }
    
    
    function test()
    {
        _model('statistics')->get_attribute_count(5);
    }
}

?>