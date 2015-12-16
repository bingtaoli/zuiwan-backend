<?php
/**
 * Created by PhpStorm.
 * User: bingtao
 * Date: 2015/12/6
 * Time: 13:35
 */
?>

<!doctype html>
<html>

<?php $this->load->view('common/admin_header') ?>
<style>
    tbody tr td.edit-or-del {
        border: none;
        border-right: solid 1px #fff;
        border-bottom: solid 1px #fff;
        background: #fff;
    }
    tbody tr td.edit-or-del .glyphicon {
        display: inline-block;
        line-height: 34px;
        width: 24px;
        cursor: pointer;
    }
</style>
<div class="container">
    <div class="content">
        <div class="alert alert-warning alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="message"></span>
        </div>
        <div class="alert alert-info alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span class="message"></span>
        </div>
        <ul class="tab-items">
            <li class="active">发布文章</li>
            <li >所有文章</li>
            <div class="clear"></div>
        </ul>
        <div id="admin-panel">
            <div id="add-article">
                <form enctype="multipart/form-data" action="<?php echo site_url() ?>/admin/add_article" method="post">
                    <div class="form-group" style="width: 70%">
                        <label>文章作者</label>
                        <input type="text" class="form-control" name="article_author" placeholder="">
                    </div>
                    <div class="form-group" style="width: 70%">
                        <label>文章来源</label>
                        <input type="text" class="form-control" name="article_source" placeholder="原创">
                    </div>
                    <div class="form-group" style="width: 70%">
                        <label>文章类型</label>
                        <select class="form-control" name="article_type">
                            <option>新闻</option>
                            <option>搞笑</option>
                            <option>文艺</option>
                            <option>故事</option>
                            <option>体育</option>
                        </select>
                    </div>
                    <div>
                        <label>文章大图: </label>
                        <input name="article_img" type="file" style="display: inline-block;">
                    </div>
                    <div class="form-group" style="width: 70%">
                        <label>文章简介</label>
                        <input type="text" class="form-control" name="article_intro" placeholder="">
                    </div>
                    <textarea name="article_content" id="rich-editor" rows="16"></textarea>
                    <div style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary">发布</button>
                    </div>
                </form>
            </div>
            <div id="all-article" class="none">
                <table class="can-more table table-bordered table-striped">
                    <colgroup>
                        <col class="col-xs-1">
                        <col class="col-xs-2">
                        <col class="col-xs-1">
                        <col class="col-xs-4">
                        <col class="col-xs-3">
                        <col class="col-xs-1">
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="none"></th>
                        <th>作者</th>
                        <th>类型</th>
                        <th>简介</th>
                        <th>来源</th>
                        <th class="none"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($articles)){
                    foreach ($articles as $a){
                    ?>
                    <tr>
                        <td name="article_id" class="none"><?php echo $a['id'] ?></td>
                        <th><?php echo $a['article_author'] ?></th>
                        <td><?php echo $a['article_type'] ?></td>
                        <td><?php echo $a['article_intro'] ?></td>
                        <td><?php echo $a['article_source'] ?></td>
                        <td class="edit-or-del">
                            <span class="glyphicon glyphicon-edit"></span>
                            <span class="glyphicon glyphicon-remove"></span>
                        </td>
                    </tr>
                    <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/admin_js') ?>
</div>