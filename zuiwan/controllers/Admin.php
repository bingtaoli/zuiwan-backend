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

    public function add_article(){
        if (METHOD == 'post'){
            /**
             * TODO 只有root账户可以访问
             */
            $post_data = $this->input->post();
            $article_type = $post_data['article_type'];
            $article_content = $post_data['article_content'];
            $article_author = $post_data['article_author'];
            $article_source = $post_data['article_source'];
            $article_intro = $post_data['article_intro'];
            //时间格式 December 19, 2015
            //$create_time = date('Y-m-d H:m:s');
            $create_time = date('F d, Y'); # December 09, 2015
            $insert_data =[
                'article_type'    => $article_type,
                'article_content' => $article_content,
                'article_author'  => $article_author,
                'article_source'  => $article_source,
                'create_time'     => $create_time,
                'article_intro'   => $article_intro,
            ];
            try {
                global $config;
                if(is_uploaded_file($_FILES['article_img']['tmp_name'])) {
                    $file_name = $_FILES['article_img']['name'];
                    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
                    $store_file_name = uniqid() . "." . $file_type;
                    $file_abs = $config["img_dir"] . "/" . $store_file_name;
                    $file_host = STATIC_PATH . $file_abs;
                    if (move_uploaded_file($_FILES['article_img']['tmp_name'], $file_host) == false) {
                        throw new Exception("文章大图上传失败");
                    }
                    //上传成功才增加文章
                    $insert_data['article_img'] = $store_file_name;
                }
                $this->article->add_article($insert_data);
            } catch (IdentifyException $e){
                $error_id = 1;
            } catch (Exception $e){
                $error_id = 2;
            }
            $url = isset($error_id) ? "/admin/index" : "/admin/index/" . $error_id;
            redirect($url);
        }
    }

    public function edit_article($id){
        try {
            $article = $this->article->get_article_by_id($id);
            $data = [
                'article' => $article,
            ];
            $this->load->view('admin_article_edit', $data);
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function del_article(){
        if (METHOD == 'post') {
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $post_data = $this->input->post();
                $article_id = $post_data['id'];
                $this->article->del_article($article_id);
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}
