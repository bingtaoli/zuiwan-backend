<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/9
 * Time: 下午3:38
 */
?>
<!doctype html>
<html>
<?php $this->load->view('common/admin_header') ?>
<div class="container" style="margin-top: 20px;">
    <p>文章编辑:</p>
    <div class="content">
        <div class="alert alert-warning alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="message"></span>
        </div>
        <div class="alert alert-info alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="message"></span>
        </div>
        <textarea id="rich-editor"><?php if(isset($article)) {echo $article['article_content'];}?></textarea>
        <div style="margin-top: 10px;">
            <button id="publish" class="btn btn-primary">保存更改</button>
        </div>
    </div>
    <?php $this->load->view('common/admin_js') ?>
</div>