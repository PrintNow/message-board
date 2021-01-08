# 留言板 | message-board
PHP期末考核作品，留言板，包括会员注册、登录，留言发布，后台管理。

# 项目地址
https://github.com/PrintNow/message-board


上交时间：2020年6月23日 第3~6节

# 功能设计
1. 会员注册
    > 用户输入自己的用户名、密码、QQ和Email地址等信息，提交到Web服务器，由Web服务器通过访问数据库将其写入数据表。
2. 会员登录
    > 会员登录后可修改自己注册的基本信息和发布留言。没有登录的用户只能浏览留言而不能发布留言。
3. 信息发布
    > 可以维护自己已经发布的留言，包括添加新留言、删除本人留言、修改本人留言。
4. 后台管理
    > 管理员可以对会员、留言等信息进行删除、修改等更新操作……

# 表设计
1.用户表
```sql
CREATE TABLE `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user_right` int(1) NOT NULL DEFAULT 0 COMMENT '默认是0，表示的是普通用户  1:超级管理员，可以管理后台', 
  `nickname` varchar(30) NOT NULL COMMENT '用户昵称',
  `password` varchar(128) NOT NULL COMMENT '用户密码，是经过双重 md5 加密的',
  `sex` int(1) NOT NULL DEFAULT 0 COMMENT '0:未知性别 1:男 2:女',
  `qq` varchar(28) DEFAULT 'QQ账号',
  `email` varchar(54) DEFAULT '邮箱',
  `reg_time` datetime DEFAULT NULL COMMENT '注册时间',
  `reg_ip` varchar(100) DEFAULT NULL COMMENT '注册时的IP地址',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
```

2.留言表
```sql
CREATE TABLE `comments` (
  `cid` int(11) NOT NULL AUTO_INCREMENT COMMENT '帖子ID',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '关联 users 表的 uid', 
  `contents` TEXT NOT NULL COMMENT '留言内容',
  `send_time` datetime DEFAULT NULL COMMENT '发表留言时间',
  `post_ip` varchar(100) DEFAULT NULL COMMENT '发表留言时的IP地址',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
```


# 项目要求
> 采用 PHP+MySQL 技术开发项目。
> 整个项目同时给出文本形式的说明，说明包括：系统结构图、系统页面概述、数据库中表的结构设计。

- 页面版面设计合理
- 页面间链接和参数设置正确
- 没有语法错误
- 代码中做必要的注释