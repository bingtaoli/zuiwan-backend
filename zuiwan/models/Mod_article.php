<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 14:55
 */

class Mod_article extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_article($data){
        $this->db->insert('article', $data);
    }

    public function del_article($id){
        $this->db->where('id', $id);
        $this->db->delete('article');
    }

    public function get_all_article(){
        $result = $this->db->get('article')->result_array();
        return $result;
    }

    public function get_article_by_id($id){
        $result = $this->db->get_where('article', ['id' => $id])->result_array();
        if ($result){
            return $result[0];
        } else{
            throw new Exception("该文章已不存在");
        }
    }

}