<?php
/**
 * Created by PhpStorm.
 * User: libingtao
 * Date: 15/12/9
 * Time: 下午3:39
 */
?>
<head>
    <meta charset="UTF-8">
    <title>醉晚后台管理系统</title>
    <?php $this->load->view('common/bootstrap');?>
    <link rel="stylesheet" type="text/css" href="<?php if(DIR_IN_ROOT) echo '/' . DIR_IN_ROOT ?>/public/styles/admin.css" />
    <script src="<?php if(DIR_IN_ROOT) echo '/' . DIR_IN_ROOT ?>/public/third_part/ckeditor/ckeditor.js"></script>
    <style>
        .none {
            display: none;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="header-main">
        <div class="fl dian-logo"><img src="<?php echo base_url() ?>/public/img/avatar.png" height="42px;"></div>
        <div class="fr logout white-on-green" title="登出"><span class="glyphicon glyphicon-log-out"></span></div>
        <div class="fr split">/</div>
        <div class="fr identify">Hi, <span>李冰涛</span></div>
        <div class="clear"></div>
    </div>
</div>
