<?php
/**
 *
 * User: chuwen
 * Date Time: 2020/6/15 12:12
 * Email: <chenwenzhou@aliyun.com>
 */


include_once dirname(__DIR__) . "/lib/common.php";

$do = get('do');

if ($do === 'delete') {
    goto delete;
} else {
    die(echoApiData(-500, "此方法不存在！"));
}



//删除留言
delete:
$cid = post('cid', '');

if (!$isLogin) {
    die(echoApiData(3, "请先登陆账号后再进行删除留言！"));
}

if (intval($uINFO['user_right']) !== 1) {
    die(echoApiData(3, "你的账号不具备超级管理员权限，无法操作"));
}

if (empty($cid)) {
    die(echoApiData(1, "CID 不能为空，请刷新页面！"));
}

$cid = intval($cid) - 10000;

$res = $DB->query(sprintf("SELECT uid FROM comments WHERE cid=%d", $cid));
if (($row = $res->fetch_assoc())) {
    $del = $DB->query(sprintf("DELETE FROM comments WHERE cid=%d", $cid));
    if ($del) {
        die(echoApiData(0, "删除留言成功！"));
    }

    die(echoApiData(-1, "删除留言失败，请稍后再试！"));
}

die(echoApiData(4, "该留言不存在，请刷新页面！"));