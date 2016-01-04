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

    public function get_articles($type=null, $id=null){
        if ($type){
            if ($type == 1){
                $result = $this->db->get_where('article', ['article_media' => $id])->result_array();
            } else if ($type == 2){
                $result = $this->db->get_where('article', ['article_topic' => $id])->result_array();
            }
        } else {
            $result = $this->db->get('article')->result_array();
        }
        add_img_prefix($result, 'article_img');
        //此时不把article_content返给前端
        foreach($result as &$article){
            if (isset($article['article_content'])){
                unset($article['article_content']);
            }
        }
        return $result;
    }

    public function get_by_id($id){
        $result = $this->db->get_where('article', ['id' => $id])->result_array();
        add_img_prefix($result, 'article_img');
        if ($result){
            return $result[0];
        } else{
            return null;
        }
    }

    public function get_count_by_topic($topic_id){
        $this->db->where('article_topic', $topic_id);
        return $this->db->count_all('article');
    }

}