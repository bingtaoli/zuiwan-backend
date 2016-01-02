<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 23:42
 */

$config=array();
$config['img_dir']="/upload/article_img";  // public is included in STATIC_PATH

class Admin extends MY_Controller
{
    var $db;
    var $article;
    var $media;
    var $topic;
    var $user;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_user', 'user');
        $this->load->model('mod_article', 'article');
        $this->load->model('mod_topic', 'topic');
        $this->load->model('mod_media', 'media');
    }

    public function index($error_id=null){
        $articles = $this->article->get_articles();
        $media = $this->media->get_all_media();
        $topic = $this->topic->get_all_topic();
        $data = [
            'articles' => $articles,
            'media'    => $media,
            'topic'     => $topic,
        ];
        if ($error_id) {
            $data['error_id'] = $error_id;
        }
        $this->load->view('admin_view', $data);
    }
}
