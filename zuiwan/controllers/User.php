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
        $this->user->init($this->db);
    }

    /**
     * 注册
     */
    public function register(){
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
                $data = [
                    'username' => $username,
                    'password' => $password,
                    'create_time' => date('Y-m-d'),
                    'identify' => 1,
                ];
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
                    $result['message'] = '登陆成功';
                    Zuiwanclient::login($username);
                    # 获取用户具体信息
                    $result['user_detail'] = (array)$user;
                } else {
                    $result['message'] = '用户名或密码错误';
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

    /**
     * 登出
     */
    public function logout(){
        if (METHOD == 'post'){
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                Zuiwanclient::logout();
            } catch(Exception $e){
                $result['message'] = 'unknown error happens';
                $result['status'] = 'error';
            }
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

            $collect_article = $this->user_detail['collect_article'];
            $update_article = $collect_article . ',' . $article_id;
            $this->user_detail['collect_article'] = $update_article;

            $result['status'] = 'success';
            $result['message'] = '';
            try {
                //更新收藏信息
                $this->user->update_user($this->user_detail);
            } catch(Exception $e){
                $result['message'] = 'unknown error happens';
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}