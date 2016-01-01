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

    public function get_all_article(){
        $result = $this->db->get('article')->result_array();
        //把article_img设置成绝对路径
        $img_prefix = HOST . DIR_IN_ROOT .  "/public/upload/img/";
        foreach($result as &$r){
            if (isset($r['article_img'])){
                $r['article_img'] = $img_prefix . $r['article_img'];
            }
        }
        return $result;
    }

    public function get_article_by_id($id){
        $result = $this->db->get_where('article', ['id' => $id])->result_array();
        //把article_img设置成绝对路径
        $img_prefix = HOST . DIR_IN_ROOT .  "/public/upload/img/";
        foreach($result as &$r){
            if (isset($r['article_img'])){
                $r['article_img'] = $img_prefix . $r['article_img'];
            }
        }
        if ($result){
            return $result[0];
        } else{
            throw new Exception("该文章已不存在");
        }
    }

}