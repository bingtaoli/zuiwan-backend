<?php
/**
 * Created by PhpStorm.
 * User: bingtaoli
 * Date: 2015/9/20
 * Time: 12:35
 * 负责session的处理，只针对session,不和数据库交互
 */
class Zuiwanclient{

    private static $session_key_name = "HIA1HIA2HIA3HIA4";

    public static function login($username){
        if (self::login_check()){
            return;
        }
        $CI = & get_instance();
        $CI->load->library('session');
        $CI->session->set_userdata(self::$session_key_name, $username);
        $CI->load->helper('cookie');
        set_cookie('username', $username);
        return;
    }

    public static function login_check(){
        if (self::get_session_client()){
            return true;
        }
        return false;
    }

    public static function get_session_client(){
        $CI = & get_instance();
        $CI->load->library('session');
        $username = $CI->session->userdata(self::$session_key_name);
        return $username;
    }

    public static function logout(){
        $CI = & get_instance();
        $CI->load->library('session');
        $CI->session->set_userdata(self::$session_key_name, '');
        $CI->load->helper('cookie');
        delete_cookie('username');
    }
}