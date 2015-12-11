<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

    var $username = '';
    var $cfg = null;
    var $dict = null;

    public function __construct(){
        error_reporting(10);
        parent::__construct();
        $this->db = $this->load->database('zuiwan_m', TRUE);
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

