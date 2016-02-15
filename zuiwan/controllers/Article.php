<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 14:55
 *  @property SphinxClient sphinxclient
 */

class Article extends MY_Controller
{
    var $config;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws
     * 传media则获取某个media内容,否则获取所有内容
     */
    public function get_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $type = isset($get_data['type']) ? $get_data['type'] : null;
            $id = isset($get_data['id']) ? $get_data['id'] : null;
            #memcached
            if (MEMCACHED){
                if ($type && $id){
                    if ($type == 1){
                        //media
                        $articles = $this->memcached->get("articles-media-$id");
                    } else if ($type == 2){
                        $articles = $this->memcached->get("articles-topic-$id");
                    } else {
                        throw new Exception("未知类型文章,无法获取数据");
                    }
                } else {
                    $articles = $this->memcached->get("articles");
                }
            }
            if (!isset($articles) || $articles == null){
                if ($type && $id ){
                    if ($type == 1){
                        $articles = $this->article->get_articles(1, $id);
                        if (MEMCACHED){
                            $this->memcached->set("articles-media-$id", $articles);
                        }
                    } else if ($type == 2){
                        $articles = $this->article->get_articles(2, $id);
                        if (MEMCACHED){
                            $this->memcached->set("articles-topic-$id", $articles);
                        }
                    } else {
                        throw new Exception("未知类型文章,无法获取数据");
                    }
                } else {
                    $articles = $this->article->get_articles();
                    if (MEMCACHED){
                        $this->memcached->set("articles", $articles);
                    }
                }
            }
            header("Access-Control-Allow-Origin: *");
            $result = $articles;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
            if (MEMCACHED){
                $this->memcached->quit();
            }
        }
    }

    public function get_recommend(){
        $result = [];
        //从php.ini获取是否使用yac
        //empty: 不是null且不是0
        if (!empty(ini_get('yac.enable'))){
            $yac = new Yac("zw");
            $recommended = $yac->get("recommended_articles");
            if (empty($recommended)){
                $recommended = $this->article->get_recommended_articles();
                $yac->set("recommended_articles", $recommended);
            }
            $banner = $yac->get('banner');
            if (empty($banner)){
                $banner = $this->article->get_banner_articles();
                $yac->set('banner', $banner);
            }
        } else {
            $recommended = $this->article->get_recommended_articles();
            $banner = $this->article->get_banner_articles();
        }
        $result['recommend'] = $recommended;
        $result['banner'] = $banner;
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function test_recommend(){
        //可以看到select *和select具体列名相差近一半
        $t = getmicrotime();
        $this->get_recommend();
        $tt = getmicrotime();
        echo "costs " . ($tt - $t) . "s" . PHP_EOL;
    }

    //后台管理分页
    //同时返回文章总数
    public function admin_get_page_article(){
        $get_data = $this->input->get();
        $numberPerPage = $get_data['numberPerPage'];
        $index = $get_data['index'];
        if (isset($get_data['is_recommend']) && isset($get_data['is_banner'])){
            $condition = "is_recommend=" . $get_data['is_recommend'] . " and is_banner=" . $get_data['is_banner'];
        } else if (isset($get_data['is_recommend'])){
            $condition = "is_recommend=" . $get_data['is_recommend'];
        } else if (isset($get_data['is_banner'])){
            $condition = "is_banner=" . $get_data['is_banner'];
        } else {
            $condition = null;
        }
        if ($numberPerPage && isset($index)){
            list($count, $articles) = $this->article->get_page_articles($index, $numberPerPage, $condition);
            $result['count'] = $count;
            $result['articles'] = $articles;
        } else {
            $result = [];
            $result['error'] = "必须设定正确的索引和每页数目";
        }
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function get_one_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $id = $get_data['id'];
            $select = 'article_img, create_time, article_title, article_author, article_media, article_topic,
                       article_content, visit_count';
            $article = $this->article->select_by_id($select, $id);
            try {
                if (!empty($article)){
                    //访问量+1
                    $update_data['id'] = $id;
                    $update_data['visit_count'] = $article['visit_count'] + 1;
                    unset($article['visit_count']);
                    $this->article->update_article($update_data);
                    $article['is_focus'] = 0;
                    //如果登陆则判断is_focus是否为1
                    if ($this->username) {
                        $user = $this->user->select_by_name('collect_article', $this->username);
                        $collect_article = $user['collect_article'];
                        $arr = json_decode($collect_article, true);
                        if (!empty($arr) && in_array($id, $arr)){
                            $article['is_focus'] = 1;
                        }
                    }
                    $media_id = $article['article_media'];
                    $topic_id = $article['article_topic'];
                    unset($article['article_media']);
                    unset($article['article_topic']);
                    $article['media'] = $this->media->select_by_id('id, media_name, media_avatar', $media_id);
                    $article['topic'] = $this->topic->select_by_id('id, topic_name, topic_intro, topic_img', $topic_id);
                    $article['topic']['article_count'] = $this->article->get_count_by_topic($article['topic']['id']);
                }
            } catch (Exception $e){
                $article = [];
                $article['status'] = 0;
                $article['error'] = $e->getMessage();
            }
            header("Access-Control-Allow-Origin: *");
            $result = $article;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function admin_get_one_article(){
        if (METHOD == 'get'){
            $get_data = $this->input->get();
            $id = $get_data['id'];
            $select = 'id, article_img, create_time, article_title, article_intro, article_author, article_media,
                       article_topic, article_content, article_color, is_recommend, is_banner';
            $article = $this->article->select_by_id($select, $id);
            header("Access-Control-Allow-Origin: *");
            $result = $article;
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    //获取热门10篇文章
    public function get_top_article(){
        $articles = $this->article->get_top_articles(10);
        header("Access-Control-Allow-Origin: *");
        $result = $articles;
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    //1. 添加文章
    //2. 更改文章
    public function add_article(){
        if (METHOD == 'post'){
            /**
             * TODO 只有root账户可以访问
             */
            $result['status'] = 'success';
            $result['message'] = '';
            $post_data = $this->input->post();
            $article_content = $post_data['article_content'];
            $article_media = $post_data['article_media'];
            $article_topic = $post_data['article_topic'];
            $isUpdate = isset($post_data['is_update']) ? $post_data['is_update'] : null;
            //时间格式 2016-1-1 12:00:00
            $create_time = date('Y-m-d H:m:s');
            //去除img包含的p标签
            $reg = "/<p>(<img.+?)<\/p>/";
            $replacement = '$1';
            $article_content = preg_replace($reg, $replacement, $article_content);
            $post_data['article_content'] = $article_content;
            $post_data['create_time'] = $create_time;
            $data = $post_data;
            $already_stored_in_db = false;
            $id = -1;
            try {
                //获取topic name && media name
                $this->load->model('mod_topic', 'topic');
                $this->load->model('mod_media', 'media');
                $media = $this->media->select_by_id('media_name, media_intro, media_avatar', $article_media);
                $topic = $this->topic->select_by_id('topic_name, topic_intro, topic_img', $article_topic);
                $data['article_media_name'] = $media['media_name'];
                $data['article_topic_name'] = $topic['topic_name'];
                if (!$isUpdate){
                    //先存数据库,再存图片,图片不成功则删除数据
                    $data['article_img'] = 'default_article_img.png';
                    $this->insert_hook($data, "article");
                    $id = $this->article->add_article($data);
                    $already_stored_in_db = true;
                    //存文章大图
                    if(is_uploaded_file($_FILES['file']['tmp_name'])) {
                        $file_name = $_FILES['file']['name'];
                        //$file_type = pathinfo($file_name, PATHINFO_EXTENSION);
                        $date = date("YmdHms");
                        $store_file_name = $date . $file_name;
                        $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
                        $file_host = STATIC_PATH . $file_abs;
                        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_host) == false) {
                            throw new Exception("文章大图上传失败");
                        }
                        $src = $file_host;
                        $this->img_compress->set_img($src);
                        $this->img_compress->set_size(400);
                        //压缩覆盖原图
                        $this->img_compress->save_img($file_host);
                        $article_img = $store_file_name;
                        $data['article_img'] = $article_img;
                        $data['id'] = $id;
                        $this->article->update_article($data);
                    }
                } else {
                    //更新文章内容
                    unset($data['is_update']);
                    if (!empty($data['id'])){
                        $this->article->update_article($data);
                    }
                }
                if (ENABLE_MEMCACHE && ONLINE_MODE){
                    @$this->memcached->delete("articles");
                }
            } catch (Exception $e){
                //根据id删除数据库
                if ($already_stored_in_db){
                    $this->article->del_article($id);
                }
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function update_article_img(){
        if (METHOD == 'post'){
            $post = $this->input->post();
            $id = $post['id'];
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                if(is_uploaded_file($_FILES['article_img']['tmp_name'])) {
                    $file_name = $_FILES['article_img']['name'];
                    //$file_type = pathinfo($file_name, PATHINFO_EXTENSION);
                    //$store_file_name = uniqid() . "." . $file_type;
                    $date = date("YmdHms");
                    $store_file_name = $date . $file_name;
                    $file_abs = $this->config->config["img_dir"] . "/" . $store_file_name;
                    $file_host = STATIC_PATH . $file_abs;
                    if (move_uploaded_file($_FILES['article_img']['tmp_name'], $file_host) == false) {
                        throw new Exception("文章大图上传失败");
                    }
                    //更新数据库
                    $select = 'article_img';
                    //不需要add_prefix
                    $article = $this->article->select_by_id($select, $id, 0);
                    $originImg = $article['article_img'];
                    $article['article_img'] = $store_file_name;
                    $this->article->update_article($article);
                    //如果有old img,则删除old img
                    if ($originImg && $originImg != 'default_article_img.jpg' ){
                        @unlink(STATIC_PATH . $originImg);
                    }
                    if (MEMCACHED){
                        @$this->memcached->delete("articles");
                    }
                }
            } catch(Exception $e){
                $result['status'] = 'error';
                $result['message'] = $e->getMessage();
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function del_article(){
        if (METHOD == 'post') {
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $post_data = $this->input->post();
                $article_id = $post_data['id'];
                $this->article->del_article($article_id);
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function search(){
        if (METHOD == 'get'){
            $data = $this->input->get();
            $query = $data['query'];
            $result = [];
            $ch = curl_init();
            $data = [
                "query" => [
                    "match_phrase" => [
                        'article_content' => $query,
                    ],
                ],
                "highlight" => [
                    "fields" => [
                        "article_content" => [],
                    ]
                ],
            ];
            //为什么写死localhost呢,因为阿里云没有开放9200端口
            $opts = [
                CURLOPT_URL => 'http://localhost:9200/zuiwan/article/_search',
                CURLOPT_POSTFIELDS => json_encode($data, JSON_FORCE_OBJECT),
                CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
                CURLOPT_RETURNTRANSFER => true,
            ];
            curl_setopt_array($ch, $opts);
            $str = curl_exec($ch);
            curl_close($ch);
            $arr = json_decode($str, true);
            /**
             * $arr结构
             * $arr = [
             *     'hits => [
             *          'total' => int,
             *          'max_score' => float,
             *          'hits'  => [
             *              0 => [
             *                  '_index' => 'zuiwan',
             *                  '_type'  => 'article',
             *                  '_source' => [
             *                      'id' => int,
             *                      'article_title' => string,
             *                      //...
             *                  ],
             *                  'highlight' => [
             *                      'article_content' => [
             *                          //
             *                      ]
             *                  ]
             *              ]
             *          ]
             *     ]
             * ]
             */
            if (!empty($arr) && $arr['hits']['total'] > 0){
                foreach ($arr['hits']['hits'] as $hit){
                    $article['article']['id'] = $hit['_source']['id'];
                    $article['article']['article_title'] = $hit['_source']['article_title'];
                    $article['highlight'] = $hit['highlight']['article_content'];
                    $result[] = $article;
                }
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
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

    public function test_core_seek_search(){
        $this->load->library("SphinxClient");
        $this->sphinxclient->SetServer('115.28.75.190' ,9312);
        $cl = $this->sphinxclient;
        $cl->SetConnectTimeout ( 3 );

        $cl->SetArrayResult ( true );
        $cl->SetMatchMode ( SPH_MATCH_EXTENDED);
        $query = '的呵呵';
        $res = $cl->Query ($query, "*" );
        $ids = [];
        var_dump($res);
        $words = array_keys($res['words']);
        //按照长度排序$words
        sort($words);
        $preg = '/';
        foreach($words as $i => $word){
            if ($i < count($words) - 1){
                $preg .= "($word)|";
            } else {
                $preg .= "($word)";
            }
        }
        $preg .= '/';
        foreach ($res['matches'] as $match){
            $ids[] = $match['id'];
        }
        var_dump($preg);
        if (count($ids) > 0){
            $articles = $this->article->select_by_ids('id, article_title, article_content', $ids);
            foreach ($articles as $a){
                preg_match_all($preg, $a['article_content'], $matches, PREG_PATTERN_ORDER);
                var_dump($matches);
            }
        }
    }

}