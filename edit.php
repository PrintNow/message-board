<?php
/**
 * 编辑留言
 * User: chuwen
 * Date Time: 2020/6/15 10:26
 * Email: <chenwenzhou@aliyun.com>
 */

include __DIR__."/logs/functions.php";
write_logs();

include_once __DIR__ . "/lib/common.php";

$cid = intval(get('cid', 0));
$error = false;
$uid = '';

if ($cid < 10001) {
    header("HTTP/1.0 404 Not Found");
    $error = '不存在该留言';
} else {
    $content = post('content', '');
    $sql = sprintf("SELECT cid,uid,contents,send_time FROM comments WHERE cid=%d LIMIT 1", $cid - 10000);
    $res = $DB->query($sql);

    if ($res) {
        $row = $res->fetch_assoc();
        $res->free_result();

        if (intval($row['uid']) !== intval($uINFO['uid'])) {
            $error = '不能修改他人的留言内容';
        }

        if (get('do') === 'update') {
            if ($content === '') {
                $result = '留言内容不能为空';
            } else {
                $sql = sprintf("UPDATE comments SET contents='%s' WHERE cid=%d", $content, $cid - 10000);
                $res = $DB->query($sql);

                if ($res) {
                    $result = '修改留言成功！<b>'.date("Y-m-d H:i:s").'</b>';
                    $row['contents'] = $content;
                } else {
                    $result = '修改留言失败！';
                }
            }
        }
    } else {
        $error = '不存在该留言.';
    }
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
    <title>编辑留言(<?php echo $cid; ?>) - PHP留言板</title>
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
                <li>
                    <a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> 首页</a>
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
                            <li><a href="userinfo.php?uid=<?php echo 10000 + intval($uINFO['uid']) ?>#sendM">发布的留言</a></li>
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
    <?php if (get('do') === 'update'): ?>
        <div class="alert alert-warning alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <p><?php echo $result; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($error === false): ?>
        <form class="form-horizontal" method="post" action="edit.php?cid=<?php echo $cid; ?>&do=update">

            <div class="form-group col-md-4">
                <label for="uid" class="control-label">CID:</label>
                <input type="text" class="form-control" name="uid" disabled="" value="<?php echo $cid; ?>">
            </div>
            <div class="col-md-2"></div>
            <div class="form-group col-md-6">
                <label for="send_time" class="control-label">发布时间:</label>
                <input type="text" class="form-control" name="send_time" disabled=""
                       value="<?php echo date("Y-m-d H:i:s", $row['send_time']); ?>">
            </div>

            <textarea class="form-control" rows="6" name="content" required="required"
                      placeholder="*请输入留言内容"><?php echo $row['contents']; ?></textarea>

            <div class="form-group">
                <div style="margin-top: 16px">
                    <div class="col-xs-4">
                        <a href="index.php" class="btn btn-default btn-lg btn-block">返回首页</a>
                    </div>
                    <div class="col-xs-8">
                        <button name="submitBtn" data-loading-text="发表留言中..."
                                type="submit" class="btn btn-primary btn-lg btn-block">更新留言
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="jumbotron" style="color: #faf6f6; background-color: #e2aa56;">
            <h1><span class="glyphicon glyphicon-remove-sign"></span> <?php echo $error; ?> : (</h1>
            <p></p>
            <p class="text-right"><a class="btn btn-danger btn-lg" href="index.php" role="button">返回首页</a></p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/js/bootstrap.min.js"></script>
<script src="static/js/toastr.min.js"></script>
<script src="static/js/script.js"></script>
</body>
</html>
