create database if not exists zuiwan_m character set utf8;
use zuiwan_m;

# user table
create table if not exists user (
    id int NOT NULL AUTO_INCREMENT,
    username varchar(20) NOT NULL,
    password varchar(32) NOT NULL ,
    user_avatar varchar(50), #用户头像图片地址
    create_time varchar(30) NOT NULL, # time of this user been creates, date(Y:m:d H:i:s)
    collect_article text, #收藏的文章， 使用id， 比如 1,2,3说明收藏了1,2,3这三篇文章
    collect_media text, #收藏的媒体，使用逗号分隔
    PRIMARY KEY (id)
) default charset=utf8;

# 文章表
create table if not exists article (
    id int NOT NULL AUTO_INCREMENT,
    article_title varchar(30) NOT NULL,
    article_intro varchar(50), #文章简介
    article_content varchar(10000) NOT NULL, #内容
    article_author varchar(20) NOT NULL, #作者
    article_media int NOT NULL , #媒体id
    article_media_name VARCHAR(30) NOT NULL,
    article_topic int NOT NULL, #专题id
    article_topic_name VARCHAR(30) NOT NULL, #专题id
    create_time varchar(30) NOT NULL, # 发布时间
    article_img VARCHAR(40) NOT NULL , #文章展示图片,可选
    is_recommend int DEFAULT 0, #是否推荐,默认不推荐
    article_color VARCHAR(40) NOT NULL, #文章颜色
    is_banner int DEFAULT 0, #是否是banner
    visit_count int DEFAULT 1,
    PRIMARY KEY (id)
) default charset=utf8;

#媒体表
create table if not exists media (
    id int NOT NULL AUTO_INCREMENT,
    media_name varchar(30) NOT NULL, #媒体名称
    media_intro VARCHAR(80) NOT NULL, #媒体简介
    media_avatar varchar(40) NOT NULL, #媒体头像
    PRIMARY KEY (id)
) default charset=utf8;

#专题表
create table if not exists topic (
    id int NOT NULL AUTO_INCREMENT,
    topic_name varchar(30) NOT NULL, #专题名称
    topic_intro VARCHAR(80) NOT NULL , #专题简介
    topic_img varchar(40) NOT NULL , #专题大图
    PRIMARY KEY (id)
) DEFAULT CHARSET=utf8;

#后台管理员
create table if not exists admin(
    id int not null auto_increment,
    username varchar(10) NOT NULL ,
    password varchar(10) NOT NULL,
    PRIMARY KEY (id)
)default charset = utf8;

#token
create table if not exists token(
    username VARCHAR(20) NOT NULL,
    token VARCHAR(32) NOT NULL ,
    expire_time int NOT NULL , #strtotime seconds
    PRIMARY KEY (username)
)default charset = utf8;