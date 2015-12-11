create database if not exists zuiwan_m character set utf8;
use zuiwan_m;

# user table
create table if not exists user (
    id int NOT NULL AUTO_INCREMENT,
    username varchar(10) NOT NULL,
    password varchar(20) NOT NULL ,
    identify int NOT NULL, # 身份，用户或者管理员
    user_avatar varchar(50), #用户头像图片地址
    create_time varchar(30) NOT NULL, # time of this user been creates, date(Y:m:d H:i:s)
    collect_article varchar(1000), #收藏的文章， 使用id， 比如 1,2,3说明收藏了1,2,3这三篇文章
    collect_media varchar(200), #收藏的媒体，使用逗号分隔
    PRIMARY KEY (id)
) default charset=utf8;

# 文章表
create table if not exists article (
    id int NOT NULL AUTO_INCREMENT,
    article_type varchar(10) NOT NULL,
    article_content varchar(10000) NOT NULL, #内容
    article_author varchar(20) NOT NULL, #作者
    article_source varchar(100), #转自，可以为空
    create_time varchar(30) NOT NULL, # 发布时间
    PRIMARY KEY (id)
) default charset=utf8;

#媒体表
create table if not exists media (
    id int NOT NULL AUTO_INCREMENT,
    media_name varchar(10) NOT NULL, #媒体名称
    media_avatar varchar(20), #媒体头像
    create_time varchar(30) NOT NULL, # 发布时间
    PRIMARY KEY (id)
) default charset=utf8;