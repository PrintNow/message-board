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
function echoApiData($code=0, $msg='ok', $data=[]){
    $data = [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
    if(count($data) < 1) unset($data['data']);

    return json_encode($data, JSON_UNESCAPED_UNICODE);
}


function formatTime($time){
    //现在的 年 月 日
    list($y, $m, $d, $h_i) = explode("-", date("Y-m-d-H:i"));

    //传入时间的 年 月 日
    list($_y, $_m, $_d, $_h_i) = explode("-", date("Y-m-d-H:i", $time));

    if($y > $_y){
        $prefix = "{$y}-{$m}-{$d}";
    }

    if($y === $_y){
        $prefix = "{$m}-{$d}";
    }

    if($d === $_d){
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
 * @param   int     $maxpage  总页数
 * @param   int     $page    当前页
 * @param   string  $para   翻页参数(不需要写$page),如http://www.example.com/article.php?page=3&id=1，$para参数就应该设为'&id=1'
 * @return  string          返回的输出分页html内容
 */
function multipage($maxpage=5, $page=1, $para = '')
{
    $multipage = '';  //输出的分页内容
    $listnum = 5;     //同时显示的最多可点击页面

    if ($maxpage < 2) {
        return '<ul class="pagination"><li class="active"><a href="?page=1'.$para.'">1</a></li></ul>';
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
        $multipage .= ' <li><a href="#" ><input type="text" size="3"  οnkeydοwn="if(event.keyCode==13) {self.window.location=\'?page=\'+this.value+\'' . $para . '\'; return false;}" ></a></li>';


        $multipage = $multipage ? '<ul class="pagination">' . $multipage . '</ul>' : '';
    }

    return $multipage;
}
