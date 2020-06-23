<?php
/**
 *
 * User: chuwen
 * Date: 2020/6/13
 * Email: <chenwenzhou@aliyun.com>
 */

/**
 * 获取客户端IP
 * @return array|false|string
 */
function getIp()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                $ip = getenv("REMOTE_ADDR");
            else
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                    $ip = $_SERVER['REMOTE_ADDR'];
                else
                    $ip = "unknown";
    return ($ip);
}


//function bind_param(&$mysqli, $table, $data){
//    $sql = "INSERT INTO users";
//    $sql = &$mysqli->prepare("INSERT INTO users (nickname,password,qq,email,sex,summary,reg_time,reg_ip) "
//        ."VALUES ('%s', '%s', %d, '%s', %d, '%s', %d, '%s')");
//}

/**
 * 避免重复造轮子，json 信息格式化输出
 * @param int $code
 * @param string $msg
 * @param array $data
 * @return false|string
 */
function echoApiData($code = 0, $msg = 'ok', $data = [])
{
    $data = [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
    if (count($data) < 1) unset($data['data']);

    return json_encode($data, JSON_UNESCAPED_UNICODE);
}


function formatTime($time)
{
    //现在的 年 月 日
    list($y, $m, $d, $h_i) = explode("-", date("Y-m-d-H:i"));

    //传入时间的 年 月 日
    list($_y, $_m, $_d, $_h_i) = explode("-", date("Y-m-d-H:i", $time));

    if ($y > $_y) {
        $prefix = "{$_y}-{$_m}-{$_d}";
    }

    if ($y === $_y) {
        $prefix = "{$_m}-{$_d}";
    }

    if ($d === $_d) {
        $prefix = '今天';
    }

    return "{$prefix} {$_h_i}";
}

/**
 * 2020/06/02 16:26
 * @param string $field GET 请求字段
 * @param string $default 如果不存在，默认输出的值
 * @return boolean|string   输出结果
 * @author Chuwen <wenzhouchan@gmail.com>
 *
 * 获取 GET 参数，并给予默认值，封装成函数
 * 避免直接写，多出不必要的判断
 *
 */
function get($field = '', $default = false)
{
    if (!isset($_GET[$field])) return $default;
    return $_GET[$field];
}

/**
 * 2020/06/02 16:26
 * @param string $field POST 请求字段
 * @param string $default 如果不存在，默认输出的值
 * @return boolean|string   输出结果
 * @author Chuwen <wenzhouchan@gmail.com>
 *
 * 获取 POST 参数，并给予默认值，封装成函数
 * 避免直接写，多出不必要的判断
 *
 */
function post($field = '', $default = false)
{
    if (!isset($_POST[$field])) return $default;
    return $_POST[$field];
}


/**
 * @param int $maxpage 总页数
 * @param int $page 当前页
 * @param string $para 翻页参数(不需要写$page),如http://www.example.com/article.php?page=3&id=1，$para参数就应该设为'&id=1'
 * @return  string          返回的输出分页html内容
 */
function multipage($maxpage = 5, $page = 1, $para = '')
{
    $multipage = '';  //输出的分页内容
    $listnum = 5;     //同时显示的最多可点击页面

    if ($maxpage < 2) {
        return '<ul class="pagination"><li class="active"><a href="?page=1' . $para . '">1</a></li></ul>';
    } else {
        $offset = 2;
        if ($maxpage <= $listnum) {
            $from = 1;
            $to = $maxpage;
        } else {
            $from = $page - $offset; //起始页
            $to = $from + $listnum - 1;  //终止页
            if ($from < 1) {
                $to = $page + 1 - $from;
                $from = 1;
                if ($to - $from < $listnum) {
                    $to = $listnum;
                }
            } elseif ($to > $maxpage) {
                $from = $maxpage - $listnum + 1;
                $to = $maxpage;
            }
        }

        $multipage .= ($page - $offset > 1 && $maxpage >= $page ? '<li><a href="?page=1' . $para . '" >1...</a></li>' : '') .
            ($page > 1 ? '<li><a href="?page=' . ($page - 1) . $para . '" >&laquo;</a></li>' : '');

        for ($i = $from; $i <= $to; $i++) {
            $multipage .= $i == $page ? '<li class="active"><a href="?page=' . $i . $para . '" >' . $i . '</a></li>' : '<li><a href="?page=' . $i . $para . '" >' . $i . '</a></li>';
        }

        $multipage .= ($page < $maxpage ? '<li><a href="?page=' . ($page + 1) . $para . '" >&raquo;</a></li>' : '') .
            ($to < $maxpage ? '<li><a href="?page=' . $maxpage . $para . '" class="last" >...' . $maxpage . '</a></li>' : '');
        $multipage .= ' <li><a><input type="text" size="3"  οnkeydοwn="if(event.keyCode===13) {self.window.location=\'?page=\'+this.value+\'' . $para . '\'; return false;}" ></a></li>';


        $multipage = $multipage ? '<ul class="pagination">' . $multipage . '</ul>' : '';
    }

    return $multipage;
}


/**
 * desc 加密和解密
 * @param string $string 需要加密或解密的字符串
 * @param string $operation DECODE:解密;ENCODE:加密;
 * @param string $key 秘钥
 * @param int $expiry 密文有效期
 * @return bool|string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;

    // 密匙
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
//解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
        sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}