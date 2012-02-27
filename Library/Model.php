<?php


// helper function
function _model($name = null) {
    static $models = array();

    if (!isset($name)) {
        $name = '__null__';
    }

    if (!isset($models[$name])) {
        $file = Model::$dir."/$name.php";
        $t = explode('/', $name);
        $table_name = end($t);

        if (file_exists($file)) {
            require_once $file;
            $class = $table_name.'_model';
        } else {
            $class = 'Model';
        }
        
        $models[$name] = new $class(Database::$instance);
        
        if ($name !== '__null__') {
            $models[$name]->table = Model::_t($table_name);
        }
    }

    return $models[$name];
}

function _M($name) {
    static $models = array();

    if (!isset($models[$name])) {
        $models[$name] = new resource($name, Database::$instance);
    }

    return $models[$name];
}

class resource
{
    private $model_id = '';
    private $db = '';
    private $tables = array();

    function __construct($model_name, $db)
    {
        $this->db = $db;
        $this->model_name = $model_name;
        $this->tables = array(
            'attribute_value' => Model::_t('attribute_value'),
            'resource_table' => Model::_t('resource'),
            'model' => Model::_t('model'),
            'attribute' => Model::_t('attribute')
        );
        $this->model_id = $this->db->getOne("SELECT id FROM {$this->tables['model']} WHERE name = ?", $model_name);
        $this->att = $this->db->getAll("SELECT * FROM {$this->tables['attribute']} WHERE model_id = ? ORDER BY view_order ASC", $this->model_id);
        foreach ($this->att as $key => $val) {
            if (in_array($val['type'], array('checkbox', 'select', 'model'))) {
                $value = explode("\n",  $val['value']);
                $value = array_map('trim', $value);
                $this->att[$key]['value'] = array_filter($value);
            }
        }
    }

    function getInfo($res_id)
    {
        $info = array();
        $data = $this->db->getAll("SELECT * FROM {$this->tables['attribute_value']} WHERE res_id = ?", $res_id);
        foreach ($this->att as $key => $val) {
            $info[$val['name']] = '';
            foreach ($data as $v) {
                if ($val['id'] == $v['attribute_id']) {
                    $info[$val['name']] = $v['value'];
                }
            }
        }
        return $info;
    }

    function getAttribute()
    {
        return $this->att;
    }

    function getCount($array, $field)
    {
        
    }

    function update($res_id, $array)
    {
        $param = array();
        $resource = _model('resource')->read(array('id'=>$res_id));
        if (!$resource || $resource['model_id'] != $this->model_id) {
            return false;
        }
        foreach ($this->att as $key => $val) {
            if (!isset($array[$val['name']])) {
                continue;
            }
            if ($val['is_unique'] && ($ck = _model('attribute_value')->read(array('attribute_id' => $val['id'], 'value' => $array[$val['name']])))) {
                if ($ck['res_id'] != $res_id) {
                    return array(
                        $val['name'] => $array[$val['name']]
                    );
                }
                if ($array[$val['name']] == $ck['value']) {
                    continue;
                }
            }
            $param[$val['id']] = $array[$val['name']];
        }

        foreach ($param as $key => $val) {
            if (_model('attribute_value')->read(array('res_id'=>$res_id,'attribute_id'=>$key))) {
                _model('attribute_value')->update(array('res_id'=>$res_id,'attribute_id'=>$key), array('value'=>$val));
            } else {
                _model('attribute_value')->create(array(
                    'res_id' => $res_id,
                    'attribute_id' => $key,
                    'value' => $val
                ));
            }
        }

        return count($param);
    }

    function create($array)
    {
        $param = array();
        $param['model_id'] = $this->model_id;
        $param['add_time'] = time();

        if (!$id = _model('resource')->create($param)) {
             return false;
        }

        $param = array();
        foreach ($this->att as $key => $val) {
            if (isset($array[$val['name']])) {
                if ($val['is_unique']) {
                    if (_model('attribute_value')->read(array('attribute_id' => $val['id'], 'value' => $array[$val['name']]))) {
                        return array(
                            $val['name'] => $array[$val['name']]
                        );
                    }
                }
                $param[$val['id']] = $array[$val['name']];
            } else {
                $param[$val['id']] = '';
            }
        }

        foreach ($param as $key => $val) {
            _model('attribute_value')->create(array(
                'res_id' => $id,
                'attribute_id' => $key,
                'value' => $val
            ));
        }

        return $id;
    }

