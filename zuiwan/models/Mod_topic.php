<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/21
 * Time: 下午12:26
 */
class Mod_topic extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    private function _add_prefix(&$result){
        add_img_prefix($result, 'topic_img');
        add_img_prefix($result, 'topic_detail_back');
    }

    public function update($data){
        try {
            $this->db->where('id', $data['id']);
            $this->db->update('topic', $data);
        } catch (Exception $e){
            throw new Exception($e);
        }
    }

    public function select_all($select, $add_prefix=1){
        $this->db->select($select);
        $result = $this->db->get('topic')->result_array();
        if ($add_prefix == 1){
            $this->_add_prefix($result);
        }
        return $result;
    }

    public function select_by_id($id, $select='*', $add_prefix=1){
        $this->db->select($select);
        $result = $this->db->get_where('topic', ['id' => $id])->result_array();
        if ($add_prefix == 1){
            $this->_add_prefix($result);
        }
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function add_topic($data){
        $this->db->insert('topic', $data);
        return $this->db->insert_id();
    }

    public function del_topic($id){
        return $this->db->delete('topic', array('id' => $id));
    }

    public function get_columns(){
        $result = $this->db->query('SHOW FULL COLUMNS FROM topic')->result_array();
        return $result;
    }

}