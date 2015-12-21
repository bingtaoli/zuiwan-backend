<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 14:55
 */

class Article extends MY_Controller
{

    var $article;
    var $config;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        $this->article->init($this->db);
    }

    /**
     * @param null $type
     * @throws
     * 传media则获取某个media内容,否则获取所有内容
     */
    public function get_article($type=null){
        if ($type){
            if ($type == 1){
                $articles = $this->article->get_article_by_media();
            } else if ($type == 2){
                $articles = $this->article->get_article_by_type();
            } else {
                throw new Exception("未知类型文章,无法获取数据");
            }
        } else {
            $articles = $this->article->get_all_article();
        }
        $img_prefix = "http://202.114.20.78/" . DIR_IN_ROOT .  "/public/article_img/";
        foreach ($articles as $a){
            if (isset($a['article_img'])){
                $a['article_img'] = $img_prefix . $a['article_img'];
            }
        }
        $result = $articles;
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function add_article(){
        if (METHOD == 'post'){
            /**
             * TODO 只有root账户可以访问
             */
            $post_data = $this->input->post();
            $article_title = $post_data['article_title'];
            $article_type = $post_data['article_type'];
            $article_content = $post_data['article_content'];
            $article_author = $post_data['article_author'];
            $article_media = $post_data['article_media'];
            $article_intro = $post_data['article_intro'];
            //时间格式 December 19, 2015
            //$create_time = date('Y-m-d H:m:s');
            $create_time = date('F d, Y'); # December 09, 2015
            $insert_data =[
                'article_title'    => $article_title,
                'article_type'    => $article_type,
                'article_content' => $article_content,
                'article_author'  => $article_author,
                'article_media'  =>  $article_media,
                'create_time'     => $create_time,
                'article_intro'   => $article_intro,
            ];
            try {
                if(is_uploaded_file($_FILES['article_img']['tmp_name'])) {
                    $file_name = $_FILES['article_img']['name'];
                    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
                    $store_file_name = uniqid() . "." . $file_type;
                    $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
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