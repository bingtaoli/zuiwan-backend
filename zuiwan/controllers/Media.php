<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/18
 * Time: 下午4:40
 */

class Media extends MY_Controller
{

    var $article;
    var $media;
    var $output;
    var $config;  // in config/config.php

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        $this->load->model('mod_media', 'media');
    }

    public function get_media(){
        $media = $this->media->get_all_media();
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($media));
    }

    public function set_media_avatar(){
        if (METHOD == 'post'){
            $result['status'] = 'success';
            $result['message'] = '';
            if(is_uploaded_file($_FILES['avatar']['tmp_name']))
            {
                $post_data = $this->input->post();
                $media_name = $post_data['media_name'];

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
                        $media = $this->media->get_media_by_name($media_name);
                        $origin = $media['media_avatar'];
                        $origin_pos = STATIC_PATH . $this->config->config['img_dir'] . "/" . $origin;
                        unlink($origin_pos);

                        //把media的media_avatar更新
                        $this->media->update_media_avatar($media_name, $random_file_name);
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

    public function add_media(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $media_name = $post_data['media_name'];
            $data = [
                'media_name'   => $media_name,
                'create_time'  => date('Y-m-d'),
                'media_avatar' => "default_media_avatar.jpg",
            ];
            $result = [
                'status' => 'success',
                'message' => '',
            ];
            try {
                $this->media->add_media($data);
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function del_media(){
        if (METHOD == 'post') {
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $post_data = $this->input->post();
                $id = $post_data['id'];
                $this->media->del_media($id);
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}