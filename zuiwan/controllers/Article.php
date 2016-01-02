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
    }

    /**
     * @throws
     * 传media则获取某个media内容,否则获取所有内容
     */
    public function get_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $type = isset($get_data['type']) ? $get_data['type'] : null;
            if ($type){
                $id = $get_data['id'];
                if ($type == 1){
                    $articles = $this->article->get_articles(1, $id);
                } else if ($type == 2){
                    $articles = $this->article->get_articles(2, $id);
                } else {
                    throw new Exception("未知类型文章,无法获取数据");
                }
            } else {
                $articles = $this->article->get_articles();
            }
            //获取topic name && media name
            $this->load->model('mod_topic', 'topic');
            $this->load->model('mod_media', 'media');
            $topics = $this->topic->get_all_topic();
            $medias = $this->media->get_all_media();
            $topic_id_name = [];
            foreach($topics as $t){
                $topic_id_name[$t['id']] = $t['topic_name'];
            }
            $media_id_name = [];
            foreach($medias as $m){
                $media_id_name[$m['id']] = $m['media_name'];
            }
            foreach($articles as &$a){
                $a['article_topic_name'] = $topic_id_name[$a['article_topic']];
                $a['article_media_name'] = $media_id_name[$a['article_media']];
            }
            header("Access-Control-Allow-Origin: *");
            $result = $articles;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function get_one_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $id = $get_data['id'];
            $article = $this->article->get_by_id($id);
            //获取topic name && media name
            $this->load->model('mod_topic', 'topic');
            $this->load->model('mod_media', 'media');
            $topics = $this->topic->get_all_topic();
            $medias = $this->media->get_all_media();
            foreach($topics as $t){
                if ($article['article_topic'] == $t['id']){
                    $article['article_topic_name'] = $t['topic_name'];
                    break;
                }
            }
            foreach($medias as $m){
                if ($article['article_media'] == $m['id']) {
                    $article['article_media_name'] = $m['media_name'];
                    break;
                }
            }
            header("Access-Control-Allow-Origin: *");
            $result = $article;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function test_get_article(){
        $this->get_article(1, "思存");
    }

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
            $article_img = $post_data['article_img_name'];
            //时间格式 December 19, 2015
            //$create_time = date('Y-m-d H:m:s');
            $create_time = date('F d, Y'); # December 09, 2015
            //去除img包含的p标签
            $reg = "/<p>(<img.+?)<\/p>/";
            $replacement = '$1';
            $article_content = preg_replace($reg, $replacement, $article_content);
            $insert_data =[
                'article_title'   => $article_title,
                'article_topic'    => $article_topic,
                'article_content' => $article_content,
                'article_author'  => $article_author,
                'article_media'   =>  $article_media,
                'create_time'     => $create_time,
                'article_intro'   => $article_intro,
                'article_img'     => $article_img,
            ];
            try {
                $this->article->add_article($insert_data);
            } catch (IdentifyException $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            } catch (Exception $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function set_article_img(){
        if (METHOD == 'post'){
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
                    $result['data'] = $store_file_name;
                }
            } catch(Exception $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function edit_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $id = isset($get_data['id']) ? $get_data['id'] : 0;
            try {
                $article = $this->article->get_by_id($id);
                $data = [
                    'article' => $article,
                ];
                $this->load->view('admin_article_edit', $data);
            } catch (Exception $e){
                echo $e->getMessage();
            }
        } else if (METHOD == 'post'){
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $post_data = $this->input->post();
                $article_content = $post_data['article_content'];
                //去除img包含的p标签
                $reg = "/<p>(<img.+?)<\/p>/";
                $replacement = '$1';
                $post_data['article_content'] = preg_replace($reg, $replacement, $article_content);
                $this->article->update_article($post_data);
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
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
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}