    private function getSql($array, $type = 1)
    {
        $att = $this->get_att(array_keys($array));
        $sql = "SELECT b.* FROM {$this->tables['resource_table']} b LEFT JOIN {$this->tables['attribute_value']} a ON a.res_id = b.id WHERE 0";

        $num = 0;
        foreach ($array as $key => $val) {
            if (!isset($att[$key])) {
                continue;
            }

            if ($att[$key]['type'] == 'input' || $att[$key]['type'] == 'text') {
                $val = addslashes($val);
                if ($type) {
                    $sql .= " OR ( a.attribute_id = ".$att[$key]['id']." AND a.value LIKE '%$val%')";
                } else {
                    $sql .= " OR ( a.attribute_id = ".$att[$key]['id']." AND a.value = '$val')";
                }
            } elseif ($att[$key]['type'] == 'model') {
                $value = explode("\n",  $att[$key]['value']);
                $value = array_map('trim', $value);
                $result = _M($value[0])->getList(array($value[1]=>$val));
                $ids = array();
                foreach ($result as $val) {
                    $ids[] = $val['id'];
                }
                $sql = $sql." OR ( a.attribute_id = ".$att[$key]['id']." AND a.value IN('".implode("', '", $ids)."'))";
            } elseif ($att[$key]['type'] == 'checkbox') {
                $t = array();
                foreach ($val as $v) {
                    $v = addslashes($v);
                    $t[] = "FIND_IN_SET('$v', a.value)";
                }
                $sql .= " OR ( a.attribute_id = ".$att[$key]['id']." AND (".implode(' OR ', $t).") )";
            } else {
                $val = addslashes($val);
                $sql .= " OR ( a.attribute_id = ".$att[$key]['id']." AND a.value = '$val')";
            }
            $num++;
        }
        
        $sql .= " GROUP BY a.res_id HAVING COUNT(*) = $num";

        return $sql;
    }

    function read($array)
    {
        $sql = $this->getSql($array, 0);
        if ($result = $this->db->getRow($sql)) {
            $result = array_merge($result, $this->getInfo($result['id']));
        }

        return $result;
    }

    function genData($array)
    {
        if (isset($array['pager'])) {
            foreach ($array['data'] as $key => $val) {
                $array['data'][$key] = array_merge($val, $this->getInfo($val['id']));
            }
        } else {
            foreach ($array as $key => $val) {
                $array[$key] = array_merge($val, $this->getInfo($val['id']));
            }
        }


        return $array;
    }

    function getList($array = array(), $order = array(), $pager = null)
    {

        if (empty($array)) {
            if ($order && !is_array($order)) {
                $pager = $order;
                $order = 'WHERE model_id = '.$this->model_id;
            } elseif ($order) {
                $order = 'WHERE model_id = '.$this->model_id.' ORDER BY '.key($order).' '.current($order);
            } else {
                $order = 'WHERE model_id = '.$this->model_id;
            }
            if ($pager) {
                return $this->genData(_model('resource')->getList($order, $pager));
            }

            return $this->genData(_model('resource')->getList($order));
        }

        $sql = $this->getSql($array);
        //echo $sql;exit;
        if ($order && !is_array($order)) {
            $pager = $order;
            $order = array();
        }
        if (empty($order)) {
            return $this->genData(_model()->getList($sql, $pager));
        } elseif (key($order) == 'add_time') {
            $sql .= " ORDER BY b.add_time ".current($order);

            return $this->genData(_model()->getList($sql, $pager));
        } else {
            $ids = $this->db->getCol($sql);
            if (empty($ids)) {
                if ($pager) {
                    return array(
                        'data' => array(),
                        'pager' => $pager
                    );
                }
                return array();
            }

            if (!$att = $this->get_att(array_keys($order))) {
                if ($pager) {
                    return array(
                        'data' => array(),
                        'pager' => $pager
                    );
                }
                return array();
            }

            $sql = "SELECT r.* , a.value FROM {$this->tables['resource_table']} r LEFT JOIN {$this->tables['attribute_value']} a ON r.id = a.res_id WHERE a.attribute_id=".$att[key($order)]['id']." AND r.id IN('".implode("', '", $ids)."') ORDER BY (`a`.`value`+0) ".current($order);

            return $this->genData(_model()->getList($sql, $pager));
        }
    }

    function get_att($array)
    {
        static $att = null;
        if ($att === null) {
            $table = Model::_t('attribute');
            $att = $this->db->getAll("SELECT * FROM $table");
        }
        $out = array();
        foreach ($att as $key => $val) {
            if ($val['model_id'] == $this->model_id && in_array($val['name'], $array)) {
                $out[$val['name']] = $val;
            }
        }
        return $out;
    }

}

class Model implements ArrayAccess, Countable
{
    protected $db  = null;
    public static $mc  = null;
    public static $dir = './model';
    public static $dbPrefix = '';

    public $table = '';

    protected function getSql($option)
    {
        $where = array();
        foreach ($option as $key => $val) {
            $where[] = "$key = ?";
        }
        return 'WHERE '.implode(' AND ', $where);
    }

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function read($option)
    {
        $table = $this->table;
        
        if ($table == '') {
            if (!is_string($option)) {
                throw new Exception("no table name, param must be string");
            }
            return $this->db->getRow($option);
        }

        if (is_string($option)) {
            $sql = "SELECT * FROM `$table` $option";

            return $this->db->getRow($sql);
        }

        $sql = "SELECT * FROM `$table` ".$this->getSql($option);

        return $this->db->getRow($sql, array_values($option));
    }

