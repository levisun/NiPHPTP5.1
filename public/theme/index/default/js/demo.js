jQuery(function(){
    jQuery.ajax({
        url: "http://api.tp5.com/query.html",
        type: "get",
        headers: {
            "accept": "application/vnd.tp5.v1.0.1+json",
            "authentication": "553da78582d3b704d22c1c1c0c47df6e8df528e1",
        },
        data: {
            method: "account.user.login",
            appid: 1000001,
            appsecret: "appsecret",
            sign: sign({
                appid: 1000001,
                appsecret: "appsecret",
                method: "account.user.login",
            })
        },
    });


function sign (_params) {
    // 先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
    var newkey = Object.keys(_params).sort();

    // 创建一个新的对象，用于存放排好序的键值对
    var newObj = {};
    for(var i = 0; i < newkey.length; i++) {
      // 遍历newkey数组
      newObj[newkey[i]] = _params[newkey[i]];
      // 向新创建的对象中按照排好的顺序依次增加键值对
    }

    var sign = "";
    for (var index in newObj) {
      if (typeof(newObj[index]) != "undefined") {
          sign += index + "=" + newObj[index] + "&";
      }
    }
    sign = sign.substr(0, sign.length - 1);

    sign = md5(sign);

    return sign;
}


});
