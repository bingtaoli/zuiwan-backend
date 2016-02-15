<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 23:42
 */

$config=array();
$config['img_dir']="/upload/article_img";  // public is included in STATIC_PATH

class Admin extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //总访问量页面的信息
    public function get_website_information(){
        $article_count = $this->article->get_count();
        $user_count = $this->user->get_count();

        $information['article_count'] = $article_count;
        $information['user_count'] = $user_count;
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($information));
    }

    public function login(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $remember = 0;
            if (!empty($post_data['username'])){
                $username = $post_data['username'];
            } else {
                throw new Exception("user name needed");
            }
            if (!empty($post_data['password'])){
                $password = $post_data['password'];
            }
            if (!empty(($post_data['remember_me']))){
                $remember = $post_data['remember_me'];
            }
            if (!empty($post_data['token'])){
                $token = $post_data['token'];
            }
            $result = [
                'status' => 1,
                'message' => '',
            ];
            try {
                if (isset($password)){
                    $user = $this->admin->get_by_name_password($username, $password);
                    if ($user){
                        $result['message'] = '登陆成功';
                        //设置session
                        $this->zw_client->login($username, $remember);
                        //生成token
                        $token = md5(microtime(true));
                        //store token in DB
                        $data = [
                            'username' => $username,
                            'token'    => $token,
                            'expire_time' => time() + SECONDS_A_DAY * 20, //20天
                        ];
                        $this->admin->update_or_add_token($data);
                        if ($remember){
                            //cookie store token
                            $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
                            setcookie('zw_token', $token, time() + SECONDS_A_DAY * 20, '/', $domain, false);
                        }
                    } else {
                        $result['message'] = '用户名或密码错误';
                        $result['status'] = 0;
                    }
                } else if (isset($token)){
                    //check token && username in DB
                    $data = $this->admin->select_token_by_user_token($username, $token);
                    if (!empty($data)){
                        if ($data['expire_time'] < time()){
                            //超时
                            $result['status'] = 0;
                            $result['message'] = 'token超时,重新登录';
                        }
                    } else {
                        //token错误
                        $result['status'] = 0;
                        $result['message'] = 'token错误';
                    }
                }
            } catch (Exception $e){
                $result['message'] = $e->getMessage();
                $result['status'] = 0;
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}
