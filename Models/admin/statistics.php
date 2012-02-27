<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */


class statistics_model extends Model
{

    function get_user_sum($start_time, $end_time, $user_id)
    {
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time)+3600*24;
        
        $att = _M('order_list')->getAttribute();
        $u_a_id = 0; // 操作人
        $j_a_id = 0; // 金额
        $z_a_id = 0; // 状态
        foreach ($att as $key => $val) {
            if ($val['name'] == '订单状态') {
                $z_a_id = $val['id'];
                continue;
            }
            if ($val['name'] == '套系价格') {
                $j_a_id = $val['id'];
                continue;
            }
            if ($val['name'] == '处理人') {
                $u_a_id = $val['id'];
                continue;
            }
        }
        $resource_table = Model::_t('resource');
        $attribute_value_table = Model::_t('attribute_value');
        
        
        
        $sql = "SELECT sum(sum1) FROM (SELECT max(case a.attribute_id when $j_a_id then a.value  end) as sum1 FROM $resource_table r LEFT JOIN $attribute_value_table a ON r.id = a.res_id 
                WHERE ((attribute_id = '$u_a_id' AND value = '$user_id') OR (attribute_id = '$z_a_id' AND (value = '正常' OR value = '救活单')) OR (attribute_id = '$j_a_id' AND value > 0))
                AND (r.add_time > $start_time AND r.add_time < $end_time) 
                GROUP BY a.res_id HAVING count(*) = 3) AS table1 ";
                
        return (int)$this->db->getOne($sql);
    }
    
    function get_user_count($start_time, $end_time, $user_id)
    {
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time)+3600*24;
        $att = _M('order_list')->getAttribute();
        $u_a_id = 0; // 操作人
        $z_a_id = 0; // 状态
        
        foreach ($att as $key => $val) {
            if ($val['name'] == '订单状态') {
                $z_a_id = $val['id'];
                continue;
            }
            if ($val['name'] == '处理人') {
                $u_a_id = $val['id'];
                continue;
            }
        }
        
        $resource_table = Model::_t('resource');
        $attribute_value_table = Model::_t('attribute_value');
        
        $sql = "SELECT COUNT(*) FROM (SELECT COUNT(*) as num FROM $resource_table r LEFT JOIN $attribute_value_table a ON r.id = a.res_id 
                WHERE (attribute_id = '$u_a_id' AND value = '$user_id') OR (attribute_id = '$z_a_id' AND value = '死单') 
                AND (r.add_time > $start_time AND r.add_time < $end_time) 
                GROUP BY a.res_id HAVING num = 2) AS table1";
        $s_num = (int)$this->db->getOne($sql);
        
        $sql = "SELECT COUNT(*) FROM (SELECT COUNT(*) as num FROM $resource_table r LEFT JOIN $attribute_value_table a ON r.id = a.res_id 
                WHERE (attribute_id = '$u_a_id' AND value = '$user_id') OR (attribute_id = '$z_a_id') 
                AND (r.add_time > $start_time AND r.add_time < $end_time) 
                GROUP BY a.res_id HAVING num = 2) AS table1";
        $t_num = (int)$this->db->getOne($sql);
        
        $sql = "SELECT COUNT(*) FROM (SELECT COUNT(*) as num FROM $resource_table r LEFT JOIN $attribute_value_table a ON r.id = a.res_id 
                WHERE (attribute_id = '$u_a_id' AND value = '$user_id') OR (attribute_id = '$z_a_id' AND value = '救活单') 
                AND (r.add_time > $start_time AND r.add_time < $end_time) 
                GROUP BY a.res_id HAVING num = 2) AS table1";
        $j_num = (int)$this->db->getOne($sql);
        
        $sql = "SELECT COUNT(*) FROM (SELECT COUNT(*) as num FROM $resource_table r LEFT JOIN $attribute_value_table a ON r.id = a.res_id 
                WHERE (attribute_id = '$u_a_id' AND value = '$user_id') OR (attribute_id = '$z_a_id' AND value = '未处理') 
                AND (r.add_time > $start_time AND r.add_time < $end_time) 
                GROUP BY a.res_id HAVING num = 2) AS table1";
        $w_num = (int)$this->db->getOne($sql);
        
        return array(
            's_num' => $s_num,
            't_num' => $t_num,
            'j_num' => $j_num,
            'w_num' => $w_num
        );
    }
    // public function __construct($db)
    // {
    //     parent::__construct($db);
    // }

    function get_res_statistics($mid, $time = '')
    {
        $attribute_table = Model::_t('attribute');
        $attribute_value_table = Model::_t('attribute_value');
        $resource_table = Model::_t('resource');
        
        $out_array = array();
        $attribute = $this->db->getAll("SELECT * FROM $attribute_table WHERE model_id=?", $mid);
        $count = $this->db->getOne("SELECT COUNT(*) FROM $resource_table WHERE model_id=?", $mid);
        $out_array['count'] = $count;
        
        foreach ($attribute as $key => $value) {
            if ($value['type'] == 'checkbox') {
                foreach (explode("\n", $value['value']) as $k => $v) {
                    $v = trim($v);
                    $out_array['checkbox'][$value['name']][$v] = $this->get_checkbox_count($value['id'], $v);
                }
            }
            if ($value['type'] == 'select') {
                foreach (explode("\n", $value['value']) as $k => $v) {
                    $v = trim($v);
                    $out_array['select'][$value['name']][$v] = $this->get_select_count($value['id'], $v);
                }
                
            }
        }
        
        return $out_array;
    }
    
    function get_select_count($attribute_id, $value)
    {
        $attribute_value_table = Model::_t('attribute_value');
        return $this->db->getOne("SELECT COUNT(*) FROM $attribute_value_table WHERE attribute_id=$attribute_id AND value='$value'");
    }
    
    function get_checkbox_count($attribute_id, $value)
    {
        $attribute_value_table = Model::_t('attribute_value');
        return $this->db->getOne("SELECT COUNT(*) FROM $attribute_value_table WHERE attribute_id=$attribute_id AND  find_in_set('$value',value)");
    }
    
    
    // function get_attribute_count($attribute_id, $count_a = '',  $time = '')
    // {
    //     $attribute_value_table = Model::_t('attribute_value');
    //     $res_table = Model::_t('resource');
    //     $out_data = array();
    //     $attribute_id = (array)$attribute_id;
    //     $count_a = (array)$count_a;
    //     foreach ($attribute_id as $value) {
    //         $res_count = $this->db->getOne("SELECT COUNT(*) FROM $attribute_value_table WHERE attribute_id=$value");
    //         $out_data[$value]['res_count'] = $res_count;
    //         $out_data[$value]['attribute_id'] = $value;
    //         $out_data[$value]['attribute_name'] = get_info('attribute', $value, 'name');
    //         if (!empty($count_a)) {
    //             
    //         }
    //     }
    // }
}


?>