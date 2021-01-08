<?php
/**
 *
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */


echo "后期会考虑做一个在线安装的，目前暂不考虑";

die;
header("Content-type:text/html;charset=utf-8");

$SQL = <<<SQL
/*用户信息表*/
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user_right` int(1) NOT NULL DEFAULT 0 COMMENT '默认是0，表示的是普通用户  1:超级管理员，可以管理后台', 
  `nickname` varchar(30) NOT NULL COMMENT '用户昵称',
  `password` varchar(128) NOT NULL COMMENT '用户密码，是经过双重 md5 加密的',
  `sex` int(1) NOT NULL DEFAULT 0 COMMENT '0:未知性别 1:男 2:女',
  `qq` varchar(28) DEFAULT 'QQ账号',
  `email` varchar(54) DEFAULT '邮箱',
  `reg_time` datetime DEFAULT NULL COMMENT '注册时间',
  `reg_ip` varchar(100) DEFAULT NULL COMMENT '注册时的IP地址',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

/*留言表*/
CREATE TABLE IF NOT EXISTS `comments` (
  `cid` int(11) NOT NULL AUTO_INCREMENT COMMENT '留言内容ID',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '关联 users 表的 uid', 
  `contents` TEXT NOT NULL COMMENT '留言内容',
  `send_time` datetime DEFAULT NULL COMMENT '发表留言时间',
  `post_ip` varchar(100) DEFAULT NULL COMMENT '发表留言时的IP地址',
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
SQL;

include __DIR__ . "/config.php";
include __DIR__ . "/lib/funtions.php";

$step = 1;//步骤1

//表示没有配置数据库信息
if ($DBC['db_host'] === '$DB_HOST$') {
    $step = 1;
} else {
    //实例化 mysqli 类
    $DB = new mysqli($DBC['db_host'], $DBC['db_user'], $DBC['db_password'], $DBC['db_name'], $DBC['db_port']);

    /**
     * 如果数据库连接失败
     * 给出错误提示，并且结束程序运行
     */
    if ($DB->connect_error) {
        $error = ('Connect Error (' . $DB->connect_errno . ') '
            . $DB->connect_error);
        $step = 1;
    }

    //设置 mysqli 字符编码
    $DB->set_charset("utf8");
    $step = 2;
}

$_step = get('step');
if ($_step === '1') {
    $host = post('host', '127.0.0.1');
    $port = post('port', '3306');
    $user = post('user', '');
    $pwd = post('password', '');
    $name = post('dbname', '');

    //实例化 mysqli 类
    $DB = @new mysqli($host, $user, $pwd, $name, $port);

    /**
     * 如果数据库连接失败
     * 给出错误提示，并且结束程序运行
     */
    if ($DB->connect_error) {
        $error = ('Connect Error (' . $DB->connect_errno . ') '
            . iconv('gbk', 'utf-8', $DB->connect_error));
        $step = 1;
    } else {
        //设置 mysqli 字符编码
        $DB->set_charset("utf8");

        if (!$DB->multi_query($SQL)) {
            $step = 1;
            $error = "创建数据表失败，原因：{$DB->error}";
        } else {
            $_c = file_get_contents(__DIR__ . "/config.php");
            $_c = str_replace(
                [
                    '$DB_HOST$',
                    '$DB_PORT$',
                    '$DB_USER$',
                    '$DB_PASSWORD$',
                    '$DB_NAME$',
                ],
                [
                    '$DB_HOST$' => $host,
                    '$DB_PORT$' => $port,
                    '$DB_USER$' => $user,
                    '$DB_PASSWORD$' => $pwd,
                    '$DB_NAME$' => $name,
                ],
                $_c
            );

            if (file_put_contents(__DIR__ . "/config.php", $_c)) {
                $step = 2;//显示创建成功页面

                $res = $DB->query("SELECT nickname FROM users WHERE user_right=1");

                if ($res) {
                    $row = $res->fetch_array();
                    $error = "你已经设置了<b>超级管理员账号</b>，用户名为：<code>{$row['username']}</code>";
                } else {
                    //表示没有设置管理员账号
                }
            } else {
                $step = 1;
                $error = __DIR__ . "/config.php 文件不具有写入权限，请检查！";
            }
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
    <title>安装 - PHP留言板</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.0/dist/css/bootstrap.css">
</head>
<body>

<div class="container col-md-6 col-md-offset-3" style="padding-top: 72px;">
    <?php if ($step === 1): ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">配置数据库信息</h3>
            </div>
            <div class="panel-body">
                <?php if (!empty($error)): ?>
                    <!--输出错误信息-->
                    <div class="alert alert-danger" role="alert"><?php echo $error ?></div>
                <?php endif; ?>

                <form class="form-horizontal" method="post" action="install.php?step=1">
                    <div class="form-group">
                        <label for="host" class="col-sm-2 control-label">MySQL 主机地址</label>
                        <div class="col-sm-10">
                            <input required="required" type="text" class="form-control"
                                   value="<?php echo post('host', '127.0.0.1') ?>"
                                   name="host" placeholder="主机地址，一般是 127.0.0.1 或 localhost">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="port" class="col-sm-2 control-label">MySQL 端口号</label>
                        <div class="col-sm-10">
                            <input required="required" type="number" class="form-control"
                                   name="port" value="<?php echo post('port', '3306') ?>"
                                   placeholder="MySQL 数据库的端口号，默认是 3306">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="user" class="col-sm-2 control-label">MySQL 用户名</label>
                        <div class="col-sm-10">
                            <input required="required"
                                   value="<?php echo post('user', '') ?>"
                                   type="text" class="form-control" name="user">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">MySQL 密码</label>
                        <div class="col-sm-10">
                            <input value="<?php echo post('password', '') ?>"
                                   required="required" type="text" class="form-control" name="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dbname" class="col-sm-2 control-label">MySQL 库名</label>
                        <div class="col-sm-10">
                            <input
                                    value="<?php echo post('dbname', '') ?>"
                                    required="required" type="text" class="form-control" name="dbname">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="col-xs-5">
                                <button type="reset" class="btn btn-default btn-lg btn-block">重置表单</button>
                            </div>
                            <div class="col-xs-7">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">安装</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    <?php elseif ($step === 2): ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">创建超级管理员账号</h3>
            </div>
            <div class="panel-body">
                <?php if (!empty($error)): ?>
                    <!--输出错误信息-->
                    <div class="alert alert-danger" role="alert"><?php echo $error ?></div>
                <?php endif; ?>

                <form class="form-horizontal" method="post" action="install.php?step=1">
                    <div class="form-group">
                        <label for="host" class="col-sm-2 control-label">账号</label>
                        <div class="col-sm-10">
                            <input required="required" type="text" class="form-control"
                                   name="host" placeholder="主机地址，一般是 127.0.0.1 或 localhost">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
</body>
</html>
