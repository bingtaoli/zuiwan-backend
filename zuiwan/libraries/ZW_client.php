<?php
/**
 * Created by PhpStorm.
 * User: bingtaoli
 * Date: 2015/9/20
 * Time: 12:35
 * 负责session的处理，只针对session,不和数据库交互
 */
class ZW_client{

    var $key;
    var $iv;
    var $iv_size;

//    public function __construct(){
//        $this->key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
//        # create a random IV to use with CBC encoding
//        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//        $this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//        $this->iv_size = $iv_size;
//    }
//
//    public function login($username, $remember=0){
//        $_SESSION['zw_username'] = $username;
//        if ($remember){
//            $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
//            //加密后存cookie
//            $username = $this->encode($username);
//            setcookie('zw_username', $username, time()+60*60*24*365, '/', $domain, false);
//        }
//        return;
//    }
//
//    public function login_check(){
//        if (isset($_SESSION['zw_username'])){
//            return true;
//        }
//        return false;
//    }
//
//    public function get_session_client(){
//        $username = isset($_SESSION['zw_username']) ? $_SESSION['zw_username'] : null;
//        return $username;
//    }
//
//    public function logout(){
//        if (isset($_SESSION['zw_username'])){
//            unset($_SESSION['zw_username']);
//        }
//        //delete cookie
//        if (isset($_COOKIE['zw_username'])){
//            $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
//            $username = "";
//            setcookie('zw_username', $username, time()+60*60*24*365, '/', $domain, false);
//        }
//    }
//
//    public function encode($str){
//        $key = $this->key;
//        $iv = $this->iv;
//        # creates a cipher text compatible with AES (Rijndael block size = 128)
//        # to keep the text confidential
//        # only suitable for encoded input that never ends with value 00h
//        # (because of default zero padding)
//        $cipher_text = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC, $iv);
//
//        # prepend the IV for it to be available for decryption
//        $cipher_text = $iv . $cipher_text;
//
//        # encode the resulting cipher text so it can be represented by a string
//        $cipher_text_base64 = base64_encode($cipher_text);
//
//        return $cipher_text_base64;
//    }
//
//    public function decode($cipher_text_base64){
//        # --- DECRYPTION ---
//        $iv_size = $this->iv_size;
//        $key = $this->key;
//
//        $cipher_text_dec = base64_decode($cipher_text_base64);
//
//        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
//        $iv_dec = substr($cipher_text_dec, 0, $iv_size);
//
//        # retrieves the cipher text (everything except the $iv_size in the front)
//        $cipher_text_dec = substr($cipher_text_dec, $iv_size);
//
//        # may remove 00h valued characters from end of plain text
//        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
//            $cipher_text_dec, MCRYPT_MODE_CBC, $iv_dec);
//
//        return $plaintext_dec;
//    }
}