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

    public function get_all_topic(){
        $this->db->select('id, topic_name, topic_intro, topic_img');
        $result = $this->db->get('topic')->result_array();
        $this->_add_prefix($result);
        return $result;
    }

    public function select_by_id($select, $id){
        $this->db->select($select);
        $result = $this->db->get_where('topic', ['id' => $id])->result_array();
        $this->_add_prefix($result);
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function get_by_id($id){
        $this->db->select('topic_name, topic_intro, topic_img');
        $result = $this->db->get_where('topic', ['id' => $id])->result_array();
        $this->_add_prefix($result);
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function get_topic_by_name($name){
        $result = $this->db->get_where('topic', ['topic_name' => $name])->result_array();
        $this->_add_prefix($result);
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

    public function update_topic_img($topic_name, $img_name){
        $this->db->where('topic_name', $topic_name);
        $this->db->set('topic_img', $img_name);
        $this->db->update('topic');
    }
}