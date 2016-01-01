<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/9
 * Time: 下午3:40
 */
?>
<script>
    $('ul.tab-items li').on('click', function () {
        var index = $(this).index();
        $(this).addClass('active');
        $(this).siblings().removeClass('active');
        var section = $('#admin-panel').children().eq(index);
        $(section).removeClass('none');
        $(section).siblings().addClass('none');
    });
    var config = {
        'toolbar': [['Bold', 'Italic', 'Strike', 'Format', 'NumberedList', 'BulletedList', 'Image', 'PasteFromWord',
            'Table', 'HorizontalRule', 'SpecialChar', 'Undo', 'Redo', 'Maximize']],
        'uiColor': '#FAFAFA',
        'removePlugins': 'elementspath',
        'width': 700,
        'height': 450
    };
    window.editor = CKEDITOR.replace('rich-editor', config);

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

    /**
     * 所有文章板块---文章编辑
     */
    $('#all-article').on('click', '.glyphicon-edit', function(){
        var id = $(this).parents('tr').find('td[name="article_id"]').text();
        var url = "<?php echo site_url() ?>/article/edit_article/" + id;
        window.location.href = url;
    });
    /**
     * 所有文章板块---文章删除
     */
    $('#all-article').on('click', '.glyphicon-remove', function(){
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
     * 媒体专题板块---上传媒体头像
     */
    $('#media-manage').on('click', '.upload-file-btn', function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        var formData = new FormData($(form)[0]);
        var url = "<?php echo site_url() ?>/media/set_media_avatar/";
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
                    $(tr).find('img').attr("src", "<?php if(DIR_IN_ROOT) echo '/' . DIR_IN_ROOT ?>/public/upload/img/" + json.data);
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
     * 媒体专题板块---新增媒体(弹出弹窗)
     */
    $('#media-manage').on('click', '.more', function(){
        $('#add-media-modal').modal();
    });
    /**
     * 媒体专题板块---新增媒体(弹窗确定新增)
     */
    $('#add-media-confirm-btn').on('click', function(){
        var media_name = $('#add-media-modal').find('[name="media_name"]').val();
        var url = "<?php echo site_url() ?>/media/add_media/";
        var data = {
            'media_name': media_name
        };
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("success");
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
     * 媒体专题板块---删除媒体
     */
    $('#media-manage').on('click', '.glyphicon-remove', function(){
        var id = $(this).parents('tr').find('td[name="id"]').text();
        var url = "<?php echo site_url() ?>/media/del_media/";
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

    /**
     * 媒体专题模块---上传专题大图
     */
    $('#type-manage').on('click', '.upload-file-btn', function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        var formData = new FormData($(form)[0]);
        var url = "<?php echo site_url() ?>/type/set_type_img/";
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
                    $(tr).find('img').attr("src", "<?php if(DIR_IN_ROOT) echo '/' . DIR_IN_ROOT ?>/public/upload/img/" + json.data);
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
     * 媒体专题板块---新增专题(弹出弹窗)
     */
    $('#type-manage').on('click', '.more', function(){
        $('#add-type-modal').modal();
    });
    /**
     * 媒体专题板块---新增专题,弹出弹窗确认
     */
    $('#add-type-confirm-btn').on('click', function(){
        var type_name = $('#add-type-modal').find('[name="type_name"]').val();
        var url = "<?php echo site_url() ?>/type/add_type/";
        var data = {
            'type_name': type_name
        };
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'JSON',
            data: data,
            timeout : 80000,  // 80s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    console.log("success");
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
     * 媒体专题板块---删除专题
     */
    $('#type-manage').on('click', '.glyphicon-remove', function(){
        var id = $(this).parents('tr').find('td[name="id"]').text();
        var url = "<?php echo site_url() ?>/type/del_type/";
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
</script>
