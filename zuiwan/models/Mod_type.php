<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/21
 * Time: 下午12:26
 */
class Mod_type extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }


    public function get_all_type(){
        return $this->db->get('type')->result_array();
    }

    public function get_type_by_name($name){
        $result = $this->db->get_where('type', ['type_name' => $name])->result_array();
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function add_type($data){
        $this->db->insert('type', $data);
    }

    public function del_type($id){
        return $this->db->delete('type', array('id' => $id));
    }

    public function update_type_img($type_name, $img_name){
        $this->db->where('type_name', $type_name);
        $this->db->set('type_img', $img_name);
        $this->db->update('type');
    }
}