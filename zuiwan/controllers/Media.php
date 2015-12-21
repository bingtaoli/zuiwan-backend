<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/18
 * Time: 下午4:40
 */

$config=array();
$config['img_dir']= "upload/img";

class Media extends MY_Controller
{

    var $article;
    var $media;
    var $output;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        $this->article->init($this->db);
        $this->load->model('mod_media', 'media');
        $this->media->init($this->db);
    }

    public function get_all_media(){
        $media = $this->media->get_all_media();
        $img_prefix = "http://202.114.20.78/" . DIR_IN_ROOT .  "/public/article_img/";
        foreach ($media as $m){
            if (isset($m['media_img'])){
                $a['media_img'] = $img_prefix . $m['media_img'];
            }
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($media));
    }

    public function set_media_avatar(){
        global $config;
        if (METHOD == 'post'){
            $result['status'] = 'success';
            $result['message'] = '';
            if(is_uploaded_file($_FILES['sicun-avatar']['tmp_name']))
            {
                $post_data = $this->input->post();
                $media_name = $post_data['media_name'];

                //判断上传文件是否允许
                $file_arr = pathinfo($_FILES['sicun-avatar']['name']);
                $file_type = $file_arr["extension"];
                $random_file_name = uniqid() . "." . $file_type;
                $file_abs = $config["img_dir"] . "/" . $random_file_name;
                $file_host = STATIC_PATH . $file_abs;
                try {
                    if(move_uploaded_file($_FILES['sicun-avatar']['tmp_name'], $file_host))
                    {
                        //把media的media_avatar更新
                        $this->media->update_avatar($media_name, $random_file_name);
                        $result['data'] = $random_file_name;
                    }
                } catch (Exception $e){
                    $result['status'] = 'error';
                    $result['message'] = $e->getMessage();
                }
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}