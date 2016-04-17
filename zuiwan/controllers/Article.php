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

    public function get_recommend(){
        $result = [];
        $page = 1;
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1){
            $page = $_GET['page'];
        }
        if ($page == 1){
            $banner = $this->article->get_banner_articles();
            if (!empty($banner) &&  count($banner) > 3){
                $banner = array_slice($banner, 0, 3);
            }
            $result['banner'] = $banner;
        }
        list($recommended, $count) = $this->article->get_recommended_articles($page-1);
        $result['recommend'] = $recommended;
        $result['recommendCount'] = $count;
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function get_recommend_articles(){
        $page = 1;
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1){
            $page = $_GET['page'];
        }
        list($recommended, $count) = $this->article->get_recommended_articles($page-1);
        $result = $recommended;
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    //获取banner的文章数,一般建议在4篇左右
    public function get_banner_count(){
        $banner = $this->article->get_banner_articles();
        $result = count($banner);
        header("Access-Control-Allow-Origin: *");
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    public function test_recommend(){
        $result = $this->article->test_get_recommend_articles();
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($result));
    }

    //提醒用户关注的媒体有文章更新了
    public function remind_media_article(){
        //1. 遍历用户表
        //1.1 遍历关注的媒体
        //1.1.1 把每个被媒体更新的文章(即create_time在时间范围之内的)加入到数组中
        //1.2 发邮件
        $users = $this->user->get_all_users();
        foreach ($users as $user){
            if (!empty($user['collect_media'])){
                $medias = json_decode($user['collect_media'], true);
                $email_data = [];
                foreach ($medias as $media_id){
                    //获取文章
                    $articles = $this->article->get_by_media($media_id);
                    //是否在时间范围内
                    $now = time();
                    $bound = $now - REMIND_TIME;
                    foreach ($articles as $article){
                        $unix_create_time = strtotime($article['create_time']);
                        if ($unix_create_time >= $bound){
                            $email_data[$media_id][] = $article;
                        }
                    }
                }
                //把media id索引转换成media name索引
                foreach ($email_data as $media_id => $media_articles){
                    $media = $this->media->select_by_id($media_id);
                    $email_data[$media['media_name']] = $media_articles;
                    unset($email_data[$media_id]);
                }
                //发送邮件
                $content = '';
                $subject = '';
                //username就是邮箱
                $receivers = [$user['username']];
                $this->_send_mail($subject, $content, $receivers);
            }
        }
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
            $select = 'id, article_title, article_intro, article_author, article_publisher, article_media, article_media_name,
                       article_topic, article_topic_name, create_time, article_img, is_recommend';
            list($count, $articles, $recommend_count, $banner_count) = $this->article->get_page_articles($index, $numberPerPage, $select, $condition);
            $result['count'] = $count;
            $result['articles'] = $articles;
            $result['recommend_count'] = $recommend_count;
            $result['banner_count'] = $banner_count;
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
            $article = $this->article->select_by_id($id, $select);
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
                        $user = $this->user->select_by_name($this->username, 'collect_article');
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
                    $article['media'] = $this->media->select_by_id($media_id, 'id, media_name, media_avatar');
                    $article['topic'] = $this->topic->select_by_id($topic_id, 'id, topic_name, topic_intro, topic_img');
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
            $select = 'id, article_img, create_time, article_title, article_intro, article_author, article_publisher, article_media,
                       article_topic, article_content, article_color, is_recommend, is_banner';
            $article = $this->article->select_by_id($id, $select);
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
            $result['status'] = 'success';
            $result['message'] = '';
            $data = $this->input->post();
            if (isset($data['editorValue'])){
                //暂时解决百度editor自动提交
                unset($data['editorValue']);
            }
            $article_content = $data['article_content'];
            $article_media = $data['article_media'];
            $article_topic = $data['article_topic'];
            $isUpdate = isset($data['is_update']) ? $data['is_update'] : null;
            //去除img包含的p标签
            //因为ckeditor默认是会把img包含在p标签中
            $reg = "/<p>(<img.+?)<\/p>/";
            $replacement = '$1';
            $article_content = preg_replace($reg, $replacement, $article_content);
            $data['article_content'] = $article_content;
            $already_stored_in_db = false;
            $id = -1;
            //时间戳
            try {
                //获取topic name && media name
                $this->load->model('mod_topic', 'topic');
                $this->load->model('mod_media', 'media');
                $media = $this->media->select_by_id($article_media, 'media_name, media_intro, media_avatar');
                $topic = $this->topic->select_by_id($article_topic, 'topic_name, topic_intro, topic_img');
                $data['article_media_name'] = $media['media_name'];
                $data['article_topic_name'] = $topic['topic_name'];
                //文章简介最少50字
                if (!empty($data['article_intro'])){
                    if (strlen(utf8_decode($data['article_intro'])) < 50){
                        throw new Exception("文章简介最少50字");
                    }
                } else {
                    throw new Exception("文章简介未填写");
                }
                if (!$isUpdate){
                    //时间格式 2016-1-1 12:00:00
                    $create_time = date('Y-m-d H:m:s');
                    $data['create_time'] = $create_time;
                    //先存数据库,再存图片,图片不成功则删除数据
                    $data['article_img'] = 'default_article_img.png';
                    //my hook function, it is a simple idea, :)
                    $this->insert_hook($data, "article");
                    $id = $this->article->add_article($data);
                    $already_stored_in_db = true;
                    //存文章大图
                    if(is_uploaded_file($_FILES['file']['tmp_name'])) {
                        $file_name = $_FILES['file']['name'];
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
                        //根据图片大小来进行压缩比例
                        //以1024K为界限
                        if ($_FILES['file']['size']  > 1024 * 1024){
                            $this->img_compress->set_quality(60);
                        } else if ($_FILES['file']['size']  > 500 * 1024){
                            $this->img_compress->set_quality(65);
                        } else if ($_FILES['file']['size']  > 400 * 1024) {
                            $this->img_compress->set_quality(70);
                        }
                        //压缩覆盖原图
                        $this->img_compress->save_img($file_host);
                        $article_img = $store_file_name;
                        $data['article_img'] = $article_img;
                        $data['id'] = $id;
                        $this->article->update_article($data);
                    }
                } else {
                    unset($data['is_update']);
                    if (!isset($data['id'])){
                        throw new Exception("没有id, 不能更新");
                    }
                    //更新文章内容
                    //除了文章大图和文章颜色
                    $article = $this->article->select_by_id($data['id'], '*', 0);
                    //后者覆盖前者
                    $data = array_merge($article, $data);
                    $this->insert_hook($data, 'article', 0);
                    $this->article->update_article($data);
                    //先删除
                    //elastic search delete
                    $es_ids = $this->get_article_elastic_id($data['id']);
                    if (!empty($es_ids)){
                        foreach ($es_ids as $es_id){
                            $this->del_article_from_elastic($es_id);
                        }
                    }
                }
                //不管增加或更新,都是add到elastic
                //data即一篇文章的数据
                $this->_add_articles_to_elastic([$data]);
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
            if (isset($post['id'])){
                $id = $post['id'];
            } else {
                throw new Exception("no article id");
            }
            $result['status'] = 1;
            $result['message'] = '';
            try {
                if(is_uploaded_file($_FILES['article_img']['tmp_name'])) {
                    //文章颜色
                    if (empty($post['article_color'])){
                        throw new Exception("没有选择颜色");
                    }
                    $article_color = $post['article_color'];

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
                    $select = 'id, article_img';
                    //不需要add_prefix
                    $article = $this->article->select_by_id($id, $select, 0);
                    $originImg = $article['article_img'];
                    $article['article_img'] = $store_file_name;
                    $article['article_color'] = $article_color;
                    $this->article->update_article($article);
                    //如果有old img,则删除old img
                    if ($originImg && $originImg != 'default_article_img.png' ){
                        @unlink(STATIC_PATH . $originImg);
                    }
                } else {
                    throw new Exception('还没有上传图片');
                }
            } catch(Exception $e){
                $result['status'] = 0;
                $result['message'] = $e->getMessage();
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function del_article(){
        if (METHOD == 'post') {
            $result['status'] = 1;
            $result['message'] = '';
            try {
                $post_data = $this->input->post();
                if (isset($post_data['id'])){
                    $article_id = $post_data['id'];
                } else {
                    throw new Exception('no article id');
                }
                $article = $this->article->select_by_id($article_id, 'article_img', 0);
                if (empty($article)){
                    throw new Exception("错误的文章");
                }
                $img = $article['article_img'];
                if ($img != 'default_article_img.png'){
                    $file_abs = $this->config->config["img_dir"] . "/" . $img;
                    $file_host = STATIC_PATH . $file_abs;
                    @unlink($file_host);
                }
                $this->article->del_article($article_id);
                //把关注该文章的用户的关注列表删除
                //虽然很延时,但是在后台管理操作,可以忽略
                //1.遍历所有的用户,如果用户关注了该文章,则删除该关注
                //todo ugly code, 每篇文章删除都要遍历..
                $users = $this->user->get_all_users();
                foreach ($users as $user){
                    $collect_article = $user['collect_article'];
                    if (empty($collect_article)){
                        continue;
                    }
                    $arr = json_decode($collect_article, true);
                    if ($index = array_search($article_id, $arr)){
                        //2.删除该关注
                        unset($arr[$index]);
                        $str = json_encode($arr);
                        //3.store
                        $user['collect_article'] = $str;
                        $this->user->update_user($user);
                    }
                }
                //elastic search delete
                $es_ids = $this->get_article_elastic_id($article_id);
                if (!empty($es_ids)){
                    foreach ($es_ids as $es_id){
                        $this->del_article_from_elastic($es_id);
                    }
                }
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 0;
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    //根据文章id获取elastic search中的存储id
    //返回是id数组,一般都只有一个元素
    public function get_article_elastic_id($article_id){
        if (!is_numeric($article_id)){
            return null;
        }
        $ch = curl_init();
        $opts = [
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_URL           => "http://localhost:9200/zuiwan/article/_search?pretty&q=id:$article_id",
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($ch, $opts);
        $str = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($str, true);
        /**
         * $arr结构可以参照下面的search函数注释
         */
        $id_array = [];
        if (!empty($arr) && $arr['hits']['total'] > 0){
            foreach ($arr['hits']['hits'] as $hit){
                if (!isset($hit['_source']['id'])){
                    continue;
                }
                $id_array[] = $hit['_id'];
            }
        }
        return $id_array;
    }

    //根据elastic search的存储id删除
    public function del_article_from_elastic($id){
        $ch = curl_init();
        $opts = [
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_URL           => "http://localhost:9200/zuiwan/article/$id",
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($ch, $opts);
        curl_exec($ch);
        curl_close($ch);
    }

    public function pull_data_to_elastic(){
        $select = 'id, article_content, article_content';
        $articles = $this->article->select_all($select);
        $this->_add_articles_to_elastic($articles);
    }

    //输入为一个数组
    public function _add_articles_to_elastic($articles){
        $ch = curl_init();
        $opts = [
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_URL           => "http://localhost:9200/zuiwan/article",
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($ch, $opts);
        foreach ($articles as $article){
            if (!isset($article['id'])){
                continue;
            }
            $data = json_encode([
                'id'    => $article['id'],
                'article_title'  => $article['article_title'],
                'article_content' => $article['article_content']
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $str = curl_exec($ch);
        }
        curl_close($ch);
    }

    public function search(){
        if (METHOD == 'get'){
            $data = $this->input->get();
            $query = $data['query'];
            $result = [];
            $ch = curl_init();
            $data = [
                "query" => [
                    //奇怪,term不行
                    "match_phrase" => [
                        'article_content' => $query,
                    ],
                ],
                "highlight" => [
                    "pre_tags"  => ["<tag1>", "<tag2>"],
                    "post_tags" => ["</tag1>", "</tag2>"],
                    "fields" => [
                        "article_content" => ["fragment_size" => 60, "number_of_fragments" => 4],
                    ]
                ],
            ];
            //为什么写死localhost呢,因为阿里云没有开放9200端口
            $json_data = json_encode($data);
            $opts = [
                CURLOPT_URL => 'http://localhost:9200/zuiwan/article/_search',
                CURLOPT_POSTFIELDS => $json_data,
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
                    if (!isset($hit['_source']['id'])){
                        continue;
                    }
                    $article['article']['id'] = $hit['_source']['id'];
                    $article['article']['article_title'] = $hit['_source']['article_title'];
                    $article['highlight'] = $hit['highlight']['article_content'];
                    $result[] = $article;
                }
                //正则去掉多余错误的html
                foreach ($result as &$article){
                    foreach ($article['highlight'] as &$highlight){
                        //1. 把em标签保存
                        $highlight = preg_replace('/<tag1>(.+?)<\/tag1>/', 'ZW_PREG${1}ZW_PREG', $highlight);
                        //2. 除去其他的html标签
                        $pattern = array(
                            # 优先级是从上到下
                            '/<.+?>/', #<strong>
                            '/<\/.+?>/', #</strong>
                            '/[^<]*?>/', # 'strong>he he he<em>he he he</em>' 去除strong
                            '/<[^>]*/', # 'he he </p'  去除</p
                        );
                        $highlight = preg_replace($pattern, '', $highlight);
                        //3. 还原em标签
                        $highlight = preg_replace('/ZW_PREG(.+?)ZW_PREG/', '<em>${1}</em>', $highlight);
                        //4. 去除换行
                        $highlight = preg_replace('/\n/', '', $highlight);
                    }
                }
            }
            header("Access-Control-Allow-Origin: *");
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    //定期调用清理未使用的图片
    public function clean_used_pic(){
        //
    }

}