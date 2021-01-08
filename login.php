<?php
/**
 * 登陆账号
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */

include __DIR__."/logs/functions.php";
write_logs();

if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    include_once __DIR__ . "/lib/common.php";
    if (get('action') === 'login') {
        $isEmail = false;
        $account = filter_var(trim(post('account')), FILTER_SANITIZE_MAGIC_QUOTES);
        $password = post('password');

        $n_l = mb_strlen($account, "utf-8");
        if ($n_l < 1) {
            die(echoApiData(1, '用户名长度必须 大于1'));
        }

        //首先判断输入的账号是否为邮箱
        if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
            $isEmail = true;
            $sql = sprintf("SELECT uid,email,password FROM users WHERE email='%s' LIMIT 1", $account);
        } else {
            $sql = sprintf("SELECT uid,qq,password FROM users WHERE qq=%d OR uid=%d LIMIT 1", $account, intval
                ($account)-10000);
        }

        $res = $DB->query($sql);
        $row = $res->fetch_assoc();

        if ($row === NULL) {
            die(echoApiData(3, '不存在该账号，请注意仅能输入 UID、QQ或E-mail ！'));
        }

        if ($row['password'] !== $password) {
            die(echoApiData(3, '账号与密码不匹配，请检查'));
        }

        //Cookie 有效期7天+随机秒数，避免被爆破
        $expireTime = time() + 24 * 60 * 60 * 7 + rand(26, 909);

        //用户ID|-|cookie过期时间戳|-| MD5散列值(过期时间戳+密码)
        //MD5散列值(过期时间戳+密码) 这样保证的是解密时 与 cookie过期时间戳 进行校验
        $code = ($row['uid']+10000) . "|-|" . $expireTime . "|-|" . crypt($row['password'] . $expireTime, '$1$rasmusle$');

        //进行对称加密
        $encode = authcode($code, 'ENCODE', KEY);

        //发送 cookie
        setcookie("mbToken", $encode, $expireTime, "/");

        die(echoApiData(0, '登录账号成功！'));
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
    <title>登陆账号 - PHP留言板</title>
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
            <a class="navbar-brand" href="index.php" style="margin-left: 16px;">PHP留言板</a>
        </div>
        <div class="collapse navbar-collapse navbar-right" id="example-navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> 首页</a>
                </li>
                <li class="active">
                    <a href="login.php"><span class="glyphicon glyphicon-log-in"
                                                             aria-hidden="true"></span> 登录</a>
                </li>
                <li>
                    <a href="reg.php"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 免费注册</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container col-md-6 col-md-offset-3" style="padding-top: 72px;">
    <ol class="breadcrumb">
        <li><a href="index.php">首页</a></li>
        <li class="active">登陆账号</li>
    </ol>

    <form class="form-horizontal" onsubmit="return loginAccount(this)">
        <div class="form-group">
            <label for="account" class="col-sm-2 control-label">账号</label>
            <div class="col-sm-10">
                <input required="required" type="text" minlength="1"
                       class="form-control" name="account" placeholder="请输入 UID/QQ账号/邮箱 其中之一">
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
                <input required="required" type="password" minlength="8" maxlength="16"
                       class="form-control" name="password" placeholder="请输入你的密码，8~16 位的密码">
            </div>
        </div>

        <div class="form-group">
            <div style="margin-top: 16px">
                <div class="col-xs-4">
                    <button type="reset" class="btn btn-default btn-lg btn-block">重置表单</button>
                </div>
                <div class="col-xs-8">
                    <button name="regBtn" data-loading-text="登录中..."
                            type="submit" class="btn btn-primary btn-lg btn-block">立即登录
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/js/bootstrap.min.js"></script>
<script src="static/js/toastr.min.js"></script>
<script src="static/js/script.js"></script>
</body>
</html>
