<?php
/**
 * 用户信息页面
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */

include_once __DIR__ . "/lib/common.php";

$uid = intval(get('uid', 0));

$notFound = true;//是否为未找到该用户，true：是，返回404页面
$max_page = 1;//最大页码

$page = intval(get('page', '1'));//当前页码

if ($uid < 10001) {
    header("HTTP/1.0 404 Not Found");
} else {
    $sql = sprintf("SELECT uid,nickname,summary,email,qq,reg_time,sex FROM users WHERE uid=%d LIMIT 1", intval($uid) -
        10000);
    $res = $DB->query($sql);

    if ($res) {
        $notFound = false;//存在该用户
        $userInfo = $res->fetch_assoc();
        $res->free_result();//用户信息数组

        //查询当前用户的留言数量
        $sql = sprintf("SELECT COUNT(*) AS c FROM comments WHERE uid=%d", intval($uid) - 10000);
        $res = $DB->query($sql);
        $count = $res->fetch_assoc()['c'];//发布了多少留言
        $max_page = ceil($count / 10);//最大页码数量
        $res->free_result();

        //查询当前页的留言
        $sql = sprintf("SELECT cid,contents,send_time FROM comments WHERE uid=%d ORDER BY send_time DESC LIMIT %d,%d",
            intval($uid) - 10000, ($page - 1) * 10, 10);
        $res = $DB->query($sql);//查询最新10条留言
        $comment = $res->fetch_all(MYSQLI_ASSOC);
        $res->free_result();
    }

    $last_time = $userInfo['reg_time'];
    if (isset($comment[0])) {
        $last_time = $comment[0]['send_time'];
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
    <title>用户详细页面 - PHP留言板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/css/bootstrap.css">
    <link rel="stylesheet" href="static/css/toastr.min.css">
    <link rel="stylesheet" href="static/css/style.css">
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
                    <a href="./"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> 首页</a>
                </li>

                <?php if ($isLogin): ?>
                    <li class="dropdown active">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                            <?php echo $uINFO['nickname']; ?>
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li class="active"><a href="userinfo.php?uid=<?php echo 10000 + intval($uINFO['uid']) ?>">个人资料</a>
                            </li>
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
    <ol class="breadcrumb">
        <li><a href="index.php">首页</a></li>
        <li class="active">用户资料：<?php echo $uid; ?></li>
    </ol>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">用户资料</h3>
        </div>
        <div class="panel-body">
            <?php if ($notFound): ?>
                <div class="well" style="color: #a94442; background-color: #f2dede;">
                    <h3><span class="glyphicon glyphicon-remove-sign"></span> 不存在 UID 为 <?php echo $uid; ?> 的用户</h3>
                    <a href="index.php">返回首页</a>
                </div>
            <?php else: ?>
                <div class="col-md-3 col-sm-12 text-center">
                    <img alt="用户头像" class="img-circle"
                         width="128px" height="128px"
                         src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $userInfo['qq']; ?>&s=0">
                    <h3><span><?php echo ['', '♂', '♀'][$userInfo['sex']] ?></span> <?php echo $userInfo['nickname'];
                    ?></h3>
                    <h6><?php echo $userInfo['summary']; ?></h6>
                </div>
                <div class="col-md-9 col-sm-12 text-center">
                    <table class="table table-striped table-bordered">
                        <tbody>
                        <tr>
                            <td><b>UID</b></td>
                            <td align="center"><code><?php echo $userInfo['uid'] + 10000; ?></code></td>
                        </tr>
                        <tr>
                            <td><b>留言数量</b></td>
                            <td align="center"><?php echo $count; ?> 条</td>
                        </tr>
                        <tr>
                            <td><b>QQ</b></td>
                            <td align="center"><?php echo $userInfo['qq']; ?></td>
                        </tr>
                        <tr>
                            <td><b>E-mail</b></td>
                            <td align="center"><?php echo $userInfo['email']; ?></td>
                        </tr>
                        <tr>
                            <td><b>注册时间</b></td>
                            <td align="center"><?php echo date("Y-m-d H:i", $userInfo['reg_time']); ?></td>
                        </tr>
                        <td><b>最后活动时间</b></td>
                        <td align="center"><?php echo date("Y-m-d H:i", $last_time); ?></td>
                        </tr>
                        </tbody>
                    </table>

                    <?php if(($uid-10000) === @intval($uINFO['uid'])): ?>
                        <div class="col-xs-6">
                            <a href="javascript:editUserInfo(<?php echo $userInfo['uid']; ?>)" class="btn btn-block
                        btn-success">修改个人资料</a>
                        </div>
                        <div class="col-xs-6">
                            <a href="index.php" class="btn btn-block btn-primary">返回首页</a>
                        </div>
                    <?php else: ?>
                        <a href="index.php" class="btn btn-block btn-primary">返回首页</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$notFound): ?>
        <div id="sendM" class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">发布的留言 <small class="text-center">总数：<?php echo $count; ?>条</small></h3>
            </div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>留言内容</th>
                    <th style="min-width: 88px;">留言时间</th>
                    <th style="min-width: 44px;">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($comment as $row): ?>
                    <tr data-cid="<?php echo $row['cid'] + 10000; ?>">
                        <th scope="row"><?php echo $row['cid'] + 10000; ?></th>
                        <td><?php echo join("</p><p>", explode("\n", $row['contents'])); ?></td>
                        <td><?php echo date("Y-m-d H:i", $row['send_time']); ?></td>
                        <td>
                            <a href="edit.php?cid=<?php echo $row['cid']+10000; ?>">编辑</a>
                            <span style="padding:8px"></span>
                            <a href="javascript:deleteM(<?php echo $row['cid']+10000; ?>)">删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <th colspan="3">
                        <?php echo multipage(intval($max_page), intval($page), '&uid=' . $uid); ?>
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>





<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    修改个人资料
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="nickname" class="col-sm-2 control-label">昵称</label>
                        <div class="col-sm-10">
                            <input required="required" type="text" minlength="1" maxlength="20" class="form-control"
                                   value="<?php echo $userInfo['nickname']; ?>" name="nickname" placeholder="昵称，长度在 1~20
                                    之间">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nickname" class="col-sm-2 control-label">个性签名</label>
                        <div class="col-sm-10">
                            <input required="required" type="text" minlength="1" maxlength="20" class="form-control"
                                   value="<?php echo $userInfo['summary']; ?>" name="summary" placeholder="设置一个个性签名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="qq" class="col-sm-2 control-label">性别</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="sex" required="required">
                                <option value="1" <?php if($userInfo['sex'] == '1') echo 'selected' ?>>♂ 男</option>
                                <option value="2" <?php if($userInfo['sex'] == '2') echo 'selected' ?>>♀ 女</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="qq" class="col-sm-2 control-label">原密码</label>
                        <div class="col-sm-10">
                            <input required="required" type="text" minlength="1" maxlength="20" class="form-control"
                                   name="_password" placeholder="如果你要修改密码请输入原密码">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="qq" class="col-sm-2 control-label">新密码</label>
                        <div class="col-sm-10">
                            <input required="required" type="text" minlength="1" maxlength="20" class="form-control"
                                   name="password" placeholder="设置新密码">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="qq" class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <input required="required" type="text" minlength="1" maxlength="20" class="form-control"
                                   name="password2" placeholder="再次输入新密码">
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    取消
                </button>
                <button onclick="confirmInfoEdit(this)" type="button" class="btn
                btn-primary" data-loading-text="修改资料中...">
                    确认修改
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/js/bootstrap.min.js"></script>
<script src="static/js/toastr.min.js"></script>
<script src="static/js/script.js"></script>
</body>
</html>
