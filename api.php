<?php
/**
 * 相关 API
 * User: chuwen
 * Date Time: 2020/6/14 9:24
 * Email: <chenwenzhou@aliyun.com>
 */

include_once __DIR__ . "/lib/common.php";

if (get('do') === 'submit') {
    goto submit;
}


//发表留言
submit:
$content = post('content', '');

if (!$isLogin) {
    die(echoApiData(3, "请先登陆账号后再进行留言！"));
}

if (empty($content)) {
    die(echoApiData(1, "留言内容不能为空"));
}

$content = addslashes($content);//评论内容发转义

$res = $DB->query(sprintf("INSERT INTO comments (uid, contents, send_time, post_ip) VALUES (%d, '%s', %d, '%s')",
    $uINFO['uid'], $content, time(), getIp()
));

if($res){
    die(echoApiData(0, "发表留言成功！"));
}

die(echoApiData(-1, "发表留言失败，内部服务错误"));