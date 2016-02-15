### 推荐页

* GET  article/get_recommend  

结果:

```
{
	'banner': [
		{
			'id': xxx,
			'article_title': xxx,
			'article_intro': xxx,
			'article_media_name': xxx,
			'article_topic_name': xxx,
			'article_img': xxx,
		},
		{
			//article 2
		},
		{
			//article 3
		},
	],
	'recommend': [
		{
			'id': xxx,
			'article_title': xxx,
			'article_media_name': xxx,
			'article_topic_name': xxx,
			'article_img': xxx,
			'article_color': '#dddddd',
		},
		{
			//...
		}
	]
}
```

### 获取专题

**GET /topic/get_topic**

结果：

```
[
	{
		'id': xxx,
		'topic_name': xxx,
		'topic_intro': xxx,
		'article_count': int,
		'topic_img': xxx,
	}
]
```

获取所有专题，包括大图

**GET /topic/get_one_topic**

请求:

```
{
	id: topic_id
}
```

结果:

```
{
	'topic_name': xxx,
	'topic_intro': xxx,
	'article_count': int,
	'topic_img': xxx,
	'articles': [
		{
			'id': xxx,
			'article_title': xxx,
			'article_intro': xxx,
			'article_media_name': xxx,
			'article_topic_name': xxx,
			'article_img': xxx,
			'create_time': '2015-1-1 12:00:00',
		},
		{
			//...
		}
	]
}
```

### 媒体列表

**GET /media/get_media**

结果:

```
[
	{
		'id': xxx,
		'media_name': xxx,
		'media_intro': xxx,
		'media_avatar': xxx,
	},
	{
		//...
	}
]
```

**GET /media/get\_one\_media**

请求:

```
{
	id: xxx,
}
```

结果:

```
{
	'media_name': xxx,
	'media_avatar': xxx,
	'media_intro': xxx,
	'article_count': int,
	'is_focus': 0, //default
	'fans_num': int,
	'articles': [
		{
			'id': xxx,
			'article_title': xxx,
			'article_media_name': xxx,
			'article_topic_name': xxx,
			'article_img': xxx,
			'article_color': '#',
		},
		{
			//...
		}
	]
}
```

### 获取文章

**GET /article/get_one_article**

请求:

```
{
	id: xxx,
}
```

结果:

```
{
	'article_img': xxx,
	'create_time': xxx,
	'article_title': xxx,
	'article_author': xxx,
	'article_content': xxx,
	'is_focus': 0,
	'media': {
		'id': int,
		'media_avatar': xxx,
		'media_name': xxx,
	},
	'topic'	: {
		'id': int,
		'topic_name': xxx,
		'topic_intro': xxx,
		'topic_img': xxx,
		'article_count': int,
	}
}
```

### 收藏或取消收藏文章

**POST /user/collect_article**

参数:

```
{
	article_id: xxx,
	action: 0 or 1, (0:取消， 1:收藏)
}
```

结果:

```
{
	status: 0 or 1, (int, 1: success, 0: error),
	message: 'xxxxx',
}
```

### 关注或取消关注媒体

**POST /user/focus_media**

请求:

```
{
	media_id: xxx,
	action: 0 or 1, (0:取消， 1:关注)
}
```

结果:

```
{
	status: 0 or 1, (int, 1: success, 0: error),
	message: 'xxxxx',
}
```

### 登陆

**POST /user/login**

请求:

```
{
	username: xxx,
	password: xxx, (加密过后)
}
```

结果:

```
{
	status: 0 or 1, (int, 1: success, 0: error),
	message: 'xxxxx',
}
```

### 注册

请求:

```
{
	username: xxx,
	password: xxx, (加密过后)
}
```

结果:

```
{
	status: 0 or 1, (int, 1: success, 0: error),
	message: 'xxxxx',
}
```

### 账户信息

**GET /user/get_detail**

不需要参数，使用cookie

结果:

```
{
	user_avatar: xxx,
	username: xxx,
	medias: [
		{
			'id': int,
			'media_name': xxx,
			'media_avatar': xxx,
		},
		//...
	],
	articles: [
		{
			'id': xxx,
			'article_title': xxx,
			'article_media_name': xxx,
			'article_topic_name': xxx,
			'article_img': xxx,
			'article_color': '#dddddd',
		},
		//...
	]
}
```

### 搜索

##### GET /article/search

参数:

```
{
	query: xxx
}
```

结果:

```
{
	[
		article: {
			id: int,
			article_title: xxx,
		},
		highlight: '带有em的html，高亮关键字'
	],
	[
		article: {
			id: int,
			article_title: xxx,
		},
		highlight: '带有em的html，高亮关键字'
	],
	//...
}
```