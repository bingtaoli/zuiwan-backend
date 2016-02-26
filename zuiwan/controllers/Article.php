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

    //获取banner的文章数,一般建议在4篇左右
    public function get_banner_count(){
        $banner = $this->article->get_banner_articles();
        $result = count($banner);
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
            list($count, $articles) = $this->article->get_page_articles($index, $numberPerPage, $select, $condition);
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
                $media = $this->media->select_by_id($article_media, 'media_name, media_intro, media_avatar');
                $topic = $this->topic->select_by_id($article_topic, 'topic_name, topic_intro, topic_img');
                $data['article_media_name'] = $media['media_name'];
                $data['article_topic_name'] = $topic['topic_name'];
                if (!$isUpdate){
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
                    $this->article->update_article($article);
                    //如果有old img,则删除old img
                    if ($originImg && $originImg != 'default_article_img.jpg' ){
                        @unlink(STATIC_PATH . $originImg);
                    }
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
                $file_abs = $this->config->config["img_dir"] . "/" . $img;
                $file_host = STATIC_PATH . $file_abs;
                @unlink($file_host);
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
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 0;
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
                        "article_content" => ["fragment_size" => 150, "number_of_fragments" => 3],
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
                //正则去掉多余错误的html
                foreach ($result as &$article){
                    foreach ($article['highlight'] as &$highlight){
                        //1. 把em标签保存
                        $highlight = preg_replace('/<em>(.+?)<\/em>/', 'ZW_PREG${1}ZW_PREG', $highlight);
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

}