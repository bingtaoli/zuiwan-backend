<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class MY_Controller
 * @property CI_DB $db
 * @property CI_Model $model
 * @property CI_Input $input
 * @property CI_Output $output
 * @property Mod_article $article
 * @property Mod_media $media
 * @property Mod_topic $topic
 * @property Mod_user $user
 * @property Mod_admin $admin
 * @property ZW_client $zw_client
 * @property Img_compress $img_compress
 */
class MY_Controller extends CI_Controller{

    var $cfg = null;
    var $dict = null;

    public function __construct(){
        error_reporting(0);
        parent::__construct();
        //load module, singleModule
        $this->load->model('mod_article', 'article');
        $this->load->model('mod_media', 'media');
        $this->load->model('mod_topic', 'topic');
        $this->load->model('mod_user', 'user');
        $this->load->model('mod_admin', 'admin');

        //在angular的BaseCtrl做登录检查

//        if ($username = $this->zw_client->get_session_client()){
//            $this->username = $username;
//        } else {
//            //检查cookie,如果有name则解密
//            if (isset($_COOKIE['zw_username']) && $_COOKIE['zw_username'] != ""){
//                $this->zw_client->login($username);
//                $this->username = $username;
//            }
//        }
    }

    /**
     * @param $data
     * @param $module
     * @throws Exception
     * 插入时检查data是否有效
     * 现在只支持int varchar(xx)的检查,后续会完善.
     */
    public function insert_hook($data, $module){
        $cols = $this->$module->get_columns();
        $notNulls = [];
        $fields = [];
        foreach($cols as $col){
            if ($col['Null'] == 'NO'){
                $notNulls[] = $col['Field'];
            }
            $fieldName = $col['Field'];
            $type = $col['Type'];
            $reg_arr = [
                "/(int)\((\d+)\)/",
                "/(varchar)\((\d+)\)/",
            ];
            $fields[$fieldName] = [];
            foreach ($reg_arr as $reg){
                if (preg_match($reg, $type, $matches)){
                    $fields[$fieldName]['type'] = $matches[1];
                    $fields[$fieldName]['length'] = $matches[2];
                }
            }
        }
        //cols名称合法检查
        foreach($data as $index => $value){
            if (!isset($fields[$index])){
                throw new Exception("unknown column " . $index . "  ");
            }
        }
        foreach($notNulls as $index){
            if (empty($data[$index]) && $index != 'id'){
                throw new Exception($index  . " is null\t");
            }
        }
        //Type合法检查,比如超长字符
        foreach ($data as $index => $value){
            if (isset($fields[$index]['type'])){
                $type = $fields[$index]['type'];
                if ($type == 'int'){
                    if (!preg_match("/^[0-9]*$/", $value)){
                        throw new Exception($index . " should be int, but format error");
                    }
                } else if ($type == 'varchar'){
                    $length = strlen(utf8_decode($value));
                    if ($length > $fields[$index]['length']){
                        throw new Exception($index . "'s length is too long");
                    }
                }
            }
        }
    }

    public function judge_login(){
        if (empty($this->username)){
            throw new Exception("您尚未登陆");
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

