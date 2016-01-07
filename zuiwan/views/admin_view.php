<!doctype html>
<html>
<?php $this->load->view('common/admin_header') ?>
<div class="container">
    <div class="content">
        <?php $this->load->view("common/alert") ?>
        <ul class="tab-items">
            <li class="active"><a href="#/">发布文章</a></li>
            <li><a href="#/articles">所有文章</a></li>
            <li><a href="#/media-topic">媒体专题</a></li>
            <div class="clear"></div>
        </ul>
        <div id="publish" class="shadow active">
            <div>
                <div class="form">
                    <div class="form-group">
                        <label>文章标题</label>
                        <input type="text" class="form-control" name="article_title" placeholder="">
                    </div>
                    <div class="form-group">
                        <label>文章作者</label>
                        <input type="text" class="form-control" name="article_author" placeholder="">
                    </div>
                    <div class="form-group">
                        <label>文章媒体</label>
                        <select class="form-control" name="article_media">
                            <?php if(isset($media)) foreach($media as $m){ ?>
                                <option value="<?php echo $m['id'] ?>"><?php echo $m['media_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>所属专题</label>
                        <select class="form-control" name="article_topic">
                            <?php if(isset($topic)) foreach($topic as $t){ ?>
                                <option value="<?php echo $t['id'] ?>"><?php echo $t['topic_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="article-img-upload">
                        <form>
                            <label>文章大图: </label>
                            <input type="hidden" name="article_img_name">
                            <input name="article_img" type="file" style="display: inline-block;">
                            <button type="button" class="upload-file-btn btn btn-success">上传</button>
                            <div style="margin-top: 5px; margin-bottom: 8px;">
                                <img class="article-img" src="<?php echo base_url(); ?>public/upload/img/huge.jpg?>">
                            </div>
                        </form>
                    </div>
                    <div class="form-group">
                        <label>文章简介</label>
                        <input type="text" class="form-control" name="article_intro" placeholder="">
                    </div>
                    <textarea name="article_content" id="rich-editor" rows="30"></textarea>
                    <div style="margin-top: 10px;">
                        <button id="add-article-submit" type="submit" class="btn btn-primary">发布</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="articles" class="shadow">
            <div id="all-article">
                <table class="can-more table table-bordered table-striped">
                    <colgroup>
                        <col class="col-xs-2">
                        <col class="col-xs-1">
                        <col class="col-xs-1">
                        <col class="col-xs-4">
                        <col class="col-xs-2">
                        <col class="col-xs-1">
                        <col class="col-xs-1">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>标题</th>
                        <th>作者</th>
                        <th>专题</th>
                        <th>简介</th>
                        <th>媒体</th>
                        <th class="none"></th>
                        <th class="none"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($articles)){
                    foreach ($articles as $a){
                    ?>
                    <tr>
                        <th><?php if(isset($a['article_title'])) echo $a['article_title'] ?></th>
                        <th><?php if(isset($a['article_author'])) echo $a['article_author'] ?></th>
                        <td><?php if(isset( $a['article_topic'])) echo $a['article_topic'] ?></td>
                        <td><?php if(isset($a['article_intro'])) echo $a['article_intro'] ?></td>
                        <td><?php if(isset($a['article_media'])) echo $a['article_media'] ?></td>
                        <td class="edit-or-del">
                            <span class="glyphicon glyphicon-edit"></span>
                            <span class="glyphicon glyphicon-remove"></span>
                        </td>
                        <td name="article_id" class="none"><?php echo $a['id'] ?></td>
                    </tr>
                    <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="media-topic shadow">
            <div id="media-manage">
                <h2>媒体</h2>
                <table class="table table-bordered table-striped">
                    <colgroup>
                        <col class="col-xs-2">
                        <col class="col-xs-6">
                        <col class="col-xs-1">
                        <col class="col-xs-1">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>媒体名称</th>
                        <th>媒体头像(建议裁剪成正方形)</th>
                        <th class="none"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="none media-template">
                        <td></td>
                        <td>
                            <form enctype="multipart/form-data" method="post">
                                <img src="<?php echo base_url(); ?>public/upload/img/default_media_avatar.jpg">
                                <input class="file" type="file" name="avatar">
                                <input name="media_name" type="hidden" value="">
                                <button class="upload-file-btn btn btn-success">上传</button>
                            </form>
                        </td>
                        <td class="del">
                            <span class="glyphicon glyphicon-remove"></span>
                        </td>
                        <td name="id"></td>
                    </tr>
                    <?php if(isset($media)) foreach($media as $i => $m){ ?>
                    <tr>
                        <td><?php echo $m['media_name'] ?></td>
                        <td>
                            <form enctype="multipart/form-data" method="post">
                                <img src="<?php if (isset($m['media_avatar'])) echo $m['media_avatar'];?>">
                                <input class="file" type="file" name="avatar">
                                <input name="media_name" type="hidden" value="<?php echo $m['media_name'] ?>">
                                <button class="upload-file-btn btn btn-success">上传</button>
                            </form>
                        </td>
                        <td class="del">
                            <span class="glyphicon glyphicon-remove"></span>
                        </td>
                        <td class="none" name="id"><?php echo $m['id'] ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="white-on-green more glyphicon glyphicon-plus" title="增加一项"></div>
            </div>
            <hr>
            <div id="topic-manage">
                <h2>专题</h2>
                <table class="table table-bordered table-striped">
                    <colgroup>
                        <col class="col-xs-2">
                        <col class="col-xs-6">
                        <col class="col-xs-1">
                        <col class="col-xs-1">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>专题名称</th>
                        <th>专题大图(建议裁剪成正方形)</th>
                        <th class="none"></th>
                        <th class="none"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="none topic-template">
                        <td name="topic_name"></td>
                        <th>
                            <form enctype="multipart/form-data" method="post">
                                <img src="<?php echo base_url(); ?>public/upload/img/default_topic_img.jpg">
                                <input class="file" type="file" name="avatar">
                                <input name="topic_name" type="hidden">
                                <button class="upload-file-btn btn btn-success">上传</button>
                            </form>
                        </th>
                        <td class="del">
                            <span class="glyphicon glyphicon-remove"></span>
                        </td>
                        <td class="none" name="id"></td>
                    </tr>
                    <?php if(isset($topic)) foreach($topic as $i => $t){ ?>
                    <tr>
                        <th><?php echo $t['topic_name'] ?></th>
                        <th>
                            <form enctype="multipart/form-data" method="post">
                                <img src="<?php if (isset($t['topic_img'])) echo $t['topic_img']; ?>">
                                <input class="file" type="file" name="avatar">
                                <input name="topic_name" type="hidden" value="<?php echo $t['topic_name'] ?>">
                                <button class="upload-file-btn btn btn-success">上传</button>
                            </form>
                        </th>
                        <td class="del">
                            <span class="glyphicon glyphicon-remove"></span>
                        </td>
                        <td class="none" name="id"><?php echo $t['id']; ?></td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <div class="white-on-green more glyphicon glyphicon-plus" title="增加一项"></div>
            </div>
        </div>
    </div>
</div>
<div id="add-media-modal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">增加媒体</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>媒体名称</label>
                    <input name="media_name" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>媒体简介</label>
                    <input name="media_intro" type="text" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="add-media-confirm-btn" type="button" class="btn btn-primary">确定增加</button>
            </div>
        </div>
    </div>
</div>

<div id="add-topic-modal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">增加专题</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>专题名称</label>
                    <input name="topic_name" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>专题简介</label>
                    <input name="topic_intro" type="text" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="add-topic-confirm-btn" type="button" class="btn btn-primary">确定增加</button>
            </div>
        </div>
    </div>
</div>
<script src="https://rawgit.com/leeluolee/stateman/master/stateman.js"></script>
<script>
    var stateman = new StateMan();
    var shadows = $(".shadow");
    var lis = $(".tab-items").find("li");
    var publish = {
        enter: function(){
            $(shadows[0]).siblings().removeClass("active");
            $(shadows[0]).addClass("active");
            $(lis[0]).addClass("active");
            $(lis[0]).siblings().removeClass("active");
        }
    };
    var article = {
        enter: function(){
            $(shadows[1]).siblings().removeClass("active");
            $(shadows[1]).addClass("active");
            $(lis[1]).addClass("active");
            $(lis[1]).siblings().removeClass("active");
        }
    };
    var media_topic = {
        enter: function(){
            $(shadows[2]).siblings().removeClass("active");
            $(shadows[2]).addClass("active");
            $(lis[2]).addClass("active");
            $(lis[2]).siblings().removeClass("active");
        }
    };
    stateman
        .state('/', publish)
        .state('articles', article)
        .state('media-topic', media_topic)
        .start({});
</script>