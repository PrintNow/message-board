<?php
/**
 * 留言板主页，显示留言内容
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */

include __DIR__."/logs/functions.php";
write_logs();

include_once __DIR__ . "/lib/common.php";

$page = get('page', 1);

/**
 * 如果页码小于 0
 * 则返回首页
 */
if ($page < 1) {
    header("Location: index.php");
    die;
}


$uidTmp = [];
$cTmp = [];//储存帖子

//最大页码数量
$max_page = ceil(($count=$DB->query("SELECT COUNT(*) AS c FROM `comments`")->fetch_assoc()['c']) / 10);

//页码*每页显示多少数据      每页显示多少数据
$sql = sprintf("SELECT * FROM `comments` ORDER BY send_time DESC LIMIT %d,%d", (intval($page) - 1) *
    10, 10);
$res = $DB->query($sql);

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $uidTmp[] = $row['uid'];
        $cTmp[] = $row;
    }
    $res->free_result();// 释放结果集
}

$uTmp = [];//储存用户信息
$uidTmp = join(",", $uidTmp);

//使用 WHERE IN 语法批量查询用户信息
$res = $DB->query("SELECT uid,nickname,summary,sex,qq,email FROM users WHERE uid IN({$uidTmp})");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $uTmp[$row['uid']] = $row;
    }
    $res->free_result();// 释放结果集
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="Wenzhou Chan">
    <title>PHP留言板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/css/bootstrap.css">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/toastr.min.css">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top affix" role="navigation" id="slider_sub">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#example-navbar-collapse">
                <span class="sr-only">切换导航</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./" style="margin-left: 16px;">PHP留言板</a>
        </div>
        <div class="collapse navbar-collapse navbar-right" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="./"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> 首页</a>
                </li>

                <?php if ($isLogin): ?>
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                            <?php echo $uINFO['nickname']; ?>
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="userinfo.php?uid=<?php echo 10000 + intval($uINFO['uid']) ?>">个人资料</a></li>
                            <li><a href="userinfo.php?uid=<?php echo 10000 + intval($uINFO['uid']) ?>#sendM">发布的留言</a>
                            </li>
                            <?php if (intval($uINFO['user_right']) === 1): ?>
                                <li role="separator" class="divider"></li>
                                <li><a href="admin/index.php">后台管理</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="./login.php"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 登录</a>
                    </li><li>
                        <a href="./reg.php"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 免费注册</a>
                    </li>
                <?php endif; ?>
                <li><a href="javascript:logout()">退出登录</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container col-md-8 col-md-offset-2" style="padding-top: 72px;">
    <form class="form-horizontal" onsubmit="return submitMessage(this)">
        <textarea class="form-control" rows="6" name="content" required="required"
                  placeholder="*请输入留言内容" title="请先登录后操作"></textarea>

        <div class="form-group">
            <div style="margin-top: 16px">
                <div class="col-xs-4">
                    <button type="reset" class="btn btn-default btn-lg btn-block">重置表单</button>
                </div>
                <div class="col-xs-8">
                    <button name="submitBtn" data-loading-text="发表留言中..."
                            type="submit" class="btn btn-primary btn-lg btn-block">发表留言
                    </button>
                </div>
            </div>
        </div>
    </form>

    <?php foreach ($cTmp as $v): ?>
    <div id="<?php echo $v['cid'] + 10000; ?>" class="media">
        <div class="media-left">
            <a target="_blank" href="userinfo.php?uid=<?php echo $v['uid'] + 10000; ?>">
                <img class="media-object img-circle" title="点击查看用户资料"
                     src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $uTmp[$v['uid']]['qq']; ?>&s=640"
                     alt="<?php echo $uTmp[$v['uid']]['qq'] ?> QQ头像">
            </a>
        </div>
        <div class="media-body">
            <div class="media-heading">
                <div class="nickname"><?php echo $uTmp[$v['uid']]['nickname']; ?></div>
                <div class="secondary">
                        <span class="time" title="<?php echo date("Y-m-d H:i", $v['send_time']); ?>"
                              datetime="<?php echo date('c', $v['send_time']); ?>"><?php echo formatTime($v['send_time']); ?></span>
                    <span class="summary"><?php echo $uTmp[$v['uid']]['summary']; ?></span>
                </div>
            </div>
            <p><?php echo join("</p><p>", explode("\n", $v['contents'])); ?></p>

            <?php if ($isLogin): ?>
                <?php if (intval($uINFO['uid']) === intval($v['uid'])): ?>
                    <div class="operate">
                        <a href="edit.php?cid=<?php echo $v['cid'] + 10000; ?>">编辑</a>
                        <span style="padding: 0 5px"></span>
                        <a href="javascript:deleteM(<?php echo $v['cid'] + 10000; ?>)">删除</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($v['reply'])): ?>
            <div class="media second">
                <div class="media-left">
                    <a href="javascript:;">
                        <img class="media-object img-circle admin-icon" src="./static/img/guanliyuan.png">
                    </a>
                </div>
                <div class="media-body second">
                    <div class="nickname" style="color: #ffa014">管理员</div>
                    <div class="secondary">
                        <span class="time" title="<?php echo date("Y-m-d H:i", $v['reply_time']); ?>"
                              datetime="<?php echo date('c', $v['reply_time']); ?>"><?php echo formatTime($v['reply_time']); ?></span>
                    </div>
                    <p><?php echo join("</p><p>", explode("\n", $v['reply'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <?php echo multipage(intval($max_page), intval($page)); ?>
    <span>共计：<?php echo $count; ?>条</span>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/js/bootstrap.min.js"></script>
<script src="static/js/toastr.min.js"></script>
<script src="static/js/script.js"></script>
</body>
</html>
