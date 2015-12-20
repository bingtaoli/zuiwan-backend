<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/18
 * Time: 下午4:34
 */
class Mod_media extends CI_Model {
    var $db;

    public function __construct() {
        parent::__construct();
    }

    public function init($db){
        $this->db = $db;
    }

    public function get_all_media(){
        return $this->db->get('media')->result_array();
    }

    public function add_media($data){
        $this->db->insert('media', $data);
    }

    public function update_media($data){
        $this->db->where('id', $data['id']);
        $this->db->update('media', $data);
    }

    public function del_media($id){
        return $this->db->delete('media', array('id' => $id));
    }

}