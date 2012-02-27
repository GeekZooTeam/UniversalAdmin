<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

class menu_model extends Model
{
    private $data = null;

    function get($id)
    {
        $data = $this->getData();
        foreach ($data as $key => $val) {
            if ($val['id'] == $id) {
                return $val;
            }
        }

        return array();
    }

    function getNewId()
    {
        $id = 0;
        $data = $this->getData();
        foreach ($data as $key => $val) {
            if ($val['id'] > $id) {
                $id = $val['id'];
            }
        }

        return $id+1;
    }

    function getParents()
    {
        $out = array();
        $data = $this->getData();
        foreach ($data as $key => $val) {
            if (!$val['parent_id']) {
                $out[] = $val;
            }
        }
        
        return $out;
    }

    function getData()
    {
        if ($this->data === null) {

            $i = 0;
            foreach (Config::get('menu') as $key => $val) {
                $parent_id = ++$i;
                $this->data[$parent_id] = array(
                    'id' => $parent_id,
                    'name' => $key,
                    'view_order' => $parent_id,
                    'url' => '',
                    'parent_id' => 0
                );
                foreach ($val as $k => $v) {
                    $id = ++$i;
                    $this->data[$id] = array(
                        'id' => $id,
                        'name' => $v['name'],
                        'view_order' => $id,
                        'url' => $k,
                        'parent_id' => $parent_id
                    );
                }
            }

            $data = $this->getList('ORDER BY view_order ASC');
            foreach ($data as $key => $val) {
                $this->data[$val['id']] = $val;
            }
            usort($this->data, array('self', 'cmp'));
        }
        $this->data === null && $this->data = array();

        return $this->data;
    }

    function getByParent($parent_id)
    {
        $out = array();
        $data = $this->getData();
        foreach ($data as $key => $val) {
            if ($val['parent_id'] == $parent_id) {
                $out[] = $val;
            }
        }

        return $out;
    }

    function getOption($parent_id = 0)
    {
        $data = $this->getByParent($parent_id);
        $o = new myRecursiveArrayIterator($data);

        return new RecursiveIteratorIterator($o, RecursiveIteratorIterator::SELF_FIRST);
    }

    static function cmp($a, $b)
    {
        if ($a['view_order'] == $b['view_order']) {
            return 0;
        }
        
        return ($a['view_order'] < $b['view_order']) ? -1 : 1;
    }
    
}

class myRecursiveArrayIterator extends RecursiveArrayIterator
{
    function hasChildren()
    {
        $c = $this->current();
        $re = _model('menu')->getByParent($c['id']);
        return !empty($re);
    }

    function getChildren()
    {
        $c = $this->current();
        return new myRecursiveArrayIterator(_model('menu')->getByParent($c['id']));
    }
}

?>