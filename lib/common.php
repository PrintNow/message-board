<?php
/**
 * 公共文件，包含如 数据库连接信息
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */

//设置页面编码
header("Content-type:text/html;charset=utf-8");

//设置时区
date_default_timezone_set("PRC");

define("KEY", "2xr5gwRN8At4iVqi@DWDVWrJ*yfW8Cjo");

//定义 根目录路径 常量
define("ROOT_DIR", dirname(__DIR__));

//定义 lib目录 常量
define("LIB_DIR", __DIR__);


include LIB_DIR."/funtions.php";


//引入数据库配置文件
include ROOT_DIR."/config.php";

//判断是否配置了数据库文件
if($DBC['db_host'] === '$DB_HOST$'){
    //如果没有配置 config.php 文件就跳转到安装页面
    die("你还没有安装，<a href='/install.php'>点击进行安装</a>");
}

//实例化 mysqli 类
$DB = new mysqli($DBC['db_host'], $DBC['db_user'], $DBC['db_password'], $DBC['db_name'], $DBC['db_port']);

//设置 mysqli 字符编码
$DB->set_charset("utf8");

//登陆状态 false：未登陆
$isLogin = false;

//如果存在 cookie
if(!empty($_COOKIE['mbToken'])){
    @list($uid, $expireTime, $token) = explode("|-|", authcode($_COOKIE['mbToken'], "DECODE", KEY));

    //Token 过期了，需要重新登录
    if(intval($expireTime) <= time()){
        $isLogin = false;
        setcookie('mbToken', NULL);
//        die("Token 过期了，需要重新登录 <a href='/login.php'>点击去登录</a>");
    }else{
        $res = $DB->query(sprintf("SELECT uid,user_right,nickname,password FROM users WHERE uid=%d",
            intval($uid)-10000));
        $uINFO = $res->fetch_assoc();
        $res->free_result();

        if($token !== crypt($uINFO['password'] . $expireTime, '$1$rasmusle$')){
            $isLogin = false;//鉴权失败，需要重新登录
            setcookie('mbToken', NULL);
//            die("鉴权失败，需要重新登录 <a href='/login.php'>点击去登录</a>");
        }

        $isLogin = true;
    }

    if(!$isLogin){
        //使 cookie 过期
        setcookie("mbToken", "", time()-86400);
    }

    unset($uid, $expireTime, $token, $res);//清理无用变量
}
