$(function () {
    // $.toastr.success('成功', {position: 'top-right'});
    // $.toastr.error('失败', {position: 'top-right'});
})


/**
 * 删除发布的留言
 * @param cid
 * @returns {boolean}
 */
function deleteM(cid) {
    var confirmD = confirm("是否确认删除此留言（CID: "+cid+"）？\n此操作不可逆，请谨慎操作！");
    if (confirmD === false) {
        return false;
    }

    $.ajax({
        url: 'api.php?do=delete',
        method: 'POST',
        dataType: 'json',
        data: {
            cid: cid
        },
        success: function (res) {
            if (res.msg === undefined) {
                res.msg = '服务器暂时出现错误，请稍后再试！';
            }

            if (res.code === 0) {
                $.toastr.success('删除留言（CID: ' + cid + '）成功！即将刷新页面', {
                    position: 'top-right',
                    time: 1800,
                    size: 'lg',
                    callback: function () {
                        location.reload();//刷新页面
                    }
                });
                $("tr[data-cid="+cid+"]").remove();
            } else {
                $.toastr.warning('删除留言失败，原因：' + res.msg, {
                    time: 6000,
                    position: 'top-right'
                });
            }
        },
        error: function () {
            $.toastr.error('删除留言失败，请检查你的网络或服务器暂时出现故障，请稍后再试！', {
                time: 8000,
                position: 'top-right'
            });
        }
    });

    return true;
}

/**
 * 删除用户
 * @param uid
 * @returns {boolean}
 */
function deleteUser(uid) {
    var confirmD = confirm("是否确认删除用户（UID: "+uid+"）？\n此操作不可逆，请谨慎操作！");
    if (confirmD === false) {
        return false;
    }

    $.ajax({
        url: 'admin/index.php?action=delete',
        method: 'POST',
        dataType: 'json',
        data: {
            uid: uid
        },
        success: function (res) {
            if (res.msg === undefined) {
                res.msg = '服务器暂时出现错误，请稍后再试！';
            }

            if (res.code === 0) {
                $.toastr.success('删除用户（UID: ' + uid + '）成功！', {
                    position: 'top-right',
                    time: 2000,
                    size: 'lg',
                    callback: function () {
                        // location.reload();//刷新页面
                    }
                });
                $("tr[data-uid="+uid+"]").remove();
            } else {
                $.toastr.warning('删除用户失败，原因：' + res.msg, {
                    time: 6000,
                    position: 'top-right'
                });
            }
        },
        error: function () {
            $.toastr.error('删除用户失败，请检查你的网络或服务器暂时出现故障，请稍后再试！', {
                time: 8000,
                position: 'top-right'
            });
        }
    });
}


/**
 * 监听“编辑用户”事件
 */
$('#editUser').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget),
        uid = button.data('uid'),
        nickname = button.data('nickname'),
        sex = button.data('sex'),
        qq = button.data('qq'),
        email = button.data('email')

    var modal = $(this)
    modal.find('.modal-title').text('编辑用户资料：' + nickname)
    modal.find('.modal-body #uid').val(uid)
    modal.find('.modal-body #nickname').val(nickname)
    modal.find('.modal-body #sex').val(sex)
    modal.find('.modal-body #qq').val(qq)
    modal.find('.modal-body #email').val(email)


    $(".modal-footer #confirm-edit").on("click", function () {
        var $btn = $(this).button('loading')//将按钮显示为 编辑中
        var that = this;

        $.ajax({
            url: 'admin/index.php?action=edit',
            method: 'POST',
            dataType: 'json',
            data: {
                uid: uid,
                nickname: $(".modal-body #nickname").val(),
                password: $(".modal-body #password").val(),
                sex: $(".modal-body #sex").val(),
                qq: $(".modal-body #qq").val(),
                email: $(".modal-body #email").val()
            },
            complete: function () {
                $btn.button('reset')//关闭按钮加载
            },
            success: function (res) {
                if (res.msg === undefined) {
                    res.msg = '服务器暂时出现错误，请稍后再试！';
                }

                if (res.code === 0) {
                    $.toastr.success('编辑用户资料（UID: ' + uid + '）成功！即将刷新页面', {
                        position: 'top-right',
                        time: 2000,
                        size: 'lg',
                        callback: function () {
                            location.reload();//刷新页面
                        }
                    });
                    $(that).off("click");//解除click绑定
                    $(modal).modal('hide');//关闭模态框
                } else {
                    $.toastr.warning('编辑用户资料失败，原因：' + res.msg, {
                        time: 6000,
                        position: 'top-right'
                    });
                }
            },
            error: function () {
                $.toastr.error('编辑用户资料失败，请检查你的网络或服务器暂时出现故障，请稍后再试！', {
                    time: 8000,
                    position: 'top-right'
                });
            }
        });
    });
}).on('hide.bs.modal', function () {
    console.log("关闭模态框");
    $(this).off("click");//解除click绑定
})


/**
 * 退出登录
 */
function logout() {
    console.info("退出登录");

    document.cookie = "mbToken=;expires=0";

    $.toastr.success('退出登陆成功！', {
        position: 'top-right',
        time: 1800,
        size: 'lg',
        callback: function () {
            location.reload();//刷新页面
        }
    });
}


/**
 * 登陆账号
 * @param dom
 * @returns {boolean}
 */
function loginAccount(dom) {
    var account = dom.account.value,
        password = dom.password.value,
        loginBtn = dom.regBtn;

    if (account.length < 1) {
        $.toastr.warning('账号长度必须大于1', {
            position: 'top-right',
            time: 4000,
            size: 'lg'
        });
        return false;
    }

    $.ajax({
        url: 'login.php?action=login',
        method: 'POST',
        dataType: 'json',
        data: {
            account: account,
            password: password
        },
        beforeSend: function () {
            $(loginBtn).button('loading');//将按钮改成 加载状态并且禁用点击
        },
        complete: function () {
            $(loginBtn).button('reset');
        },
        success: function (res) {
            if (res.msg === undefined) {
                res.msg = '服务器暂时出现未知的错误，请稍后再试！';
            }

            if (res.code === 0) {
                $.toastr.success('账号登录成功！', {
                    position: 'top-right',
                    time: 1800,
                    size: 'lg',
                    callback: function () {
                        window.location.href = "index.php";
                    }
                });
            } else {
                $.toastr.warning('账号登录失败！原因：' + res.msg, {
                    position: 'top-right',
                    time: 7000,
                    size: 'lg'
                });
            }
        },
        error: function (e) {
            $.toastr.error('账号登录失败！<br/>原因：服务器暂时出现未知的错误 或者 你的网络出现问题。请稍后再试！', {
                position: 'top-right',
                time: 8000,
                size: 'lg'
            });
        }
    })

    return false;
}


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

    if (nickname.length < 1 || nickname.length > 21) {
        $.toastr.warning('用户名长度必须 大于1 小于21', {
            position: 'top-right',
            time: 4000,
            size: 'lg'
        });
        return false;
    }

    if (password.length < 7 || password.length > 16) {
        $.toastr.warning('密码长度必须 大于1 小于16', {
            position: 'top-right',
            time: 4000,
            size: 'lg'
        });
        return false;
    }

    if (qq.length < 5) {
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
                    callback: function () {
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
                    callback: function () {
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