    public function create($option)
    {
        $table = $this->table;

        if ($table == '') {
            if (!is_string($option)) {
                throw new Exception("no table name, param must be string");
            }

            $result = $this->db->exec($option);
            $id = $this->db->lastInsertId();

            return $id;
        }

        $sql = "INSERT INTO `$table` (".implode(', ', array_keys($option)).') VALUES ('.implode(', ', array_fill(0, count($option), '?')) . ')';

        $result = $this->db->exec($sql, array_values($option));
        $id = $this->db->lastInsertId();

        return $id;
    }

    public function getDataBase()
    {
        return $this->db;
    }

    public function update($option, $array = array())
    {
        $table = $this->table;

        if ($table == '') {
            if (!is_string($option)) {
                throw new Exception("no table name, param must be string");
            }
            return $this->db->exec($option);
        }

        if (is_string($option)) {
            $sql = "UPDATE `$table` $option";

            return $this->db->exec($sql);
        }
    
        $set = array();
        foreach ($array as $key => $val) {
            $set[] = "$key = ?";
        }

        $param = array_merge(array_values($array), array_values($option));

        $sql = "UPDATE `$table` SET ".implode(',', $set).' '.$this->getSql($option);

        return $this->db->exec($sql, $param);
    }

    public function delete($option)
    {
        $table = $this->table;

        if ($table == '') {
            if (!is_string($option)) {
                throw new Exception("no table name, param must be string");
            }
            return $this->db->exec($option);
        }

        if (is_string($option)) {
            $sql = "DELETE FROM `$table` $option";
            

            return $this->db->exec($sql);
        }

        $sql = "DELETE FROM `$table` ".$this->getSql($option);

        return $this->db->exec($sql, array_values($option));
    }

    public function getList($option  = '', $sqladd = '', $pager = null)
    {
        $table = $this->table;

        if ($table == '') {
            if (!is_string($option)) {
                throw new Exception("no table name, param must be string");
            }
            if (!$sqladd) {
                return $this->db->getAll($option);
            }
            $pager = $sqladd;
            $limit = $pager->setPage()->getLimit();
            
            $option = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $option)." $limit";

            $data = $this->db->getAll($option);
            $count = $this->db->getOne("SELECT FOUND_ROWS()");
            $pager->generate($count);
            return array(
                'data' => $data,
                'pager' => $pager
            );
        }

        if (is_string($option)) {
            $pager = $sqladd;
            if ($pager) {
                $limit = $pager->setPage()->getLimit();
                $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `$table` $option $limit";
                $data = $this->db->getAll($sql);
                $count = $this->db->getOne("SELECT FOUND_ROWS()");
                $pager->generate($count);
                return array(
                    'data' => $data,
                    'pager' => $pager
                );
            } else {
                $sql = "SELECT * FROM `$table` $option";

                return $this->db->getAll($sql);
            }
        }
    
        if (!isset($pager) && !is_string($sqladd)) {
            $pager = $sqladd;
            $limit = $pager->setPage()->getLimit();
            $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `$table` ".$this->getSql($option)." $limit";
            $data = $this->db->getAll($sql, array_values($option));
            $count = $this->db->getOne("SELECT FOUND_ROWS()");
            $pager->generate($count);
            return array(
                'data' => $data,
                'pager' => $pager
            );
        } elseif (isset($pager)) {
            $limit = $pager->setPage()->getLimit();
            $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `$table` ".$this->getSql($option)." $sqladd $limit";
            $data = $this->db->getAll($sql, array_values($option));
            $count = $this->db->getOne("SELECT FOUND_ROWS()");
            $pager->generate($count);
            return array(
                'data' => $data,
                'pager' => $pager
            );
        } else {
            $sql = "SELECT * FROM `$table` ".$this->getSql($option)." $sqladd";

            return $this->db->getAll($sql, array_values($option));
        }
    }

    public function getCount($option = array())
    {
        $table = $this->table;

        if ($table == '') {
            if (!is_string($option)) {
                throw new Exception("no table name, param must be string");
            }
            return $this->db->getOne($option);
        }

        if (is_string($option)) {
            $sql = "SELECT COUNT(*) FROM `$table` $option";

            return $this->db->getOne($sql);
        }

        if ($option) {
            $sql = "SELECT COUNT(*) FROM `$table` ".$this->getSql($option);

            return $this->db->getOne($sql, array_values($option));
        }
        $sql = "SELECT COUNT(*) FROM `$table`";

        return $this->db->getOne($sql);
    }

    public static function _t($table_name)
    {
        foreach (self::$dbPrefix as $key => $val) {
            if ($val == '*') {
                return $key.$table_name;
            }
            if (in_array($table_name, $val)) {
                return $key.$table_name;
            }
        }
        return $table_name;
    }

    // interface

    public function count()
    {
        return $this->getCount();
    }

    public function offsetGet($key)
    {
        $result = $this->read(array('id'=>$key));

        return $result;
    }

    public function offsetExists($key)
    {
        if ($result = $this->read(array('id'=>$key))) {
            return true;
        }

        return false;
    }

    public function offsetSet($key, $val)
    {
        return $this->update(array('id'=>$key), $val);
    }
    // delete
    public function offsetUnset($key)
    {
        return $this->delete(array('id'=>$key));
    }
}

?>