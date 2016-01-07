$(function(){
    //tab_switch_init();
    editor_init();
    article_init();
    media_init();
    topic_init();
});

//function tab_switch_init(){
//    $('ul.tab-items li').on('click', function () {
//        var index = $(this).index();
//        $(this).addClass('active');
//        $(this).siblings().removeClass('active');
//        var section = $('#admin-panel').children().eq(index);
//        $(section).removeClass('none');
//        $(section).siblings().addClass('none');
//    });
//}

function editor_init(){
    var config = {
        'toolbar': [['Bold', 'Italic', 'Strike', 'Format', 'NumberedList', 'BulletedList', 'Image', 'PasteFromWord',
            'Table', 'HorizontalRule', 'SpecialChar', 'Undo', 'Redo', 'Maximize']],
        'uiColor': '#FAFAFA',
        'removePlugins': 'elementspath',
        'width': 900,
        'height': 550
    };
    window.editor = CKEDITOR.replace('rich-editor', config);
}

function article_init(){
    var all_article = $("#all-article");
    if (all_article == undefined){
        return;
    }
    /**
     * 所有文章板块: 文章编辑
     */
    $(all_article).on('click', '.glyphicon-edit', function(){
        var id = $(this).parents('tr').find('td[name="article_id"]').text();
        var url = window.site_uri + "/article/edit_article?id=" + id;
        window.location.href = url;
    });
    /**
     * 所有文章板块: 文章删除
     */
    $(all_article).on('click', '.glyphicon-remove', function(){
        var id = $(this).parents('tr').find('td[name="article_id"]').text();
        var url = "<?php echo site_url() ?>/article/del_article/";
        var tr = $(this).parents('tr');
        var data = {
            'id': id
        };
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    _show_alert_message("删除文章成功", 1);
                    $(tr).remove();
                } else if (json.status == 'error'){
                    _show_alert_message("删除文章失败 " + (json.message ? json.message : ''), 2);
                }
            },
            error: function (e) {
                _show_alert_message("删除文章失败 " + (e.message ? e.message : ''), 2);
            }
        });
    });
    /**
     * 发布文章界面: 文章大图上传
     */
    $(".article-img-upload").on("click", ".upload-file-btn", function (e) {
        e.preventDefault();
        var form = $(this).parents('form');
        var formData = new FormData($(form)[0]);
        var url = window.site_uri + "/article/set_article_img/";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("success");
                    $(form).find('img').attr("src", window.base_uri + "/public/upload/img/" + json.data).show();
                    //设置article img name
                    $(form).find("[name='article_img_name']").val(json.data);
                } else if (json.status == 'error'){
                    console.log(json.message);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
    /**
     * 发布文章界面: 增加文章
     */
    $("#add-article-submit").on("click", function(){
        editor.updateElement();
        var form = $(this).parents(".form");
        var url =  window.site_uri + "/article/add_article/";
        var data = {};
        data.article_title = $(form).find("[name='article_title']").val();
        data.article_author = $(form).find("[name='article_author']").val();
        data.article_media = $(form).find("[name='article_media']").val();
        data.article_topic = $(form).find("[name='article_topic']").val();
        data.article_intro = $(form).find("[name='article_intro']").val();
        data.article_img_name = $(form).find("[name='article_img_name']").val();
        data.article_content = $(form).find("[name='article_content']").val();
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("add article success");
                    location.reload(true);
                } else if (json.status == 'error'){
                    console.log("add article fail");
                }
            },
            error: function () {
                console.log("add article fail");
            }
        });
    });
    /**
     * 编辑文章界面: 编辑保存文章
     */
    $("#edit-article-submit").on("click", function(){
        editor.updateElement();
        var url =  window.site_uri + "/article/edit_article/";
        var data = {};
        data.id = $("[name='article_id']").val();
        data.article_content = $("[name='article_content']").val();
        data.article_img = $("[name='article_img_name']").val();
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("edit article success");
                    location.reload(true);
                } else if (json.status == 'error'){
                    console.log("edit article fail");
                }
            },
            error: function (e) {
                console.log("edit article fail", e.message);
            }
        });
    });
}

