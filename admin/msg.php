<?php
/**
 * 用户留言管理
 * User: chuwen
 * Date Time: 2020/6/15 11:27
 * Email: <chenwenzhou@aliyun.com>
 */

include_once dirname(__DIR__) . "/lib/common.php";

$page = get('page', 1);

if (get('action') === 'delete') {
    if (!$isLogin) {
        die(echoApiData(4, "请先登陆后再进行操作！"));
    }

    if (intval($uINFO['user_right']) !== 1) {
        die(echoApiData(3, "你没有超级管理员权限，无法进行操作！"));
    }

    $cid = post('cid');
    $cid = intval($cid)-10000;

    $res = $DB->query(sprintf("SELECT cid FROM users WHERE cid=%d", $cid));
    if (($row = $res->fetch_assoc()) === null) {
        die(echoApiData(5, "不存在该留言，请刷新页面！"));
    }

    //删除操作
    if($action === 'delete'){
        $res = $DB->query(sprintf("DELETE FROM comments WHERE uid=%d", $cid));
        if($res){
            die(echoApiData(0, "删除留言成功！"));
        }

        die(echoApiData(-1, "删除留言失败，请稍后再试！"));
    }
}


if (get('action') === 'reply') {
    if (!$isLogin) {
        die(echoApiData(4, "请先登陆后再进行操作！"));
    }

    if (intval($uINFO['user_right']) !== 1) {
        die(echoApiData(3, "你没有超级管理员权限，无法进行操作！"));
    }

    $cid = post('cid');
    $cid = intval($cid);

    $res = $DB->query(sprintf("SELECT cid FROM comments WHERE cid=%d", $cid));
    if (($row = $res->fetch_assoc()) === null) {
        die(echoApiData(5, "不存在该留言，请刷新页面！"));
    }

    //更新操作
    $sql = sprintf("UPDATE comments SET reply='%s', reply_time='%d' WHERE cid=%d", post('reply', ''), time(), $cid);
    if($DB->query($sql)){
        die(echoApiData(0, "回复留言成功！"));
    }

    die(echoApiData(-1, "回复留言失败，服务器出现问题，请稍后再试"));
}

