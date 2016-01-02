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
<style>
    .my-header {
        margin: 10px 0 10px;
        border-bottom: 1px solid #eee;
    }
</style>
<div class="container">
    <div class="my-header"><h2>文章编辑</h2></div>
    <div class="content">
        <div class="alert alert-warning alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="message"></span>
        </div>
        <div class="alert alert-info alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="message"></span>
        </div>
        <div>
            <input name="article_id" type="hidden" value="<?php if(isset($article)) echo $article['id'] ?>">
            <label>文章大图: </label>
            <input type="hidden" name="article_img_name">
            <input name="article_img" type="file" style="display: inline-block;">
            <button type="button" class="upload-file-btn btn btn-success">上传</button>
            <div style="margin-top: 5px; margin-bottom: 8px;">
                <img <?php if(!isset($article['article_img']) || $article['article_img'] == '') echo "style=\"display: none;\""; ?>
                     style="width: 400px;"
                     src="<?php if(isset($article['article_img'])) echo $article['article_img']; ?>"
                >
            </div>
        </div>
        <textarea name="article_content" id="rich-editor"><?php if(isset($article)) {echo $article['article_content'];}?></textarea>
        <div style="margin-top: 10px;">
            <button type="button" id="edit-article-submit" class="btn btn-primary">保存更改</button>
        </div>
    </div>
    <?php $this->load->view('common/admin_js') ?>
</div>