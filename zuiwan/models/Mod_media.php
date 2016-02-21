<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/18
 * Time: 下午4:34
 */
class Mod_media extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    private function _add_prefix(&$result){
        add_img_prefix($result, 'media_avatar');
        add_img_prefix($result, 'media_detail_back');
    }

    public function update($data){
        try {
            $this->db->where('id', $data['id']);
            $this->db->update('media', $data);
        } catch (Exception $e){
            throw new Exception($e);
        }
    }

    public function select_all($select){
        $this->db->select($select);
        $result = $this->db->get('media')->result_array();
        $this->_add_prefix($result);
        return $result;
    }

    public function select_by_id($id, $select='*', $add_prefix=1){
        $this->db->select($select);
        $result = $this->db->get_where('media', ['id' => $id])->result_array();
        if ($add_prefix == 1){
            $this->_add_prefix($result);
        }
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function select_by_name($name, $select='*', $add_prefix=1){
        $this->db->select($select);
        $result = $this->db->get_where('media', ['media_name' => $name])->result_array();
        if ($add_prefix == 1){
            $this->_add_prefix($result);
        }
        if ($result){
            return $result[0];
        }
        return null;
    }

    public function add_media($data){
        $this->db->insert('media', $data);
        return $this->db->insert_id();
    }

    public function del_media($id){
        return $this->db->delete('media', array('id' => $id));
    }

    public function update_avatar($media_id, $avatar_name){
        $this->db->where('id', $media_id);
        $this->db->set('media_avatar', $avatar_name);
        $this->db->update('media');
    }

    public function get_media_fans($media_id){
        $this->db->select('id, collect_media');
        $result = $this->db->get('user')->result_array();
        $num = 0;
        foreach($result as $r){
            if ($r['collect_media']){
                $arr = json_decode($r['collect_media'], true);
                if (in_array($media_id, $arr)){
                    $num++;
                }
            }
        }
        return $num;
    }

}