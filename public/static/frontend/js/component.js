$.ajaxSetup({
    crossDomain: true,
    xhrFields: {
        withCredentials: true

    }
});

//微信软键盘失去焦点域名错误问题
setTimeout(function() {
    $("body").on('blur', 'input,select,textarea', function(event) {
        setTimeout(function() {
            var scrollHeight = document.documentElement.scrollTop || document.body.scrollTop || 0;
            window.scrollTo(0, Math.max(scrollHeight - 1, 0));
        }, 100);
    });
}, 2000);

// 滚动页计算
function computeRollPage(rollpage, totalPages, number) {
    var array = new Array();
    if (rollpage > 0) {
        rollpage = (rollpage > totalPages) ? totalPages : rollpage - 1;
        var beginIndex = Math.ceil(number + 1 - rollpage / 2);
        if (beginIndex > 1) {
            if (beginIndex > (totalPages - rollpage)) {
                beginIndex = rollpage >= totalPages ? 1 : totalPages - rollpage;
            }
        } else {
            beginIndex = 1;
        }
        var endIndex = beginIndex;
        for (var i = beginIndex; i < rollpage + beginIndex; i++) {
            if (i < totalPages) {
                endIndex++;
            }
        }
    }

    var s = 0;
    for (var i = beginIndex; i <= endIndex; i++) {
        array[s] = i;
        s++;
    }
    return array;
}

/*
 * 获取网页参数数组
 */
function getParams() {
    var url = location.href;
    url = url.substring(url.lastIndexOf("?") + 1);
    var params = url.split("&");
    var paramArr = {};
    var map = "";
    var key = "";
    var value = "";
    for (i = 0; i < params.length; i++) {
        map = params[i].split("=");
        key = map[0];
        value = map[1];
        paramArr[key] = value;
    }
    return paramArr;
}


/*
 * 返回首页
 */
function backindex() {
	window.location.href='http://fin.jrfacai.com/static/frontend/index.html';
	 
}

var ui = {};

$.ajax({
    url: 'http://fin.jrfacai.com/home/base_api/getWeixinUserInfo',
    type: 'GET',
    dataType: 'json',
    async: false,
    data: {referer_url: window.location.href},
})
.done(function(res) {
    if (res.code == 200) {
        ui = res.data;
        // var path = window.location.pathname;
        // if (ui.userType == 0 && (path != "/static/frontend/index.html" || path != "/static/frontend/find.html" || path != "/static/frontend/selectedProjects.html")) {
        //     window.location.href = "http://fin.jrfacai.com/static/frontend/reg.html?s=12315";
        // }
        // else {
        //     if (res.data.is_first_match == 2) {
        //         window.location.href = "./preference.html";
        //     }
        // }
    }else if(res.code == 999) {
    	window.location.href = 'http://fin.jrfacai.com/static/frontend/forbidden.html';
    }else {
        window.location.href = res.data.weixin_auth_url;
    }
});


var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?7fcfdddf9172fe3c90899bbaf1b17cb4";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();


