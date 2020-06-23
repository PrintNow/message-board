<?php
/**
 *
 * User: chuwen
 * Date Time: 2020/6/15 12:29
 * Email: <chenwenzhou@aliyun.com>
 */

include __DIR__."/lib/common.php";

//退出登录
$logout = get('logout');
if($logout === 'true'){
    setcookie('mbToken', NULL);
    header("Location: index.php");
}