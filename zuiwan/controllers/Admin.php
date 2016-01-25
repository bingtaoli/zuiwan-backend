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
    public function __construct()
    {
        parent::__construct();
    }

    public function index($error_id=null){
        $articles = $this->article->get_articles();
        $media = $this->media->select_all('id, media_name, media_intro, media_avatar');
        $topic = $this->topic->select_all('id, topic_name, topic_intro, topic_img');
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

    //总访问量页面的信息
    public function get_website_information(){
        $article_count = $this->article->get_count();
        $user_count = $this->user->get_count();
        
        $information['article_count'] = $article_count;
        $information['user_count'] = $user_count;
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($information));
    }

}
