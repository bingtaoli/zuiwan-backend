<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 16/1/1
 * Time: 下午4:16
 */
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists("add_img_prefix")){
    function add_img_prefix(&$result){
        //把article_img设置成绝对路径
        $img_prefix = HOST . DIR_IN_ROOT .  "/public/upload/img/";
        foreach($result as &$r){
            if (isset($r['article_img']) && $r['article_img'] != ''){
                $r['article_img'] = $img_prefix . $r['article_img'];
            } else if ($r['article_img'] == ''){
                $r['article_img'] = null;
            }
        }
    }
}