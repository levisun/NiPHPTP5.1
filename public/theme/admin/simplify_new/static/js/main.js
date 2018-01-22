
/**
 * 系统信息
 */
function _monitorSetInfo(_request) {

}

/**
 * 登录
 */
function _handleLogin(_request) {
    var data = "method=login.login.account";
    data += "&"+$("form").serialize();

    $.loading({
        url: _request.api,
        async: false,
        data: data,
    }, function(result){
        if (result.error_msg === 'ILLEGAL') {
            $.reload();
        } else if (result.error_msg === 'SUCCESS' && result.return_code === 'SUCCESS') {
            $.redirect(_request.domain+"admin/settings/info.shtml");
        } else {
            $.uiToast(result.return_msg);
        }
    });
}
