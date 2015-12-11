<?php
/*
CKEditor_upload.php
monkee
2009-11-15 16:47
*/
$config=array();
$config['type']=array("flash","img"); //上传允许type值
$config['img']=array("jpg","bmp","gif","png"); //img允许后缀
$config['flash']=array("flv","swf"); //flash允许后缀
$config['flash_size']=200; //上传flash大小上限 单位：KB
$config['img_size']=5000; //上传img大小上限 单位：KB
$config['message']="上传成功"; //上传成功后显示的消息，若为空则不显示
$config['name']=uniqid(); //上传后的文件命名规则 这里以unix时间戳来命名
$config['flash_dir']=""; //上传flash文件地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/"
$config['img_dir']= "upload/img"; //上传img文件地址 采用绝对地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/"
/**
 *  TODO 这里图片路径地址还有部分未明确，后续优化
 */
$config['site_url']="/zuiwan-m/public/"; //网站的网址 这与图片上传后的地址有关 最后不加"/" 可留空

class Upload extends MY_Controller {

    function upload_file()
    {
        global $config;
        //判断是否是非法调用
        if(empty($_GET['CKEditorFuncNum']))
            $this->_mkhtml(1,"","错误的功能调用请求");
        $fn=$_GET['CKEditorFuncNum'];
//        if(!in_array($_GET['type'],$config['type']))
//            $this->_mkhtml(1,"","错误的文件调用请求");
        $type = 'img';
        if(is_uploaded_file($_FILES['upload']['tmp_name']))
        {
            //判断上传文件是否允许
            $filearr=pathinfo($_FILES['upload']['name']);
            $filetype=$filearr["extension"];
            if(!in_array($filetype, $config[$type])){
            	$this->_mkhtml($fn,"","错误的文件类型！");
            }
            //判断文件大小是否符合要求
            if($_FILES['upload']['size']>$config[$type."_size"]*1024){
            	$this->_mkhtml($fn,"","上传的文件不能超过".$config[$type."_size"]."KB！");
            }
            //$filearr=explode(".",$_FILES['upload']['name']);
            //$filetype=$filearr[count($filearr)-1];
            $file_abso=$config[$type."_dir"]."/".$config['name'].".".$filetype;
            $file_host= STATIC_PATH . $file_abso;
            echo $file_host . '\n';
            echo $_FILES['upload']['tmp_name'] . '\n' ;
            if(move_uploaded_file($_FILES['upload']['tmp_name'],$file_host))
            {
                $this->_mkhtml($fn, $config['site_url'].$file_abso, $config['message']);
            }
            else
            {
                echo($_FILES['upload']['tmp_name']);
                echo($file_host);
                $this->_mkhtml($fn,"","文件上传失败，请检查上传目录设置和目录读写权限");
            }
        }
    }
    //输出js调用
    private function _mkhtml($fn,$fileurl,$message)
    {
        $str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
        exit($str);
    }
}
