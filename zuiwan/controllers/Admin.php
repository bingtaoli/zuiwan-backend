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
        $this->load->model('mod_user', 'user');
        $this->user->init($this->db);
        $this->load->model('mod_article', 'article');
        $this->article->init($this->db);
    }

    public function index($error_id=null){
        $articles = $this->article->get_all_article();
        $data = [
            'articles' => $articles
        ];
        if ($error_id) {
            $data['error_id'] = $error_id;
        }
        $this->load->view('admin_view', $data);
    }
}
