<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: bingtaoli
 * Date: 2015/9/19
 * Time: 15:37
 * 显示登录界面/执行登录登出操作
 */
class User extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('mod_user', 'user');
        $this->load->model('mod_media', 'media');
        $this->load->model('mod_article', 'article');
        $this->load->model('mod_topic', 'topic');
    }

    /**
     * 注册
     */
    public function register(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $username = $post_data['username'];
            $password = $post_data['password'];
            $s = var_export($post_data, true);
            $result = [
                'status' => 'success',
                'message' => $s,
                //不返回用户信息
            ];
            try {
                $data = [
                    'username' => $username,
                    'password' => $password,
                    'create_time' => date("Y-m-d"),
                    'identify' => 1,
                ];
                log_message('info', 'register user: ' . $s);
                $this->user->add_user($data);
            } catch (IdentifyException $e){
                if ($e->getCode() == 0){
                    $result['message'] = '该用户已经注册';
                    $result['status'] = 'error';
                }
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    /**
     * 登录
     */
    public function login(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $username = $post_data['username'];
            $password = $post_data['password'];
            $result = [
                'status' => 'success',
                'message' => '',
                //不返回用户信息
            ];
            try {
                $user = $this->user->get_user_by_name_password($username, $password);
                if ($user){
                    $result['message'] = '登陆成功';
                    $this->zw_client->login($username, 1);
                    log_message('info', 'user logged in' . $username);
                } else {
                    $result['message'] = '用户名或密码错误';
                    $result['status'] = 'error';
                }
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function get_detail(){
        $username = $this->username;
        if (!$username){
            return;
        }
        $user = $this->user->select_user_by_name('username, user_avatar, collect_media, collect_article', $username);
        //medias
        $collect_media = $user['collect_media'];
        unset($user['collect_media']);
        if ($collect_media){
            $arr = json_decode($collect_media, true);
            foreach($arr as $a){
                $media = $this->media->select_by_id('id, media_name, media_avatar', $a);
                $user['medias'][] = $media;
            }
        }
        //articles
        $collect_article = $user['collect_article'];
        unset($user['collect_article']);
        if ($collect_article){
            $arr = json_decode($collect_article, true);
            foreach($arr as $a){
                $select = 'id, article_title, article_media_name, article_topic_name, article_img, article_color';
                $article = $this->article->select_by_id($select, $a);
                $user['articles'][] = $article;
            }
        }
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($user));
    }

    /**
     * 收藏文章
     */
    public function collect_article(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $article_id = $post_data['article_id'];
            $username = $post_data['username'];
            $action = $post_data['action'];

            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $user = $this->user->select_user_by_name('collect_article', $username);
                $collect_articles = $user['collect_article'];
                $arr = $collect_articles ? json_decode($collect_articles, true) : []; // old collect
                if ($action == 1){
                    //collect
                    if (array_search($article_id, $arr) == false){
                        $arr[] = $article_id;
                    }
                } else if ($action == 0){
                    //undo collect
                    if ($index = array_search($article_id, $arr)){
                        unset($arr[$index]);
                    }
                }
                $update_articles = json_encode($arr);
                $user['collect_article'] = $update_articles;
                //更新收藏信息
                $this->user->update_user($user);
            } catch(Exception $e){
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    /**
     * 关注媒体
     */
    public function focus_media(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $media_id = $post_data['media_id'];
            $username = $post_data['username'];
            $action = $post_data['action'];

            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $user = $this->user->select_user_by_name('collect_media', $username);
                $origin_collect = $user['collect_media'];
                $arr = $origin_collect ? json_decode($origin_collect, true) : []; // old collect
                if ($action == 1){
                    //collect
                    if (array_search($media_id, $arr) == false){
                        $arr[] = $media_id;
                    }
                } else if ($action == 0){
                    //undo collect
                    if ($index = array_search($media_id, $arr)){
                        unset($arr[$index]);
                    }
                }
                $update_collect = json_encode($arr);
                $user['collect_media'] = $update_collect;
                //更新收藏信息
                $this->user->update_user($user);
            } catch(Exception $e){
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function temp_store_string_to_json()
    {
        if (0) {
            //取出users的collect media
            $users = $this->user->get_all_user();
            foreach ($users as $user) {
                if ($user['collect_media']) {
                    $string = $user['collect_media'];
                    $array = explode(',', $string);
                    $json_str = json_encode($array);
                    $user['collect_media'] = $json_str;
                }
                if ($user['collect_article']) {
                    $string = $user['collect_article'];
                    $array = explode(',', $string);
                    $json_str = json_encode($array);
                    $user['collect_article'] = $json_str;
                }
                $this->user->update_user($user);
            }
            echo "success\n";
        }
    }

}