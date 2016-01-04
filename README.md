

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

**/article/get_article**

* /get_article -> 返回所有文章
* /get_article?type=1&id=1 -> 返回媒体id为1的所有文章
* get_article?type=2&id=1 -> 返回专题id为1的所有文章

**/article/get_one_article**

* get_one_article?id=1 -> 返回文章id为1的某篇文章

返回的json数组每一项都有多个字段:

* article_media: 媒体id
* article_topic: 话题id
* article_media_name: 媒体名称
* article_topic_name: 话题名称

### 获取媒体列表

/media/get_media

获取所有媒体，包括媒体大图

### 获取专题

**/topic/get_topic**

获取所有专题，包括大图

**/topic/get_one_topic**

* get_one_topic?id=1 返回topic id为1的专题信息

返回json每一项字段：

* article_count 该专题文章总数

### 获取用户收藏的媒体

/user/get_collect_media?username=123

获取用户名为123的所有收藏的媒体

### 获取用户收藏的文章

/user/get_collect_article?username=123

获取用户名为123的所有收藏的文章