if ($isLogin) {
    if (intval($uINFO['user_right']) === 1) {
        $sql = sprintf("SELECT * FROM comments ORDER BY send_time DESC LIMIT %d,%d",
            (intval($page) - 1) * 10, 10);
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

        //最大页码数量
        $_count = $DB->query("SELECT COUNT(*) AS c FROM `comments`")->fetch_assoc()['c'];
        $max_page = ceil($_count / 10);
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
    <title>用户留言管理 - PHP留言板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/css/bootstrap.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <link rel="stylesheet" href="../static/css/toastr.min.css">
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
            <a class="navbar-brand" href="../index.php" style="margin-left: 16px;">PHP留言板</a>
        </div>
        <div class="collapse navbar-collapse navbar-right" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="/admin/index.php"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span>
                        用户管理</a>
                </li>
                <li class="active">
                    <a href="/admin/msg.php">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span> 留言管理</a>
                </li>
                <li>
                    <a href="../index.php">
                        <span class="glyphicon glyphicon-home" aria-hidden="true"></span> 返回首页</a>
                </li>
                <?php if ($isLogin): ?>
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                            <?php echo $uINFO['nickname']; ?>
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="../userinfo.php?uid=<?php echo 10000 + intval($uINFO['uid']) ?>">个人资料</a></li>
                            <li><a href="../userinfo.php?uid=<?php echo 10000 + intval($uINFO['uid']) ?>#sendM">发布的留言</a></li>
                            <?php if(intval($uINFO['user_right']) === 1): ?>
                                <li role="separator" class="divider"></li>
                                <li><a href="index.php">后台管理</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="../login.php">
                            <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 登录</a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<div class="container col-md-8 col-md-offset-2" style="padding-top: 72px;">
    <?php if (!$isLogin): ?>
        <div class="jumbotron" style="color: #a94442; background-color: #f2dede;">
            <h1>请先登录账号 : (</h1>
            <p></p>
            <p class="text-right"><a class="btn btn-primary btn-lg" href="../login.php" role="button">登录账号</a></p>
        </div>
    <?php elseif (intval($uINFO['user_right']) !== 1): ?>
        <div class="jumbotron" style="color: #faf6f6; background-color: #e2aa56;">
            <h1><span class="glyphicon glyphicon-remove-sign"></span> 你的账号不具备超级管理员权限 : (</h1>
            <p></p>
            <p class="text-right"><a class="btn btn-danger btn-lg" href="../index.php" role="button">返回首页</a></p>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            <p>请注意，删除留言后，需要刷新页面才能查看最新结果</p>
        </div>
        <blockquote>
            发表留言：<?php echo $_count; ?>条
        </blockquote>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>CID</th>
                <th>用户名(UID)</th>
                <th>内容</th>
                <th style="width: 80px;">管理回复</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($cTmp as $v): ?>
                <tr data-cid="<?php echo $v['cid']; ?>">
                    <th scope="row"><?php echo $v['cid']; ?></th>
                    <td style="min-width: 60px"><?php $uid = intval($v['uid'])+10000;echo "{$uTmp[$v['uid']]['nickname']}<br/>{$uid}"; ?></td>
                    <td data-type="contents"><?php echo $v['contents']; ?></td>
                    <td data-type="reply"><?php echo $v['reply']; ?></td>
                    <td><?php echo date("Y-m-d H:i:s", $v['send_time']); ?></td>
                    <td style="min-width: 44px">
                        <a data-toggle="modal" data-target="#reply"
                           href="javascript:;"
                           data-name="<?php echo $uTmp[$v['uid']]['nickname']; ?>"
                           data-cid="<?php echo $v['cid']; ?>">回复</a>
                        <span style="padding: 0 6px"></span>
                        <a href="edit.php?cid=<?php echo $v['cid']+10000; ?>">编辑</a>
                        <span style="padding: 0 6px"></span>
                        <a href="javascript:deleteM(<?php echo $v['cid']+10000; ?>)">删除</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <?php echo multipage(intval($max_page), intval($page)); ?>
    <?php endif; ?>
</div>


<div class="modal fade" id="editUser" tabindex="-1" data-backdrop="static" aria-hidden="true"
     role="dialog" aria-labelledby="editUser">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="editUserLabel">编辑用户资料</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">UID:</label>
                        <input type="text" class="form-control" id="uid" disabled>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">用户名:</label>
                        <input type="text" class="form-control" id="nickname">
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">密码:</label>
                        <input type="text" class="form-control" minlength="8" maxlength="16"
                               id="password" placeholder="留空表示不修改，长度在 8~16 位数">
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">性别:</label>
                        <select id="sex" class="form-control">
                            <option value="0" disabled selected>请选择性别</option>
                            <option value="1">男</option>
                            <option value="2">女</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">QQ:</label>
                        <input type="text" class="form-control" id="qq">
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">E-mail:</label>
                        <input type="text" class="form-control" id="email">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary"
                        data-loading-text="编辑中..." id="confirm-edit">确认编辑
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="reply" tabindex="-1" data-backdrop="static" aria-hidden="true"
     role="dialog" aria-labelledby="reply">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="replyLabel">编辑用户资料</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">用户:</label>
                        <input id="replyUser" type="text" class="form-control" placeholder="留言的用户" disabled>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">留言内容: <a id="editLink">点击编辑留言</a></label>
                        <textarea id="replyContent" class="form-control" rows="6"
                                  required="required" disabled
                                  placeholder="留言内容"></textarea>
<!--                        <span class="help-block">如果你要修改它的留言内容，请点击“操作”中的“编辑”</span>-->
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">回复留言:</label>
                        <textarea class="form-control" rows="6"
                                  required="required" id="replyText"
                                  placeholder="回复Ta的留言"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary"
                        data-loading-text="回复..." id="confirm-reply">确认回复
                </button>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/js/bootstrap.min.js"></script>
<script src="../static/js/toastr.min.js"></script>
<script src="../static/js/script.js"></script>
</body>
</html>
