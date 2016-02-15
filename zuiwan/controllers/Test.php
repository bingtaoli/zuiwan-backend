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
    }

    public function test_add_user(){
        $data = [
            'username'  => 'bingtao',
            'password'   => "123456",
        ];
        $this->user->add_user($data);
    }

    public function temp_modify_time_format(){
        if (0){
            $articles = $this->article->select_all('id, create_time');
            try {
                foreach ($articles as $a){
                    $unix = strtotime($a['create_time']);
                    $a['create_time'] = date('Y-m-d H:m:s', $unix);
                    $this->article->update_article($a);
                }
            } catch (Exception $e){
                var_dump($e);
            }
        }
    }

    public function get_article_table_columns(){
        $cols = $this->user->get_columns();
        var_dump($cols);
    }

    public function test(){
        // nothing
        if (METHOD == 'post'){
            $result['status'] = 1;
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function test_get_article_visit_count(){
        $result['status'] = 1;
        header("Access-Control-Allow-Origin: *");
        $select = 'visit_count';
        $result['article'] = $this->article->select_by_id($select, 17);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function insert_admin(){
        $data = [
            'username' => 'root',
            'password' => 'zw123',
        ];
        $this->admin->add($data);
    }

}
