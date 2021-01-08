<?php
/**
 *
 * User: chuwen
 * Date Time: 2020/7/4 21:08
 * Email: <chenwenzhou@aliyun.com>
 */

function write_logs(){
    $datetime = new DateTime();
    $now_time = $datetime->format("Y-m-d H:i:s.u");
    $text = "[{$now_time}]\t{$_SERVER['REQUEST_URI']}";
    $text .= "\n[GET 参数]\t".print_r($_GET, true)."\n";
    $text .= "[POST 参数]\t".print_r($_POST, true)."\n";
    $text .= "---------------------------------------------\n\n";

    return file_put_contents(__DIR__."/".date("Y-m-d_H-i").".log", $text, FILE_APPEND);
}