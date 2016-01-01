<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/21
 * Time: 下午12:26
 * 专题 Type
 */
class Type extends MY_Controller
{
    var $input;
    var $article;
    var $type;
    var $output;
    var $config;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        $this->load->model('mod_type', 'type');
    }

    public function get_all_type(){
        $type = $this->type->get_all_type();
        $img_prefix = "http://202.114.20.78/" . DIR_IN_ROOT .  "/public/upload/img/";
        foreach ($type as $m){
            if (isset($m['type_img'])){
                $a['type_img'] = $img_prefix . $m['type_img'];
            }
        }
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($type));
    }

    public function set_type_img(){
        if (METHOD == 'post'){
            $result['status'] = 'success';
            $result['message'] = '';
            if(is_uploaded_file($_FILES['avatar']['tmp_name']))
            {
                $post_data = $this->input->post();
                $type_name = $post_data['type_name'];

                //判断上传文件是否允许
                $file_arr = pathinfo($_FILES['avatar']['name']);
                $file_type = $file_arr["extension"];
                $random_file_name = uniqid() . "." . $file_type;
                $file_abs = $this->config->config["img_dir"] . "/" . $random_file_name;
                $file_host = STATIC_PATH . $file_abs;
                try {
                    if(move_uploaded_file($_FILES['avatar']['tmp_name'], $file_host))
                    {
                        //把以前的图片删除
                        $type = $this->type->get_type_by_name($type_name);
                        $origin = $type['type_img'];
                        $origin_pos = STATIC_PATH . $this->config->config['img_dir'] . "/" . $origin;
                        unlink($origin_pos);

                        //把type的type_avatar更新
                        $this->type->update_type_img($type_name, $random_file_name);
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

    public function add_type(){
        if (METHOD == 'post'){
            $post_data = $this->input->post();
            $type_name = $post_data['type_name'];
            $data = [
                'type_name'   => $type_name,
                'create_time' => date('Y-m-d'),
                'type_img'    => "default_type_img.jpg",
            ];
            $result = [
                'status' => 'success',
                'message' => '',
            ];
            try {
                $this->type->add_type($data);
            } catch (Exception $e){
                $result['message'] = '未知错误，请联系管理员';
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

    public function del_type(){
        if (METHOD == 'post') {
            $result['status'] = 'success';
            $result['message'] = '';
            try {
                $post_data = $this->input->post();
                $id = $post_data['id'];
                $this->type->del_type($id);
            } catch (Exception $e) {
                $result['message'] = $e->getMessage();
                $result['status'] = 'error';
            }
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($result));
        }
    }

}
