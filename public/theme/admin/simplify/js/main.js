
/**
 * 系统信息
 */
function _monitorSetInfo(_request) {

}

/**
 * 登录
 */
function _handleLogin(_request) {
    var data = "method=account.login.login";
    data += "&"+$("form").serialize();

    $.loading({
        url: _request.api,
        async: false,
        data: data,
    }, function(result){
        if (result.msg === 'ILLEGAL') {
            $.reload();
        } else if (result.msg === 'SUCCESS' && result.code === 'SUCCESS') {
            $.redirect(_request.domain+"admin/settings/info.shtml");
        } else {
            $.uiToast(result.msg);
        }
    });
}
