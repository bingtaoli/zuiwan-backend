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
    var $config;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_topic(){
        $topic = $this->topic->select_all('id, topic_name, topic_intro, topic_img');
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
            $topic = $this->topic->select_by_id($id, 'topic_name, topic_intro, topic_img');
            if (!empty($topic)){
                //设置文章count
                $topic['article_count'] = $this->article->get_count_by_topic($id);
                $topic['articles'] = $this->article->get_by_topic($id);
            }
            header("Access-Control-Allow-Origin: *");
            $result = $topic;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function admin_get_one_topic(){
        if (METHOD == 'get') {
            $get_data = $this->input->get();
            $id = $get_data['id'];
            $topic = $this->topic->select_by_id($id, 'id, topic_name, topic_intro, topic_img');
            header("Access-Control-Allow-Origin: *");
            $result = $topic;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function update_topic(){
        if (METHOD == 'post') {
            $result['status'] = 'success';
            $post_data = $this->input->post();
            try {
                if (!isset($post_data['id'])) {
                    throw new Exception('id needed');
                }
                if (empty($post_data['topic_name']) || empty($post_data['topic_intro'])){
                    throw new Exception("专题名称和简介都不能为空");
                }
                $topic_id = $post_data['id'];
                $topic = $this->topic->select_by_id($topic_id, 'id, topic_img', 0);
                //1. update topic name intro
                $topic['topic_name'] = $post_data['topic_name'];
                $topic['topic_intro'] = $post_data['topic_intro'];
                // ugly code, but will fix later
                $this->topic->update($topic);
                //2. maybe update img
                if (is_uploaded_file($_FILES['topic_img']['tmp_name'])) {
                    //判断上传文件是否允许
                    $file_name = $_FILES['topic_img']['name'];
                    $date = date("YmdHms");
                    $store_file_name = $date . $file_name;
                    $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
                    $file_host = STATIC_PATH . $file_abs;
                    if (move_uploaded_file($_FILES['topic_img']['tmp_name'], $file_host)) {
                        //把以前的图片删除
                        $origin = $topic['topic_img'];
                        $origin_pos = STATIC_PATH . $this->config->config['img_dir'] . "/" . $origin;
                        if (file_exists($origin_pos) && $origin != 'default_topic_img.png') {
                            unlink($origin_pos);
                        }
                        $topic['topic_img'] = $store_file_name;
                        //把topic的topic_img更新
                        $this->topic->update($topic);
                        $result['data'] = $store_file_name;
                    }
                }
            } catch (Exception $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function set_topic_img(){
    }

    public function add_topic(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $data = [
                'topic_name'   => $post_data['topic_name'],
                'topic_intro'   => $post_data['topic_intro'],
            ];
            $result = [
                'status' => 'success',
                'message' => '',
                'data'    => '',
            ];
            try {
                //topic topic_img
                if(is_uploaded_file($_FILES['topic_img']['tmp_name'])) {
                    $file_name = $_FILES['topic_img']['name'];
                    $date = date("YmdHms");
                    $store_file_name = $date . $file_name;
                    $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
                    $file_host = STATIC_PATH . $file_abs;
                    if (move_uploaded_file($_FILES['topic_img']['tmp_name'], $file_host) == false) {
                        throw new Exception("topic img上传失败");
                    }
                    $data['topic_img'] = $store_file_name;
                } else {
                    throw new Exception("沒有上传图片");
                }
                $this->insert_hook($data, "topic");
                $id = $this->topic->add_topic($data);
                $result['data'] = $id;
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
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
                if (!isset($post_data['id'])){
                    throw new Exception('无id');
                }
                $id = $post_data['id'];
                $topic = $this->topic->select_by_id($id, 'topic_img', 0);
                $img = $topic['topic_img'];
                $file_abs = $this->config->config["img_dir"] . "/" . $img;
                $file_host = STATIC_PATH . $file_abs;
                @unlink($file_host);
                $this->topic->del_topic($id);
                //删除topic img

                //把该media下的所有文章都删除
                $this->article->del_by_topic($id);
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }
}
