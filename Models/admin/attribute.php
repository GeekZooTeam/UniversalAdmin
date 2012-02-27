<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author quqiang <quqiang@geek-zoo.com>
 *
 */


class attribute_model extends Model
{
    function get_attribute_value($res_id)
    {
        if (!$res_id) {
            return array();
        }
        $sql = "SELECT B.value,A.name FROM admin_attribute_value B LEFT JOIN admin_attribute A ON B.attribute_id = A.id WHERE res_id = $res_id";
        // $sql = "SELECT B.value FROM attribute as A INNER JOIN attribute_value as B ON (A.id=B.attribute_id AND A.model_id=$model_id AND A.name='$attribute_name' AND B.res_id = $res_id)";
        $result = $this->db->getAll($sql);
        $out = array();
        foreach ($result as $key => $value) {
            $out[$value['name']] = $value['value'];
        }
        return $out;
    }

    function check_unique($attribute_id, $content)
    {
        $attribute_table = Model::_t('attribute');
        $attribute_value_table = Model::_t('attribute_value');
        
        $attribute = $this->db->getRow("SELECT * FROM $attribute_table WHERE id=$attribute_id");
        if ($attribute['is_unique'] == '0' || empty($content)) {
           return false;
        }
        $check = $this->db->getRow("SELECT * FROM $attribute_value_table WHERE attribute_id=$attribute_id  AND  value='$content'");
        if ($check) {
            return $check;
        } else {
            return false;
        }
    }
}


?>