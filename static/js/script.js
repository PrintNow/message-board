$(function () {
    // $.toastr.config({
    //     // 设置默认关闭时间为5秒
    //     time: 2500
    // });
    // $.toastr.success('成功', {position: 'top-right'});
    // $.toastr.error('失败', {position: 'top-right'});
})



/**
 * 注册账号
 * @param dom           form 的 DOM
 * @returns {boolean}   永远返回的是 false
 */
function regAccount(dom) {
    var nickname = dom.nickname.value,
        password = dom.password.value,
        email = dom.email.value,
        qq = dom.qq.value,
        sex = dom.sex.value,
        regBtn = dom.regBtn;

    if(nickname.length < 1 || nickname.length > 21){
        $.toastr.warning('用户名长度必须 大于1 小于21', {
            position: 'top-right',
            time: 4000,
            size: 'lg'
        });
        return false;
    }

    if(password.length < 7 || password.length > 16){
        $.toastr.warning('密码长度必须 大于1 小于16', {
            position: 'top-right',
            time: 4000,
            size: 'lg'
        });
        return false;
    }

    if(qq.length < 5){
        $.toastr.warning('QQ账号长度必须 大于5', {
            position: 'top-right',
            time: 4000,
            size: 'lg'
        });
        return false;
    }

    $.ajax({
        url: 'reg.php?action=reg',
        method: 'POST',
        dataType: 'json',
        data: {
            nickname: nickname,
            password: password,
            email: email,
            qq: qq,
            sex: sex
        },
        beforeSend: function () {
            $(regBtn).button('loading');//将按钮改成 加载状态并且禁用点击
        },
        complete: function () {
            $(regBtn).button('reset');
        },
        success: function (res) {
            if (res.msg === undefined) {
                res.msg = '服务器暂时出现未知的错误，请稍后再试！';
            }

            if (res.code === 0) {
                $.toastr.success('账号注册成功！', {
                    position: 'top-right',
                    time: 1800,
                    size: 'lg',
                    callback: function(){
                        window.location.href = "login.php";
                    }
                });
            } else {
                $.toastr.warning('账号注册失败！原因：' + res.msg, {
                    position: 'top-right',
                    time: 7000,
                    size: 'lg'
                });
            }
        },
        error: function (e) {
            $.toastr.error('账号注册失败！<br/>原因：服务器暂时出现未知的错误 或者 你的网络出现问题。请稍后再试！', {
                position: 'top-right',
                time: 8000,
                size: 'lg'
            });
        }
    })

    return false;
}


/**
 * 发表留言
 * @param dom           form 的 DOM
 * @returns {boolean}   永远返回的是 false
 */
function submitMessage(dom) {
    var content = dom.content.value,
        submitBtn = dom.submitBtn;

    if (content === "" || content === undefined) {
        $.toastr.warning('留言内容不能为空！', {position: 'top-right'});
        return false
    }

    $.ajax({
        url: 'api.php?do=submit',
        method: 'POST',
        dataType: 'json',
        data: {
            content: content
        },
        beforeSend: function () {
            $(submitBtn).button('loading');//将按钮改成 加载状态并且禁用点击
        },
        complete: function () {
            $(submitBtn).button('reset');
        },
        success: function (res) {
            if (res.msg === undefined) {
                res.msg = '服务器暂时出现未知的错误，请稍后再试！';
            }

            if (res.code === 0) {
                dom.content.value = "";//清空表单

                $.toastr.success('发表留言成功！', {
                    position: 'top-right',
                    time: 1800,
                    size: 'lg',
                    callback: function(){
                        location.reload();//刷新页面
                    }
                });
            } else {
                $.toastr.warning('发表留言失败！原因：' + res.msg, {
                    position: 'top-right',
                    time: 7000,
                    size: 'lg'
                });
            }
        },
        error: function (e) {
            $.toastr.error('发表留言失败！<br/>原因：服务器暂时出现未知的错误 或者 你的网络出现问题。请稍后再试！', {
                position: 'top-right',
                time: 8000,
                size: 'lg'
            });
        }
    })

    return false;
}