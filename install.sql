/*
 Navicat Premium Data Transfer

 Source Server         : 学习-本机 MySQL
 Source Server Type    : MySQL
 Source Server Version : 50562
 Source Host           : localhost:3307
 Source Schema         : board

 Target Server Type    : MySQL
 Target Server Version : 50562
 File Encoding         : 65001

 Date: 23/06/2020 11:51:01
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
                          `uid` int(11) NOT NULL AUTO_INCREMENT,
                          `user_right` int(1) NOT NULL DEFAULT 0 COMMENT '默认是0，表示的是普通用户  1:超级管理员，可以管理后台',
                          `nickname` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户昵称',
                          `summary` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '个性签名',
                          `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户密码，是经过双重 md5 加密的',
                          `sex` int(1) NOT NULL DEFAULT 0 COMMENT '0:未知性别 1:男 2:女',
                          `qq` varchar(28) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'QQ账号',
                          `email` varchar(54) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '邮箱',
                          `reg_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '注册时间',
                          `reg_ip` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '注册时的IP地址',
                          PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 1, '初文', '我就是我，不一样的烟火..', '1361289290', 1, '1361289290', 'chenwenzhou@aliyun.com', '1592030801', '192.168.3.4');
INSERT INTO `users` VALUES (2, 0, '有容乃大', '海纳百川，有容乃大。', '2634407844', 2, '2634407844', '2624407844@qq.com', '1592021773', '192.168.3.4');
INSERT INTO `users` VALUES (3, 0, 'chuwen', '', 's1361289290', 1, '1361289290', '1361289290@qq.com', '1592110180', '127.0.0.1');
INSERT INTO `users` VALUES (4, 0, 'WDNMDY', '', 'WDNMDYLQ123', 1, '979446687', '979446687@qq.com', '1592279280', '240e:ce:80bd:b054:245c:e2ad:6a2e:1fd');

SET FOREIGN_KEY_CHECKS = 1;



DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments`  (
                             `cid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '留言内容ID',
                             `reply` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '管理员回复',
                             `reply_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '回复时间',
                             `uid` int(11) NOT NULL COMMENT '关联 users 表的 uid',
                             `contents` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '留言内容',
                             `send_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发表留言时间',
                             `post_ip` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '发表留言时的IP地址',
                             PRIMARY KEY (`cid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of comments
-- ----------------------------
INSERT INTO `comments` VALUES (1, NULL, '', 1, '.宋代著名学者朱熹对此章评价极高，说它是「入道之门，积德之基」。本章这三句话是人们非常熟悉的。历来的解释都是：学了以后，又时常温习和练习，不也高兴吗等等。三句话，一句一个意思，前后句子也没有什么连贯性。但也有人认为这样解释不符合原义，指出这里的「学」不是指学习，而是指学说或主张；「时」不能解为时常，而是时代或社会的意思，「习」不是温习，而是使用，引申为采用。而且，这三句话不是孤立的，而是前后相互连贯的。这三句的意思是：自己的学说，要是被社会采用了，那就太高兴了；退一步说，要是没有被社会所采用，可是很多朋友赞同我的学说，纷纷到我这里来讨论问题，我也感到快乐；再退一步说，即使社会不采用，人们也不理解我，我也不怨恨，这样做，不也就是君子吗？（见《齐鲁学刊》1986年第6期文）这种解释可以自圆其说，而且也有一定的道理，供读者在理解本章内容时参考。', '1592030801', '127.0.0.1');
INSERT INTO `comments` VALUES (2, '测试回复内容...', '1592470278', 2, '“海纳百川，有容乃大；壁立千仞，无欲则刚。”此联为清末政治家林则徐任两广总督时在总督府衙题书的堂联。意为：大海因为有宽广的度量才容纳了成百上千条河流；高山因为没有勾心斗角的凡世杂欲才如此的挺拔。上下联最后一字——“大”与“刚”，意思是说，这种浩然之气最伟大，最刚强，更表明了作者的至大至刚。这种海纳百川的胸怀和“壁立千仞”的刚直，来源于“无欲”。这样的气度和“无欲”情怀以及至大至刚的浩然之气，正是心理健康不可缺少的元素。\r\n\r\n做人如此，治国也可以借鉴，一个国家各个领域都兴旺发达，能接纳不同的思想，政治、经济、文化、艺术等，才能高度文明，而不是某一方面畸形发展，造成社会大众的心智的缺失，这样的国家是不会长久富强的。', '1592021773', '127.0.0.1');
INSERT INTO `comments` VALUES (3, NULL, '', 1, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '1592059416', '127.0.0.1');
INSERT INTO `comments` VALUES (4, NULL, '', 1, '学问之美，在于使人一头雾水;诗歌之美，在于煽动男女出轨;女人之美，在于蠢得无怨无悔;男人之美，在于说谎说得白日见鬼。', '1592138555', '2409:8a38:6822:c8a0:4cd0:cb47:55ee:f78f');
INSERT INTO `comments` VALUES (5, NULL, '', 1, '没见过草原，不知道天多高地多厚。没见过草原上的白云，不知道什么是空灵，什么是纯净。', '1592138640', '2409:8a38:6822:c8a0:4cd0:cb47:55ee:f78f');
INSERT INTO `comments` VALUES (6, '测试回复', '1592470060', 1, '	你见，或者不见我 我就在那里 不悲 不喜 你念，或者不念我 情就在那里 不来 不去 你爱，或者不爱我 爱就在那里 不增 不减 你跟，或者不跟我 我的手就在你手里 不舍不弃 来我的怀里， 或者 让我住进你的心里 默然相爱 寂静欢喜\r\n--扎西拉姆·多多 《班扎古鲁白玛的沉默》', '1592138764', '2409:8a38:6822:c8a0:4cd0:cb47:55ee:f78f');
INSERT INTO `comments` VALUES (10, NULL, '', 1, '测试', '1592190927', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (11, NULL, '', 1, '测试', '1592190928', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (12, NULL, '', 1, '测试', '1592190929', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (14, NULL, '', 1, '测试', '1592190931', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (15, NULL, '', 1, '测试', '1592190932', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (16, NULL, '', 1, '测试', '1592190933', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (17, NULL, '', 1, '测试', '1592190934', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (18, NULL, '', 1, '测试', '1592190935', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (19, NULL, '', 1, '测试', '1592190937', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (20, NULL, '', 1, '测试', '1592190938', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (21, NULL, '', 1, '测试', '1592190939', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (22, NULL, '', 1, '测试.....', '1592190940', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (23, NULL, '', 1, '测试', '1592190940', '2409:8a38:6822:c8a0:c45b:450c:38e2:4580');
INSERT INTO `comments` VALUES (24, NULL, '', 4, '下午下课了来打王者', '1592279308', '240e:ce:80bd:b054:245c:e2ad:6a2e:1fd');
INSERT INTO `comments` VALUES (25, '测试回复内容', '1592470365', 1, '@WDNMDY 收到。吃完午饭就来打王者', '1592279326', '2409:8a38:6822:c8a0:14fe:23d0:aa68:2fa0');
INSERT INTO `comments` VALUES (26, '测试管理员回复留言内容', '1592616301', 1, '再次测试留言内容，测试自己更新留言内容', '1592616277', '127.0.0.1');
INSERT INTO `comments` VALUES (27, '点个赞', '1592883333', 1, '今日完成修改个人资料', '1592883316', '127.0.0.1');

SET FOREIGN_KEY_CHECKS = 1;
