<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/5
 * Time: 14:55
 */

class Article extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('mod_article', 'article');
        $this->article->init($this->db);
    }

}