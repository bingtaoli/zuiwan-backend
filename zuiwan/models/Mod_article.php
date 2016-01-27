<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 14:55
 */

class Mod_article extends CI_Model
{
    /**
     *  Model层API的顺序都为增删改查,方便管理
     */

    public function __construct()
    {
        parent::__construct();
    }

    private function _add_prefix(&$result){
        add_img_prefix($result, 'article_img');
    }

    public function add_article($data){
        try {
            $this->db->insert('article', $data);
        } catch(Exception $e){
            throw new Exception($e);
        }
        return $this->db->insert_id();
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

    public function select_all($select){
        $this->db->select($select);
        $result = $this->db->get('article')->result_array();
        return $result;
    }

    public function select_by_id($select, $id, $add_prefix=1){
        $this->db->select($select);
        $result = $this->db->get_where('article', ['id' => $id])->result_array();
        if ($add_prefix == 1){
            $this->_add_prefix($result);
        }
        if ($result){
            return $result[0];
        } else{
            return null;
        }
    }

    public function get_articles($type=null, $id=null){
        $this->db->select('id, article_title, article_intro, article_author, article_media, article_media_name,
                          article_topic, article_topic_name, create_time, article_img, is_recommend');
        if ($type){
            if ($type == 1){
                $result = $this->db->get_where('article', ['article_media' => $id])->result_array();
            } else if ($type == 2){
                $result = $this->db->get_where('article', ['article_topic' => $id])->result_array();
            }
        } else {
            $result = $this->db->get('article')->result_array();
        }
        $this->_add_prefix($result);
        return $result;
    }

    public function get_top_articles($number){
        $select = 'id, visit_count, article_title';
        $this->db->select($select);
        $this->db->order_by('visit_count', 'DESC');
        $this->db->limit($number, 0);
        $result = $this->db->get('article')->result_array();
        return $result;
    }

    public function get_count(){
        return $this->db->count_all_results('article');
    }

    public function get_recommended_articles(){
        $this->db->select('id, article_title, article_media_name, article_topic_name, article_img, article_color');
        $result = $this->db->get_where('article', ['is_recommend' => 1])->result_array();
        $this->_add_prefix($result);
        return $result;
    }

    public function get_banner_articles(){
        $this->db->select('id, article_title, article_intro, article_media_name, article_topic_name, article_img');
        $result = $this->db->get_where('article', ['is_banner' => 1])->result_array();
        $this->_add_prefix($result);
        return $result;
    }

    public function get_page_articles($index, $numberPerPage, $condition){
        if ($condition){
            $this->db->where($condition);
        }
        $this->db->select('id, article_title, article_intro, article_author, article_media, article_media_name,
                          article_topic, article_topic_name, create_time, article_img, is_recommend');
        $count = $this->db->count_all_results('article');
        //上一次的限制查询过后就没有了 todo 优化,如果不用limit实现分页则少一次查询
        if ($condition){
            $this->db->where($condition);
        }
        $this->db->limit($numberPerPage, $index*$numberPerPage);
        $result = $this->db->get('article')->result_array();
        $this->_add_prefix($result);
        return [$count, $result];
    }

    public function get_by_topic($id){
        $this->db->select('id, article_title, article_intro, article_media_name, article_topic_name, article_img, create_time');
        $result = $this->db->get_where('article', ['article_topic' => $id])->result_array();
        $this->_add_prefix($result);
        return $result;
    }

    public function get_by_media($id){
        $this->db->select('id, article_title, article_media_name, article_topic_name, article_img, article_color');
        $result = $this->db->get_where('article', ['article_media' => $id])->result_array();
        $this->_add_prefix($result);
        return $result;
    }

    public function get_count_by_topic($topic_id){
        $this->db->where('article_topic', $topic_id);
        return $this->db->count_all_results('article');
    }

    public function get_columns(){
        $result = $this->db->query('SHOW FULL COLUMNS FROM article')->result_array();
        return $result;
    }

}