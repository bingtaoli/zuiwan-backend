<?php
/**
 * Created by PhpStorm.
 * User: bingtaoli
 * Date: 2015/9/19
 * Time: 23:12
 */

/**
 * Class Mod_user
 */
class Mod_user extends CI_Model {

    var $db;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $db
     * not via magic method, so pass $db
     */
    public function init($db){
        $this->db = $db;
    }

    /**
     * @param $data Array
     * @throws
     */
    public function add_user($data){
        /**
         * 先判断user是否已经存在，如果存在则保存
         */
        if ($this->get_user_by_name($data['username'])){
            throw new IdentifyException("该用户应经存在", 0);
        }
        $this->db->insert('user', $data);
    }

    /**
     * @param $data
     * 更新用户信息，比如头像更换，文章收藏
     */
    public function update_user($data){
        $this->db->where('username', $data['username']);
        $this->db->update('user', $data);
    }

    public function del_user($username){
        return $this->db->delete('user', array('username' => $username));
    }

    public function get_all_user(){
        return $this->db->get('user')->result();
    }

    public function get_user_by_name($username){
        $this->db->where('username', $username);
        $result = $this->db->get('user')->result();
        if ($result){
            /**
             * result()是一个数组
             */
            return $result[0];
        } else {
            return NULL;
        }
    }

    public function get_user_by_name_password($username, $password){
        return $this->db->get_where('user', ['username' => $username, 'password' => $password])->result();
    }
}