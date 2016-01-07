<!doctype html>
<html>
<?php $this->load->view('common/admin_header') ?>
<div class="container">
    <div class="header-section">
        <div class="fl header-title">文章编辑</div>
        <div class="fr">
            <span id="back-to-articles"><a href="<?php echo site_url() ?>/admin/index/#/articles">返回所有文章</a></span>
        </div>
        <div style="clear: both;"></div>
    </div>
    <div class="content">
        <?php $this->load->view("common/alert") ?>
        <div class="article-img-upload">
            <form>
                <input name="article_id" type="hidden" value="<?php if(isset($article)) echo $article['id'] ?>">
                <label>文章大图: </label>
                <input type="hidden" name="article_img_name">
                <input name="article_img" type="file" style="display: inline-block;">
                <button type="button" class="upload-file-btn btn btn-success">上传</button>
                <div class="article-img-wrapper">
                    <img <?php if(!isset($article['article_img']) || $article['article_img'] == '') echo "style=\"display: none;\""; ?>
                         class="article-img"
                         src="<?php if(isset($article['article_img'])) echo $article['article_img']; ?>"
                    >
                </div>
            </form>
        </div>
        <textarea name="article_content" id="rich-editor"><?php if(isset($article)) {echo $article['article_content'];}?></textarea>
        <div>
            <button type="button" id="edit-article-submit" class="btn btn-primary">保存更改</button>
        </div>
    </div>
</div>