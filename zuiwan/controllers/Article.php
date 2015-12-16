<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 14:55
 */

class Article extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        $this->article->init($this->db);
    }

    /**
     * @param null $media
     * 传media则获取某个media内容,否则获取所有内容
     */
    public function get_article($media = null){
        $articles = $media ? $this->article->get_article_by_media() :  $this->article->get_all_article();
        $img_prefix = "http://202.114.20.78/" . DIR_IN_ROOT .  "/public/article_img/";
        foreach ($articles as $a){
            if (isset($a['article_img'])){
                $a['article_img'] = $img_prefix . $a['article_img'];
            }
        }
        $result = [];
        $result['articles'] = $articles;
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }
}