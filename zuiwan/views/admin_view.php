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
    input[type="file"] {
        display: inline-block;
        margin-left: 16px;
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
            <li>所有文章</li>
            <li>媒体专题</li>
            <div class="clear"></div>
        </ul>
        <div id="admin-panel">
            <div id="add-article">
                <form enctype="multipart/form-data" action="<?php echo site_url() ?>/article/add_article" method="post">
                    <div class="form-group" style="width: 70%">
                        <label>文章标题</label>
                        <input type="text" class="form-control" name="article_title" placeholder="">
                    </div>
                    <div class="form-group" style="width: 70%">
                        <label>文章作者</label>
                        <input type="text" class="form-control" name="article_author" placeholder="">
                    </div>
                    <div class="form-group" style="width: 70%">
                        <label>文章媒体</label>
                        <select class="form-control" name="article_source">
                            <option >思存</option>
                            <option>醉晚</option>
                            <option>新闻中心</option>
                            <option>华科学生会</option>
                        </select>
                    </div>
                    <div class="form-group" style="width: 70%">
                        <label>所属专题</label>
                        <select class="form-control" name="article_type">
                            <option value="1">艺术殿堂</option>
                            <option value="2">搞笑</option>
                            <option value="3">文艺</option>
                            <option value="4">故事</option>
                            <option value="5">体育</option>
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
                    <textarea name="article_content" id="rich-editor" rows="30"></textarea>
                    <div style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary">发布</button>
                    </div>
                </form>
            </div>
            <div id="all-article" class="none">
                <table class="can-more table table-bordered table-striped">
                    <colgroup>
                        <col class="col-xs-1">
                        <col class="col-xs-1">
                        <col class="col-xs-1">
                        <col class="col-xs-1">
                        <col class="col-xs-4">
                        <col class="col-xs-3">
                        <col class="col-xs-1">
                    </colgroup>
                    <thead>
                    <tr>
                        <th class="none"></th>
                        <th>标题</th>
                        <th>作者</th>
                        <th>专题</th>
                        <th>简介</th>
                        <th>媒体</th>
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
                        <th><?php echo $a['article_title'] ?></th>
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
            <div id="media-and-type" class="none">
                <div id="media">
                    <h2>媒体</h2>
                    <table class="table table-bordered table-striped">
                        <colgroup>
                            <col class="col-xs-1">
                            <col class="col-xs-2">
                            <col class="col-xs-3">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="none"></th>
                            <th>媒体名称</th>
                            <th>媒体头像</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="none"></td>
                            <th>思存工作室</th>
                            <th>
                                <form enctype="multipart/form-data" method="post">
                                    <img src="/<?php echo DIR_IN_ROOT ?>/public/upload/img/default_media_avatar.jpg">
                                    <input class="file" type="file" name="sicun-avatar">
                                    <input name="media_name" type="hidden" value="sicun">
                                    <button class="upload-file-btn btn btn-success">上传</button>
                                </form>
                            </th>
                            <td class="edit-or-del">
                                <span class="glyphicon glyphicon-remove"></span>
                            </td>
                        </tr>
                        <tr>
                            <td name="" class="none"></td>
                            <th>醉晚亭</th>
                            <th>
                                <form enctype="multipart/form-data" method="post">
                                    <img src="/<?php echo DIR_IN_ROOT ?>/public/upload/img/default_media_avatar.jpg">
                                    <input class="file" type="file" name="sicun-avatar">
                                    <input name="media_name" type="hidden" value="zuiwan">
                                    <button class="upload-file-btn btn btn-success">上传</button>
                                </form>
                            </th>
                            <td class="edit-or-del">
                                <span class="glyphicon glyphicon-remove"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <hr>
                <div id="type">
                    <h2>专题</h2>
                    <table class="table table-bordered table-striped">
                        <colgroup>
                            <col class="col-xs-1">
                            <col class="col-xs-2">
                            <col class="col-xs-3">
                            <col class="col-xs-1">
                        </colgroup>
                        <thead>
                        <tr>
                            <th class="none"></th>
                            <th>专题名称</th>
                            <th>专题大图</th>
                            <th class="none"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td name="" class="none"></td>
                            <th>艺术殿堂</th>
                            <th>
                                无<input type="file">
                            </th>
                            <td class="edit-or-del">
                                <span class="glyphicon glyphicon-remove"></span>
                            </td>
                        </tr>
                        <tr>
                            <td name="" class="none"></td>
                            <th>体育</th>
                            <th>
                                无<input type="file">
                            </th>
                            <td class="edit-or-del">
                                <span class="glyphicon glyphicon-remove"></span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view('common/admin_js') ?>
</div>