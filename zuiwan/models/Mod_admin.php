<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 16/2/6
 * Time: 下午12:22
 */
class Mod_admin extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function add($data){
        if ( !empty($this->select_by_name('id', $data['username'])) ){
            throw new IdentifyException("已经存在", 0);
        }
        $this->db->insert('admin', $data);
    }

    public function del($id){
        return $this->db->delete('admin', array('id' => $id));
    }

    public function select_by_name($select ,$username){
        $this->db->select($select);
        $this->db->where('username', $username);
        $result = $this->db->get('user')->result_array();
        return $result;
    }

    public function get_by_name_password($username, $password){
        $result =  $this->db->get_where('admin', ['username' => $username, 'password' => $password])->result_array();
        return $result;
    }

    # token store
    public function select_token_by_user($username){
        $this->db->select('username, token');
        return $this->db->get_where('token', ['username' => $username])->result_array();
    }

    public function select_token_by_user_token($username, $token){
        $this->db->select('username, token, expire_time');
        $result = $this->db->get_where('token', ['username' => $username, 'token' => $token])->result_array();
        if (!empty($result)){
            return $result[0];
        }
        return null;
    }

    public function update_or_add_token($data){
        $username = $data['username'];
        if (!empty($this->select_token_by_user($username))){
            //update
            $this->db->update('token', $data);
        } else {
            $this->db->insert('token', $data);
        }
    }

    public function del_token($username){
        $this->db->delete('token', ['username' => $username]);
    }

}