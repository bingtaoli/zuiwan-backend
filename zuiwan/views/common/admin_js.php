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
        'removePlugins': 'elementspath'
    };
    window.editor = CKEDITOR.replace('rich-editor', config);
    $('#publish').on('click', function(){
        var article_content = editor.getData();
        var article_type = 1;
        var article_author = 'test';
        var article_source = '思存';
        var data = {
            'article_content': article_content,
            'article_type': article_type,
            'article_author': article_author,
            'article_source': article_source
        };
        var url = "<?php echo site_url() ?>/admin/add_article";
        $.ajax({
            type: "POST",
            url: url,
            dataType: "json",
            data: data,
            timeout : 8000,  // 8s超时时间
            success: function (json) {
                if (json.status == 'success'){
                    _show_alert_message("发布文章成功", 1);
                } else if (json.status == 'error'){
                    _show_alert_message("发布文章失败 " + json.message, 2);
                }
            },
            error: function (e) {
                _show_alert_message("发布文章失败 " + e.message, 2);
            }
        });
    });
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
    $('.edit-or-del .glyphicon-edit').on('click', function(){
        var id = $(this).parents('tr').find('td').eq(0).text();
        var url = "<?php echo site_url() ?>/admin/edit_article/" + id;
        window.location.href = url;
    });
</script>
