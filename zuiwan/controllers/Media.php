<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/18
 * Time: 下午4:40
 */

class Media extends MY_Controller
{
    var $config;  // in config/config.php

    public function __construct()
    {
        parent::__construct();
    }

    public function get_media(){
        $media = $this->media->select_all('id, media_name, media_intro, media_avatar');
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($media));
    }

    public function get_one_media(){
        $get_data = $this->input->get();
        $id = $get_data['id'];
        $media = $this->media->select_by_id($id, 'media_name, media_intro, media_avatar');
        if (!empty($media)){
            // is_focus
            $media['is_focus'] = 0;
            if (!empty($this->username)){
                $user = $this->user->select_by_name($this->username, 'collect_media');
                $collect_media = $user['collect_media'];
                $arr = json_decode($collect_media, true);
                if (!empty($arr) && in_array($id, $arr)){
                    $media['is_focus'] = 1;
                }
            }
            //fans_num
            $media['fans_num'] = $this->media->get_media_fans($id);
            //articles
            $articles = $this->article->get_by_media($id);
            $media['articles'] = $articles;
            //article count
            $media['article_count'] = count($articles);
        }
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($media));
    }

    public function admin_get_one_media(){
        $get_data = $this->input->get();
        $id = $get_data['id'];
        $media = $this->media->select_by_id($id, 'id, media_name, media_intro, media_avatar');
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($media));
    }

    public function update_media(){
        if (METHOD == 'post'){
            $result['status'] = 1;
            $post_data = $this->input->post();
            if (!isset($post_data['id'])){
                throw new Exception('id needed');
            }
            if (empty($post_data['media_name']) || empty($post_data['media_intro'])){
                throw new Exception("媒体名称和简介都不能为空");
            }
            $media_id = $post_data['id'];
            $media = $this->media->select_by_id($media_id, 'id, media_avatar', 0);
            //1. media name and intro 需要修改,贺鑫的建议
            $media['media_name'] = $post_data['media_name'];
            $media['media_intro'] = $post_data['media_intro'];
            //ugly code, but will fix later
            $this->media->update($media);
            //2. maybe update avatar
            if(is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                //判断上传文件是否允许
                //$file_arr = pathinfo($_FILES['avatar']['name']);
                //$file_type = $file_arr["extension"];
                $file_name = $_FILES['avatar']['name'];
                $date = date("YmdHms");
                if ( strlen($file_name) > 40 - strlen($date) ){
                    //
                }
                $store_file_name = $date . $file_name;
                $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
                $file_host = STATIC_PATH . $file_abs;
                try {
                    if(move_uploaded_file($_FILES['avatar']['tmp_name'], $file_host))
                    {
                        //把以前的图片删除
                        if (!empty($media)){
                            $origin = $media['media_avatar'];
                            $origin_pos = STATIC_PATH . $this->config->config['img_dir'] . "/" . $origin;
                            if (file_exists($origin_pos) && $origin != 'default_media_avatar.png'){
                                @unlink($origin_pos);
                            }
                            //把media的media_avatar更新
                            $media['media_avatar'] = $store_file_name;
                            $this->media->update($media);
                            $result['data'] = $store_file_name;
                            $result['message'] = 'success';
                        }
                    }
                } catch (Exception $e){
                    $result['status'] = 0;
                    $result['message'] = $e->getMessage();
                }
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function add_media(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $data = [
                'media_name'   => $post_data['media_name'],
                'media_intro'  => $post_data['media_intro'],
            ];
            $result = [
                'status' => 'success',
                'message' => '',
                'data'    => '',
            ];
            try {
                //存media avatar
                if(is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                    $file_name = $_FILES['avatar']['name'];
                    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
                    $store_file_name = uniqid() . "." . $file_type;
                    $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
                    $file_host = STATIC_PATH . $file_abs;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_host) == false) {
                        throw new Exception("media avatar上传失败");
                    }
                    $data['media_avatar'] = $store_file_name;
                } else {
                    throw new Exception("沒有上传头像");
                }
                $this->media->add_media($data);
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
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
                if (!isset($post_data['id'])){
                    throw new Exception('无id');
                }
                $id = $post_data['id'];
                $media = $this->media->select_by_id($id, 'media_avatar', 0);
                //删img
                $img = $media['media_avatar'];
                $file_abs = $this->config->config["img_dir"] . "/" . $img;
                $file_host = STATIC_PATH . $file_abs;
                @unlink($file_host);
                $this->media->del_media($id);
                //把该media下的所有文章都删除
                $this->article->del_by_media($id);
                //把关注列表中的该media删除
                $users = $this->user->get_all_users();
                foreach ($users as $user){
                    $collect_media = $user['collect_media'];
                    if (empty($collect_media)){
                        continue;
                    }
                    $arr = json_decode($collect_media, true);
                    if ($index = array_search($id, $arr)){
                        //2.删除该关注
                        unset($arr[$index]);
                        $str = json_encode($arr);
                        //3.store
                        $user['collect_media'] = $str;
                        $this->user->update_user($user);
                    }
                }
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