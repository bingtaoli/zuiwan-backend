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
        try {
            $this->db->insert('article', $data);
            //$sql = $this->db->
        } catch(Exception $e){
            throw new Exception($e);
        }
    }

    public function del_article($id){
        $this->db->where('id', $id);
        $this->db->delete('article');
    }

    public function update_article($data){
        try {
            $this->db->where('id', $data['id']);
            $this->db->update('article', $data);
        } catch (Exception $e){
            throw new Exception($e);
        }
    }

    public function get_articles($type=null, $name=null){
        if ($type){
            if ($type == 1){
                $result = $this->db->get_where('article', ['article_media' => $name])->result_array();
            } else if ($type == 2){
                $result = $this->db->get_where('article', ['article_type' => $name])->result_array();
            }
        } else {
            $result = $this->db->get('article')->result_array();
        }
        add_img_prefix($result);
        //此时不把article_content返给前端
        foreach($result as &$article){
            if (isset($article['article_content'])){
                unset($article['article_content']);
            }
        }
        return $result;
    }

    public function get_article_by_id($id){
        $result = $this->db->get_where('article', ['id' => $id])->result_array();
        add_img_prefix($result);
        if ($result){
            return $result[0];
        } else{
            throw new Exception("该文章已不存在");
        }
    }
}