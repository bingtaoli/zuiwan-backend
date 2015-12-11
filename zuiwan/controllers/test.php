<?php
/**
 * Created by PhpStorm.
 * User: bingtaoli
 * Date: 2015/11/21
 * Time: 21:53
 * 测试添加user，project，mates
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_user', 'user');
        $this->user->init($this->db);
        $this->load->model('mod_article', 'article');
        $this->article->init($this->db);
    }

    public function test_add_user(){
        $data = [
            'username'  => 'bingtao',
            'password'   => "123456",
        ];
        $this->user->add_user($data);
    }

    public function test_show_all_article(){
        $data = $this->article->get_all_article();
        var_dump($data);
    }

}
