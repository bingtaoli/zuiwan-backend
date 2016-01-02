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

    public function get_all_topic(){
        $result = $this->db->get('topic')->result_array();
        add_img_prefix($result, 'topic_img');
        return $result;
    }

    public function get_topic_by_name($name){
        $result = $this->db->get_where('topic', ['topic_name' => $name])->result_array();
        add_img_prefix($result, 'topic_img');
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function add_topic($data){
        $this->db->insert('topic', $data);
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