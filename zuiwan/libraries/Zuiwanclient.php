<?php
/**
 * Created by PhpStorm.
 * User: bingtaoli
 * Date: 2015/9/20
 * Time: 12:35
 * 负责session的处理，只针对session,不和数据库交互
 */
class Zuiwanclient{

    public static function login($username){
        if (self::login_check()){
            return;
        }
        $_SESSION['username'] = $username;
        return;
    }

    public static function login_check(){
        if (self::get_session_client()){
            return true;
        }
        return false;
    }

    public static function get_session_client(){
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }

    public static function logout(){
        if(isset($_SESSION['username'])){
            unset($_SESSION['username']);
        }
    }
}