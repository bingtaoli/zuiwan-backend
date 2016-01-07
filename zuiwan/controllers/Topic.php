<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/21
 * Time: 下午12:26
 * 专题 Type
 */
class Topic extends MY_Controller
{
    var $input;
    var $article;
    var $type;
    var $output;
    var $config;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        $this->load->model('mod_topic', 'topic');
    }

    public function get_topic(){
        $topic = $this->topic->get_all_topic();
        //获取每个专题文章总数
        foreach($topic as &$t){
            $t['article_count'] = $this->article->get_count_by_topic($t['id']);
        }
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($topic));
    }

    public function get_one_topic(){
        if (METHOD == 'get') {
            $get_data = $this->input->get();
            $id = $get_data['id'];
            $topic = $this->topic->get_by_id($id);
            //设置文章count
            $topic['article_count'] = $this->article->get_count_by_topic($topic['id']);
            header("Access-Control-Allow-Origin: *");
            $result = $topic;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function set_topic_img(){
        if (METHOD == 'post'){
            $result['status'] = 'success';
            $result['message'] = '';
            if(is_uploaded_file($_FILES['avatar']['tmp_name']))
            {
                $post_data = $this->input->post();
                $topic_name = $post_data['topic_name'];

                //判断上传文件是否允许
                $file_arr = pathinfo($_FILES['avatar']['name']);
                $file_type = $file_arr["extension"];
                $random_file_name = uniqid() . "." . $file_type;
                $file_abs = $this->config->config["img_dir"] . "/" . $random_file_name;
                $file_host = STATIC_PATH . $file_abs;
                try {
                    if(move_uploaded_file($_FILES['avatar']['tmp_name'], $file_host))
                    {
                        //把以前的图片删除
                        $topic = $this->topic->get_topic_by_name($topic_name);
                        $origin = $topic['topic_img'];
                        $origin_pos = STATIC_PATH . $this->config->config['img_dir'] . "/" . $origin;
                        if (file_exists($origin_pos)){
                            unlink($origin_pos);
                        }

                        //把topic的topic_avatar更新
                        $this->topic->update_topic_img($topic_name, $random_file_name);
                        $result['data'] = $random_file_name;
                    }
                } catch (Exception $e){
                    $result['status'] = 'error';
                    $result['message'] = $e->getMessage();
                }
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function add_topic(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $data = [
                'topic_name'   => $post_data['topic_name'],
                'topic_intro'   => $post_data['topic_intro'],
                'topic_img'    => "default_topic_img.jpg",
            ];
            $result = [
                'status' => 'success',
                'message' => '',
                'data'    => '',
            ];
            try {
                $id = $this->topic->add_topic($data);
                $result['data'] = $id;
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function del_topic(){
        if (METHOD == 'post') {
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $post_data = $this->input->post();
                $id = $post_data['id'];
                $this->topic->del_topic($id);
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }
}
