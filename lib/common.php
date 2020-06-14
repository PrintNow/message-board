<?php
/**
 * 公共文件，包含如 数据库连接信息
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */

header("Content-type:text/html;charset=utf-8");


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