function media_init(){
    var media_manage = $('#media-manage');
    if (media_manage == undefined){
        return;
    }
    /**
     * 上传媒体头像
     */
    $(media_manage).on('click', '.upload-file-btn', function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        var formData = new FormData($(form)[0]);
        var url = window.site_uri + "/media/set_media_avatar/";
        var tr = $(this).parents('tr');
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("success");
                    $(tr).find('img').attr("src", window.base_uri + "public/upload/img/" + json.data);
                } else if (json.status == 'error'){
                    console.log(json.message);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
    /**
     * 新增媒体(弹出弹窗)
     */
    $(media_manage).on('click', '.more', function(){
        $('#add-media-modal').modal();
    });
    /**
     * 新增媒体(弹窗确定新增)
     */
    $('#add-media-confirm-btn').on('click', function(){
        var modal = $('#add-media-modal');
        var data = {};
        data.media_name = $(modal).find('[name="media_name"]').val();
        data.media_intro = $(modal).find('[name="media_intro"]').val();
        var url = window.site_uri +  "/media/add_media/";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("success");
                    var id = json.data;
                    //增加一行
                    var clone = $(".media-template:hidden").clone();
                    clone.show();
                    $(clone).find("td").eq(0).text(data.media_name);
                    $(clone).find("td").last().text(id);
                    clone.removeClass("media-template");
                    $(media_manage).find('tbody').append(clone);
                    modal.modal('hide');
                } else if (json.status == 'error'){
                    console.log(json.message);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
    /**
     * 删除媒体
     */
    $(media_manage).on('click', '.glyphicon-remove', function(){
        var id = $(this).parents('tr').find('td[name="id"]').text();
        var url = window.site_uri + "/media/del_media/";
        var tr = $(this).parents('tr');
        var data = {
            'id': id
        };
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    _show_alert_message("删除媒体成功", 1);
                    $(tr).remove();
                } else if (json.status == 'error'){
                    _show_alert_message("删除媒体失败 " + (json.message ? json.message : ''), 2);
                }
            },
            error: function (e) {
                _show_alert_message("删除媒体失败 " + (e.message ? e.message : ''), 2);
            }
        });
    });
}

function topic_init(){
    var topic_manage =  $('#topic-manage');
    if (topic_manage == undefined){
        return;
    }
    /**
     * 媒体专题模块---上传专题大图
     */
    $(topic_manage).on('click', '.upload-file-btn', function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        var formData = new FormData($(form)[0]);
        var url = window.site_uri + "/topic/set_topic_img/";
        var tr = $(this).parents('tr');
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("success");
                    $(tr).find('img').attr("src", window.base_uri + "/public/upload/img/" + json.data);
                } else if (json.status == 'error'){
                    console.log(json.message);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
    /**
     * 新增专题(弹出弹窗)
     */
    $(topic_manage).on('click', '.more', function(){
        $('#add-topic-modal').modal();
    });
    /**
     * 新增专题,弹出弹窗确认
     */
    $('#add-topic-confirm-btn').on('click', function(){
        var modal = $('#add-topic-modal');
        var data = {};
        data.topic_name = $(modal).find('[name="topic_name"]').val();
        data.topic_intro = $(modal).find('[name="topic_intro"]').val();
        var url = window.site_uri + "/topic/add_topic/";
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("success");
                    var id = json.data;
                    //增加一行
                    var clone = $(".topic-template:hidden").clone();
                    clone.show();
                    clone.removeClass("topic-template");
                    $(clone).find("td").eq(0).text(data.topic_name);
                    $(clone).find("td").last().text(id);
                    $(topic_manage).find('tbody').append(clone);
                    modal.modal('hide');
                } else if (json.status == 'error'){
                    console.log(json.message);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
    /**
     * 删除专题
     */
    $(topic_manage).on('click', '.glyphicon-remove', function(){
        var id = $(this).parents('tr').find('td[name="id"]').text();
        var url = window.site_uri + "/topic/del_topic/";
        var tr = $(this).parents('tr');
        var data = {
            'id': id
        };
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    _show_alert_message("删除专题成功", 1);
                    $(tr).remove();
                } else if (json.status == 'error'){
                    _show_alert_message("删除专题失败 " + (json.message ? json.message : ''), 2);
                }
            },
            error: function (e) {
                _show_alert_message("删除文章失败 " + (e.message ? e.message : ''), 2);
            }
        });
    });
}

/**
 * 弹出提醒消息
 */
function _show_alert_message(message_content, type){
    $('.alert').hide();
    var message;
    if (type == 1){
        message = $('.alert-info')[0];
    } else if (type == 2){
        message = $('.alert-warning')[0];
    } else {
        return;
    }
    var clone_message = $(message).clone();
    $(clone_message).find('.message').text(message_content);
    $('.content').prepend(clone_message);
    $(clone_message).show();
}