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

    public function get_media_by_name($name){
        $result = $this->db->get_where('media', ['media_name' => $name])->result_array();
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function add_media($data){
        $this->db->insert('media', $data);
    }

    public function del_media($id){
        return $this->db->delete('media', array('id' => $id));
    }

    public function update_media_avatar($media_name, $avatar_name){
        $this->db->where('media_name', $media_name);
        $this->db->set('media_avatar', $avatar_name);
        $this->db->update('media');
    }

}