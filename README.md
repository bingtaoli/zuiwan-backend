

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

* /get_article -> 返回所有文章
* /get_article?type=1&name=思存 -> 返回媒体为“思存”的所有文章
* get_article?type=2&name=艺术殿堂 -> 返回专题为“艺术殿堂”的所有文章

/article/get_one_article

* get_one_article?id=1 -> 返回文章id为1的某篇文章

### 获取媒体列表

/media/get_media

获取所有媒体，包括媒体大图

### 获取专题

/type/get_topic

获取所有专题，包括大图

### 获取用户收藏的媒体

/user/get_collect_media?username=123

获取用户名为123的所有收藏的媒体

### 获取用户收藏的文章

/user/get_collect_article?username=123

获取用户名为123的所有收藏的文章