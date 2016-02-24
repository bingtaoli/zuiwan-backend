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

    public function del_cookie(){
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        $username = "";
        //删除cookie,把超时时间设置成一个小时过去
        //domain is needed
        setcookie('zw_username', $username, time() - 60*60, '/', $domain, false);
    }

    public function test_elastic_search(){
        $ch = curl_init();
        $data = [
            "query" => [
                "match_phrase" => [
                    'article_content' => '呵呵'
                ],
            ],
            "highlight" => [
                "fields" => [
                    "article_content" => [],
                ]
            ],
        ];
        $opts = [
            CURLOPT_URL => 'http://localhost:9200/zuiwan/article/_search',
            CURLOPT_POSTFIELDS => json_encode($data, JSON_FORCE_OBJECT),
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => true,
        ];

        curl_setopt_array($ch, $opts);
        $str = curl_exec($ch);
        $arr = json_decode($str, true);
        var_dump($arr);
        //var_dump($arr['hits']);
        var_dump($arr['hits']['hits']);
        $article =  $arr['hits']['hits'][0]['_source'];
        $article_highlight =  $arr['hits']['hits'][0]['highlight']['article_content'];
        echo $article['article_content'];
        echo $article_highlight;
        curl_close($ch);
    }
}
