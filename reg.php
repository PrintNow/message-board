<?php
/**
 * 注册页面
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */

include __DIR__."/logs/functions.php";
write_logs();

if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    include_once __DIR__ . "/lib/common.php";
    if (get('action') === 'reg') {
        $nickname = filter_var(post('nickname'), FILTER_SANITIZE_MAGIC_QUOTES);
        $password = post('password');
        $qq = post('qq');
        $email = strtolower(post('email'));
        $sex = post('sex');

        $n_l = mb_strlen($nickname, "utf-8");
        if ($n_l < 2 || $n_l > 20) {
            die(echoApiData(1, '用户名长度必须 大于1 小于21！'));
        }

        $_p = preg_match('/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)]|[\(\)])+$)([^(0-9a-zA-Z)]|[\(\)]|[a-z]|[A-Z]|[0-9]){8,16}$/', $password);
        if (!$_p) {
            die(echoApiData(1, '密码至少由 字母、数字或特殊字符其中两种组成，且长度在 8~16位 之间'));
        }

        $_q = preg_match('/[1-9][0-9]{5,}/', $qq);
        if (!$qq) {
            die(echoApiData(1, '请输入正确的QQ号码，由纯数字、开头不为0并且长度大于5组成'));
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die(echoApiData(1, '请输入正确的 E-mail！'));
        }

        if (!in_array($sex, ['0', '1', '2'])) {
            die(echoApiData(1, '请输入选择性别！'));
        }

        //查询 用户名、QQ、邮箱 是否已经被注册了
        $sql = sprintf("SELECT nickname,qq,email FROM users WHERE nickname='%s' OR qq=%d OR email='%s' LIMIT 1",
            $nickname,
            $qq,
            $email
        );
        $res = $DB->query($sql);
        $row = $res->fetch_assoc();
        if ($row['nickname'] === $nickname) {
            die(echoApiData(2, '该 用户名 已被注册了，换一个试试吧'));
        }
        if ($row['qq'] === $qq) {
            die(echoApiData(2, '该 QQ号 已被注册了，请检查输入是否有误', $row));
        }
        if ($row['email'] === $email) {
            die(echoApiData(2, '该 E-mail 已被注册了，请检查输入是否有误'));
        }
        $res->free_result();//释放结果集

        //插入数据库
        $sql = sprintf("INSERT INTO users (nickname,password,qq,email,sex,summary,reg_time,reg_ip) "
            . "VALUES ('%s', '%s', %d, '%s', %d, '%s', %d, '%s')",
            $nickname,//用户名
            $password,//密码
            $qq,//QQ
            $email,//邮箱
            $sex,//性别
            '',//个性签名
            time(),//注册时间戳
            getIp()//获取用户IP
        );
        $query = $DB->query($sql);
        if ($query) {
            die(echoApiData(0, '注册成功，欢迎使用'));
        }

        die(echoApiData(-1, '注册失败，内部服务出现错误 或者 你输入的信息含有害信息已被系统拦截！'));
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
    <title>免费注册账号 - PHP留言板</title>
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
                <li >
                    <a href="index.php"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> 首页</a>
                </li>
                <li>
                    <a href="login.php"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> 登录</a>
                </li>
                <li class="active">
                    <a href="reg.php"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 免费注册</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container col-md-6 col-md-offset-3" style="padding-top: 72px;">
    <ol class="breadcrumb">
        <li><a href="index.php">首页</a></li>
        <li class="active">免费注册账号</li>
    </ol>

    <form class="form-horizontal" onsubmit="return regAccount(this)">
        <div class="form-group">
            <label for="nickname" class="col-sm-2 control-label">*用户名</label>
            <div class="col-sm-10">
                <input required="required" type="text" minlength="1" maxlength="20"
                       class="form-control" name="nickname" placeholder="请设置一个用户名，长度在 1~20 之间">
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">*密码</label>
            <div class="col-sm-10">
                <input required="required" type="text" minlength="8" maxlength="16"
                       class="form-control" name="password" placeholder="请设置一个 8~16 位的密码">
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">*E-mail</label>
            <div class="col-sm-10">
                <input required="required" type="email" class="form-control" name="email" placeholder="请输入以你的电子邮箱">
            </div>
        </div>
        <div class="form-group">
            <label for="qq" class="col-sm-2 control-label">*QQ 账号</label>
            <div class="col-sm-10">
                <input required="required" type="number" pattern="/[1-9][0-9]{5,}/"
                       class="form-control" name="qq" placeholder="请输入你的QQ账号，至少5位数">
            </div>
        </div>

        <div class="form-group">
            <label for="qq" class="col-sm-2 control-label">*性别</label>
            <div class="col-sm-10">
                <select class="form-control" name="sex" required="required">
                    <option value="" selected="selected" disabled="disabled">请选择你的性别</option>
                    <option value="1">♂ 男</option>
                    <option value="2">♀ 女</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div style="margin-top: 16px">
                <div class="col-xs-4">
                    <button type="reset" class="btn btn-default btn-lg btn-block">重置表单</button>
                </div>
                <div class="col-xs-8">
                    <button name="regBtn" data-loading-text="注册账号中..."
                            type="submit" class="btn btn-primary btn-lg btn-block">立即注册
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
