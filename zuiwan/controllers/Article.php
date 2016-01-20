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
    var $memcached;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        if (MEMCACHED) {
            $this->memcached = new Memcached();
            $this->memcached->addServer('localhost', 11211);
        }
    }

    /**
     * @throws
     * 传media则获取某个media内容,否则获取所有内容
     */
    public function get_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $type = isset($get_data['type']) ? $get_data['type'] : null;
            $id = isset($get_data['id']) ? $get_data['id'] : null;
            #memcached
            if (MEMCACHED){
                if ($type && $id){
                    if ($type == 1){
                        //media
                        $articles = $this->memcached->get("articles-media-$id");
                    } else if ($type == 2){
                        $articles = $this->memcached->get("articles-topic-$id");
                    } else {
                        throw new Exception("未知类型文章,无法获取数据");
                    }
                } else {
                    $articles = $this->memcached->get("articles");
                }
            }
            if (!isset($articles) || $articles == null){
                if ($type && $id ){
                    if ($type == 1){
                        $articles = $this->article->get_articles(1, $id);
                        if (MEMCACHED){
                            $this->memcached->set("articles-media-$id", $articles);
                        }
                    } else if ($type == 2){
                        $articles = $this->article->get_articles(2, $id);
                        if (MEMCACHED){
                            $this->memcached->set("articles-topic-$id", $articles);
                        }
                    } else {
                        throw new Exception("未知类型文章,无法获取数据");
                    }
                } else {
                    $articles = $this->article->get_articles();
                    if (MEMCACHED){
                        $this->memcached->set("articles", $articles);
                    }
                }
            }
            header("Access-Control-Allow-Origin: *");
            $result = $articles;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
            if (MEMCACHED){
                $this->memcached->quit();
            }
        }
    }

    //后台管理分页
    public function get_page_article(){
        $get_data = $this->input->get();
        $numberPerPage = $get_data['numberPerPage'];
        $index = $get_data['index'];
        if ($numberPerPage && isset($index)){
            $result = $this->article->get_page_articles($index, $numberPerPage);
        } else {
            $result = [];
            $result['error'] = "必须设定正确的索引和每页数目";
        }
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function get_one_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $id = $get_data['id'];
            $article = $this->article->get_by_id($id);
            header("Access-Control-Allow-Origin: *");
            $result = $article;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function test_get_article(){
        $this->get_article(1, "思存");
    }

    //1. 添加文章
    //2. 更改文章
    public function add_article(){
        if (METHOD == 'post'){
            /**
             * TODO 只有root账户可以访问
             */
            $result['status'] = 'success';
            $result['message'] = '';
            $post_data = $this->input->post();
            $article_title = $post_data['article_title'];
            $article_topic = $post_data['article_topic'];
            $article_content = $post_data['article_content'];
            $article_author = $post_data['article_author'];
            $article_media = $post_data['article_media'];
            $article_intro = $post_data['article_intro'];
            $isUpdate = isset($post_data['is_update']) ? $post_data['is_update'] : null;
            //时间格式 December 19, 2015
            $create_time = date('F d, Y'); # December 09, 2015
            //去除img包含的p标签
            $reg = "/<p>(<img.+?)<\/p>/";
            $replacement = '$1';
            $article_content = preg_replace($reg, $replacement, $article_content);
            $data =[
                'article_title'   => $article_title,
                'article_topic'    => $article_topic,
                'article_content' => $article_content,
                'article_author'  => $article_author,
                'article_media'   =>  $article_media,
                'create_time'     => $create_time,
                'article_intro'   => $article_intro,
            ];
            try {
                //获取topic name && media name
                $this->load->model('mod_topic', 'topic');
                $this->load->model('mod_media', 'media');
                $media = $this->media->get_by_id($article_media);
                $topic = $this->topic->get_by_id($article_topic);
                $data['article_media_name'] = $media['media_name'];
                $data['article_topic_name'] = $topic['topic_name'];
                if (!$isUpdate){
                    //存文章大图
                    if(is_uploaded_file($_FILES['file']['tmp_name'])) {
                        $file_name = $_FILES['file']['name'];
                        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
                        $store_file_name = uniqid() . "." . $file_type;
                        $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
                        $file_host = STATIC_PATH . $file_abs;
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_host) == false) {
                            throw new Exception("文章大图上传失败");
                        }
                        $article_img = $store_file_name;
                        $data['article_img'] = $article_img;
                        $this->article->add_article($data);
                    }
                } else {
                    //更新文章内容
                    $data['id'] = $post_data['id'];
                    $this->article->update_article($data);
                }
                if (MEMCACHED){
                    @$this->memcached->delete("articles");
                }
            } catch (IdentifyException $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            } catch (Exception $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function update_article_img(){
        if (METHOD == 'post'){
            $post = $this->input->post();
            $id = $post['id'];
            $result['status'] = 'success';
            $result['message'] = '';
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
                    //更新数据库
                    $article = $this->article->get_by_id($id);
                    $originImg = $article['article_img'];
                    $article['article_img'] = $store_file_name;
                    $this->article->update_article($article);
                    //如果有old img,则删除old img
                    if ($originImg && $originImg != 'default_article_img.jpg' ){
                        @unlink(STATIC_PATH . $originImg);
                    }
                    if (MEMCACHED){
                        @$this->memcached->delete("articles");
                    }
                }
            } catch(Exception $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
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
                if (MEMCACHED){
                    @$this->memcached->delete("articles");
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}