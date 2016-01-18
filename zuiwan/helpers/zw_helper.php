<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 16/1/1
 * Time: 下午4:16
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists("add_img_prefix")){
    function add_img_prefix(&$result, $name){
        //把article_img设置成绝对路径
        $img_prefix = 'http://115.28.75.190/zuiwan-backend/' .  "public/upload/img/";
        foreach($result as &$r){
            if (!isset($r[$name])){
                continue;
            } else if ($r[$name] != ''){
                $r[$name] = $img_prefix . $r[$name];
            } else if ($r[$name] == ''){
                $r[$name] = null;
            }
        }
    }
}