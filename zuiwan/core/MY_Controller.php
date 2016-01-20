<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

    var $cfg = null;
    var $dict = null;
    var $zw_client;

    public function __construct(){
        error_reporting(10);
        parent::__construct();
        if ($username = $this->zw_client->get_session_client()){
            $this->username = $username;
        } else {
            //检查cookie,如果有name则解密
            if (isset($_COOKIE['zw_username']) && $_COOKIE['zw_username'] != ""){
                $username = $this->zw_client->decode($_COOKIE['zw_username']);
                //去除加密算法带来的空格
                $username = rtrim($username);
                $this->zw_client->login($username);
                $this->username = $username;
            }
        }
    }

    protected function _json($data,$code=1,$msg=null){
        $ret = array('code'=>$code,'msg'=>$msg,'data'=>$data);
        echo json_encode($ret);
    }
}

/**
 * Class IdentifyException
 * TODO 完善log信息的输出
 */
class IdentifyException extends Exception {
    // 重定义构造器使 message 变为必须被指定的属性
    public function __construct($message, $code = 0) {
        // 自定义的代码, 确保所有变量都被正确赋值
        parent::__construct($message, $code);
    }
    // 自定义字符串输出的样式
    public function __toString() {
        return __CLASS__ . ": [{$this->file}]: {$this->message}\n";
    }
}

