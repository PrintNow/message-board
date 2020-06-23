<?php
/**
 * 相关 API
 * User: chuwen
 * Date Time: 2020/6/14 9:24
 * Email: <chenwenzhou@aliyun.com>
 */

include_once __DIR__ . "/lib/common.php";

$do = get('do');

if ($do === 'submit') {
    goto submit;
} else if ($do === 'delete') {
    goto delete;
} else if ($do === 'edit') {
    goto edit;
} else {
    die(echoApiData(-500, "此方法不存在！"));
}

//修改用户个人资料内容
edit:
$nickname = addslashes(post('nickname'));//昵称
$sex = addslashes(post('sex'));//性别
$summary = addslashes(post('summary'));//更新签名

$_password = addslashes(post('_password'));//源密码
$password = addslashes(post('password'));//密码

if(!isset($uINFO['uid'])){
    die(echoApiData(3, '请先登录再进行操作'));
}

if(!$nickname){
    die(echoApiData(5, '昵称不能为空'));
}

if(!$sex){
    die(echoApiData(5, '性别不能为空'));
}

if(!empty($_password)){
    if($_password !== $uINFO['password']){
        die(echoApiData(6, '原密码错误'));
    }
}else{
    $password = $uINFO['password'];
}

if(!$password){
    die(echoApiData(5, '请输入你要修改的密码，长度在8~16位之间'));
}

//更新
$res = $DB->query(sprintf("UPDATE users SET nickname='%s', sex=%d, summary='%s', password='%s' WHERE uid=%d",
    $nickname, $sex, $summary, $password, $uINFO['uid']
));

if($res){
    die(echoApiData(0, "修改个人资料成功！"));
}

die(echoApiData(-1, "修改个人资料失败，请稍后再试！", [$DB->error]));



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

if ($res) {
    die(echoApiData(0, "发表留言成功！"));
}


die(echoApiData(-1, "发表留言失败，内部服务错误"));


//删除留言
delete:
$cid = post('cid', '');

if (!$isLogin) {
    die(echoApiData(3, "请先登陆账号后再进行删除留言！"));
}

if (empty($cid)) {
    die(echoApiData(1, "CID 不能为空，请刷新页面！"));
}

$cid = intval($cid) - 10000;

$res = $DB->query(sprintf("SELECT uid FROM comments WHERE cid=%d", $cid));
if (($row = $res->fetch_assoc())) {
    //判断是否为自己的留言
    //不是自己的禁止删除
    if (intval($row['uid']) !== intval($uINFO)) {
        die(echoApiData(5, "不能删除他人留言，请刷新页面！"));
    }

    $del = $DB->query(sprintf("DELETE FROM comments WHERE cid=%d", $cid));
    if ($del) {
        die(echoApiData(0, "删除留言成功！"));
    }

    die(echoApiData(-1, "删除留言失败，请稍后再试！"));
}

die(echoApiData(4, "该留言不存在，请刷新页面！"));