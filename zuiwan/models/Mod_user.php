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

    public function __construct() {
        parent::__construct();
    }

    private function _add_prefix(&$result){
        add_img_prefix($result, 'user_avatar');
    }

    /**
     * @return int 获取所有用户数量
     */
    public function get_count(){
        return $this->db->count_all_results('user');
    }

    /**
     * @param $data array
     * @throws
     */
    public function add_user($data){
        /**
         * 先判断user是否已经存在，如果存在则保存
         */
        if ($this->select_by_name($data['username'], 'id')){
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

    public function get_all_users(){
        return $this->db->get('user')->result_array();
    }

    /**
     * @param $username
     * @param string $select
     * @param int $add_prefix
     * @return null
     * 这样设计接口是因为username一定需要,select偶尔需要,add_prefix基本不需要
     */
    public function select_by_name($username, $select="*", $add_prefix=1){
        $this->db->select($select);
        $this->db->where('username', $username);
        $result = $this->db->get('user')->result_array();
        if ($add_prefix == 1){
            $this->_add_prefix($result);
        }
        if ($result){
            return $result[0];
        } else {
            return NULL;
        }
    }

    public function get_by_name_password($username, $password){
        $result =  $this->db->get_where('user', ['username' => $username, 'password' => $password])->result_array();
        if ($result){
            return $result[0];
        } else {
            return NULL;
        }
    }

    /**
     * @return mixed
     * 数据库hook所必须的获取所有列函数
     */
    public function get_columns(){
        $result = $this->db->query('SHOW FULL COLUMNS FROM user')->result_array();
        return $result;
    }

}