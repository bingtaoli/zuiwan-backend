<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 23:42
 */

class Admin extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        /**
         *  TODO 检测是否是root登陆,否则报错
         */
        $this->load->model('mod_user', 'user');
        $this->user->init($this->db);
        $this->load->model('mod_article', 'article');
        $this->article->init($this->db);
    }

    public function index(){
        $articles = $this->article->get_all_article();
        $data = [
            'articles' => $articles,
        ];
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
            //$article_intro = $post_data['article_intro'];
            $create_time = date('Y-m-d H:m:s');
            $result = [
                'status' => 'success',
                'message' => '',
            ];
            $insert_data =[
                'article_type'    => $article_type,
                'article_content' => $article_content,
                'article_author'  => $article_author,
                'article_source'  => $article_source,
                'create_time'     => $create_time,
                //'article_intro'   => $article_intro,
            ];
            try {
                $this->article->add_article($insert_data);
            } catch (IdentifyException $e){
                if ($e->getCode() == 0){
                    $result['message'] = '无权限增加文章';
                    $result['status'] = 'error';
                }
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
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
}
