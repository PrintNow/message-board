<?php
/**
 * 后台首页
 * User: chuwen
 * Date Time: 2020/6/14 20:54
 * Email: <chenwenzhou@aliyun.com>
 */

include_once dirname(__DIR__) . "/lib/common.php";

if (in_array(($action = get('action')), ['edit', 'delete'])) {
    if (!$isLogin) {
        die(echoApiData(4, "请先登陆后再进行操作！"));
    }

    if (intval($uINFO['user_right']) !== 1) {
        die(echoApiData(3, "你没有超级管理员权限，无法进行操作！"));
    }

    $uid = post('uid');
    $uid = intval($uid)-10000;

    $nickname = post('nickname');
    $password = post('password', '');
    $sex = post('sex');
    $qq = post('qq');
    $email = post('email');

    $res = $DB->query(sprintf("SELECT uid,nickname,password,sex,qq,email FROM users WHERE uid=%d", $uid));
    if (($row = $res->fetch_assoc()) === null) {
        die(echoApiData(5, "不存在该用户，请刷新页面！"));
    }

    //删除操作
    if($action === 'delete'){
        $res = $DB->query(sprintf("DELETE FROM users WHERE uid=%d", $uid));
        if($res){
            die(echoApiData(0, "删除用户成功！"));
        }

        die(echoApiData(-1, "删除用户失败，请稍后再试！"));
    }

    if($password === '' || $password === $res['password']){
        $password = '';
    }else{
        $password = sprintf(",password='%s' ", $password);
    }

    //由于超级管理员页面仅拥有管理员权限才能修改
    //所以不考虑 SQL注入问题
    $update = $DB->query($sql=sprintf("UPDATE users SET nickname='%s' {$password}, sex=%d, qq=%d, email='%s' WHERE uid=%d",
        $nickname, $sex, $qq, $email, $uid
    ));

    if($update){
        die(echoApiData(0, "编辑用户资料成功！"));
    }

    die(echoApiData(-1, "编辑用户资料失败，请稍后再试！"));
}

if ($isLogin) {
    if (intval($uINFO['user_right']) === 1) {
        $sql = sprintf("SELECT uid+10000 AS uid,nickname,sex,email,qq,reg_ip,reg_time FROM users ORDER BY reg_time ASC LIMIT 20");
        $res = $DB->query($sql);

        $row = [];
        if ($res) {
            $row = $res->fetch_all(MYSQLI_ASSOC);
        }
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
    <title>后台管理 - PHP留言板</title>
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
                <li class="active">
                    <a href="../index.php"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> 后台管理首页</a>
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
                            <li><a href="javascript:void(0)">发布的留言</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:logout()">退出登录</a></li>
                            <?php if(intval($uINFO['user_right']) === 1): ?>
                                <li role="separator" class="divider"></li>
                                <li><a href="admin/index.php">后台管理</a></li>
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
            <p>请注意，编辑、删除用户后，需要刷新页面才能查看最新结果</p>
            <p style="font-weight: bold">PS：编辑用户成功后会自动刷新页面，但是删除用户成功后不会自动刷新</p>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>UID</th>
                <th>用户名</th>
                <th>性别</th>
                <th>QQ</th>
                <th>E-mail</th>
                <th>注册IP</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($row as $v): ?>
                <tr data-uid="<?php echo $v['uid']; ?>">
                    <th scope="row"><?php echo $v['uid']; ?></th>
                    <td><?php echo $v['nickname']; ?></td>
                    <td><?php echo ['未知', '男', '女'][$v['sex']]; ?></td>
                    <td><?php echo $v['qq']; ?></td>
                    <td><?php echo $v['email']; ?></td>
                    <td><?php echo $v['reg_ip']; ?></td>
                    <td><?php echo date("Y-m-d H:i:s", $v['reg_time']); ?></td>
                    <td>
                        <a data-toggle="modal" data-target="#editUser"
                           data-uid="<?php echo $v['uid']; ?>"
                           data-nickname="<?php echo $v['nickname']; ?>"
                           data-sex="<?php echo $v['sex']; ?>"
                           data-qq="<?php echo $v['qq']; ?>"
                           data-email="<?php echo $v['email']; ?>"
                           href="javascript:;">编辑</a>
                        <span style="padding: 0 6px"></span>
                        <a href="javascript:deleteUser(<?php echo $v['uid']; ?>)">删除</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
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

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/js/bootstrap.min.js"></script>
<script src="../static/js/toastr.min.js"></script>
<script src="../static/js/script.js"></script>
</body>
</html>
