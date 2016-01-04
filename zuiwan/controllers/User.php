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
                'user_detail' => '',  #登录成功后用户具体信息
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
                $user = $this->user->get_user_by_name($username);
                $result['user_detail'] = $user;
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
                'user_detail' => '',  #登录成功后用户具体信息
            ];
            try {
                $user = $this->user->get_user_by_name_password($username, $password);
                if ($user){
                    //设置$user->collect_media
                    if (isset($user['collect_media'])){
                        $arr = explode(',', $user['collect_media']);
                        $collect_media = [];
                        $collect_media_item = [];
                        foreach($arr as $a){
                            $media = $this->media->get_by_id($a);
                            if (isset($media)){
                                $collect_media_item['id'] = $media['id'];
                                $collect_media_item['media_intro'] = $media['media_intro'];
                                $collect_media_item['media_avatar'] = $media['media_avatar'];
                                $collect_media_item['media_name'] = $media['media_name'];
                                //add
                                $collect_media[] = $collect_media_item;
                            } else {
                                //todo 取消收藏
                            }
                        }
                        $user['collect_media'] = $collect_media_item;
                    }
                    //设置$user->collect_article
                    if (isset($user['collect_article'])){
                        $arr = explode(',', $user['collect_article']);
                        $collect_article = [];
                        $collect_item = [];
                        foreach($arr as $a){
                            $article = $this->article->get_by_id($a);
                            if (isset($article)){
                                $collect_item['id'] = $article['id'];
                                $collect_item['article_title'] = $article['article_title'];
                                $topic = $this->topic->get_by_id($article['article_topic']);
                                if (isset($topic)){
                                    $collect_item['article_topic_name'] = $topic['topic_name'];
                                }
                                $media = $this->media->get_by_id($article['article_media']);
                                if (isset($media)){
                                    $collect_item['article_media_name'] = $media['media_name'];
                                }
                                $collect_item['article_intro'] = isset($article['article_intro']) ? $article['article_intro'] : null;
                                $collect_item['article_img'] = isset($article['article_img']) ? $article['article_img'] : null;
                                //add
                                $collect_article[] = $collect_item;
                            } else {
                                //todo 把用户收藏的这篇文章取消
                            }
                        }
                        $user['collect_article'] = $collect_article;
                    }
                    $result['message'] = '登陆成功';
                    log_message('info', 'user logged in' . $username);
                    # 获取用户具体信息
                    $result['user_detail'] = $user;
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

    /**
     * 收藏文章
     */
    public function collect_article(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $article_id = $post_data['article_id'];
            $username = $post_data['username'];
            $user = $this->user->get_user_by_name($username);
            $collect_articles = $user['collect_article'];
            $arr = $collect_articles ? explode(',', $collect_articles) : []; // old collect
            $arr[] = $article_id; // add new
            $arr = array_unique($arr); // unique
            $update_articles = implode(',', $arr);
            $user['collect_article'] = $update_articles;
            $result['status'] = 'success';
            $result['message'] = '';
            try {
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
     * 收藏媒体
     */
    public function collect_media(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $media_id = $post_data['media_id'];
            $username = $post_data['username'];
            $user = $this->user->get_user_by_name($username);
            $origin_collect = $user['collect_media'];
            $arr = $origin_collect ? explode(',', $origin_collect) : []; // old collect
            $arr[] = $media_id; // add new
            $arr = array_unique($arr); // unique
            $update_collect = implode(',', $arr);
            $user['collect_media'] = $update_collect;

            $result['status'] = 'success';
            $result['message'] = '';
            try {
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

    public function get_collect_media(){
        if (METHOD == 'get') {
            $get_data = $this->input->get();
            $username = isset($get_data['username']) ? $get_data['username'] : null;
            $user = $this->user->get_user_by_name($username);
            if ($user){
                $collect = $user['collect_media'];
                $collect_arr = explode(',', $collect);
                $this->load->model('mod_media', 'media');
                $medias = [];
                foreach($collect_arr as $c){
                    $medias[] =  $this->media->get_by_id($c);
                }
                $result = $medias;
            } else {
                $result = [];
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function get_collect_article(){
        if (METHOD == 'get') {
            $get_data = $this->input->get();
            $username = isset($get_data['username']) ? $get_data['username'] : null;
            $user = $this->user->get_user_by_name($username);
            if ($user){
                $collect = $user['collect_article'];
                $collect_arr = explode(',', $collect);
                $this->load->model('mod_article', 'article');
                $medias = [];
                foreach($collect_arr as $c){
                    $medias[] =  $this->article->get_by_id($c);
                }
                $result = $medias;
            } else {
                $result = [];
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}