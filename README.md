### 收藏文章

/user/collect_article

post参数:

* article_id
* username

返回:

json: status, message

### 收藏媒体

user/collect_media

post参数:

* media_id
* username

返回:

json: status, message

### 获取文章

/article/get_article

参数：

* get_article()返回所有文章
* get_article(1, "思存")返回媒体为“思存”的所有文章
* get_article(2, "艺术殿堂")返回专题为“艺术殿堂”的所有文章

/article/get_one_article

* get_one_article(1) 返回文章id为1的某篇文章
