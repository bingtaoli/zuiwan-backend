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
    }

    public function test_hook(){
        try {
            $data = [
                'username' => 'lixxxxxx',
                'password' => 'li',
                'create_time' => '2015-2-1 12:00:00',
            ];
            $this->insert_hook($data, "user");
        } catch (Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * 注册
     */
    public function register(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            //数据校验
            if (empty($post_data['username']) || empty($post_data['password'])){
                throw new Exception("必须正确填写用户名和密码");
            }
            $username = $post_data['username'];
            $password = $post_data['password'];
            $result = [
                'status' =>  1,
                'message' => '',
                //不返回用户信息
            ];
            try {
                $data = [
                    'username' => $username,
                    'password' => $password,
                    'create_time' => date("Y-m-d"),
                    'user_avatar' => 'default_user_avatar.png',
                ];
                log_message('info', 'register user: ' . $username);
                $this->insert_hook($data, "user");
                $this->user->add_user($data);
            } catch (IdentifyException $e){
                if ($e->getCode() == 0){
                    $result['message'] = '该用户已经注册';
                    $result['status'] = 0;
                }
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 0;
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
            try {
                $post_data = $this->input->post();
                if (empty($post_data['username']) || empty($post_data['password'])) {
                    //throw exception
                    throw new Exception('用户名或密码缺失');
                }
                $username = $post_data['username'];
                $password = $post_data['password'];
                $result = [
                    'status' => 1,
                    'message' => '',
                    //不返回用户信息
                ];
                $user = $this->user->get_by_name_password($username, $password);
                if ($user) {
                    $result['message'] = '登陆成功';
                    $this->zw_client->login($username);
                    //设置cookie
                    //store username in cookie
                    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
                    setcookie('zw_username', $username, time() + SECONDS_A_DAY * 20, '/', $domain, false);
                    log_message('info', 'user logged in' . $username);
                } else {
                    $result['message'] = '用户名或密码错误';
                    $result['status'] = 0;
                }
            } catch (Exception $e){
                if ($e->getMessage()){
                    $message = $e->getMessage();
                } else {
                    $message = '未知错误，请联系管理员';
                }
                $result['message'] = $message;
                $result['status'] = 0;
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function get_detail(){
        //从cookie获取username
        //$username =
        $username = $this->username;
        if (!$username){
            return;
        }
        $user = $this->user->select_by_name($username, 'username, user_avatar, collect_media, collect_article');
        //medias
        $collect_media = $user['collect_media'];
        unset($user['collect_media']);
        $user['medias'] = [];
        if ($collect_media){
            $arr = json_decode($collect_media, true);
            foreach($arr as $a){
                $media = $this->media->select_by_id($a, 'id, media_name, media_avatar');
                if (empty($media)){
                    continue;
                }
                $user['medias'][] = $media;
            }
        }
        //articles
        $collect_article = $user['collect_article'];
        unset($user['collect_article']);
        $user['articles'] = [];
        if ($collect_article){
            $arr = json_decode($collect_article, true);
            foreach($arr as $a){
                $select = 'id, article_title, article_media_name, article_topic_name, article_img, article_color';
                $article = $this->article->select_by_id($a, $select);
                if (empty($article)){
                    continue;
                }
                $user['articles'][] = $article;
            }
        }
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($user));
    }

    /**
     * 收藏文章
     * 保存article id数组 json字符串
     */
    public function collect_article(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $article_id = $post_data['article_id'];
            $action = $post_data['action'];

            $result['status'] = 1;
            $result['message'] = '';
            try {
                $this->judge_login();
                $user = $this->user->select_by_name($this->username, 'collect_article, username');
                $collect_articles = $user['collect_article'];
                $arr = $collect_articles ? json_decode($collect_articles, true) : []; // old collect
                if ($action == 1){
                    //collect
                    if (array_search($article_id, $arr) === false){
                        $arr[] = $article_id;
                    }
                } else if ($action == 0){
                    //undo collect
                    if (($index = array_search($article_id, $arr)) !== false){
                        unset($arr[$index]);
                    }
                }
                $update_articles = json_encode($arr);
                $user['collect_article'] = $update_articles;
                //更新收藏信息
                $this->user->update_user($user);
            } catch(Exception $e){
                $result['message'] = $e->getMessage();
                $result['status'] = 0;
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    /**
     * 关注媒体
     * 保存media id数组 json字符串
     */
    public function focus_media(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $media_id = $post_data['media_id'];
            $action = $post_data['action'];

            $result['status'] = 1;
            $result['message'] = '';
            try {
                $this->judge_login();
                $user = $this->user->select_by_name($this->username, 'collect_media, username');
                $origin_collect = $user['collect_media'];
                $arr = $origin_collect ? json_decode($origin_collect, true) : []; // old collect
                if ($action == 1){
                    //collect
                    if (array_search($media_id, $arr) === false){
                        $arr[] = $media_id;
                    }
                } else if ($action == 0){
                    //undo collect
                    if (($index = array_search($media_id, $arr)) !== false){
                        unset($arr[$index]);
                    }
                }
                $update_collect = json_encode($arr);
                $user['collect_media'] = $update_collect;
                //更新收藏信息
                $this->user->update_user($user);
            } catch(Exception $e){
                $result['message'] = $e->getMessage();
                $result['status'] = 0;
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}