/**
 * JSAPI内部实现    JSApi.invoke("shareWX", JSON.stringify(msg), "callback");
 */

/**
 * 外部调用方法，如 ： var result = LEMON.shareWX(param, callback);
 * @param json格式参数列表,默认为空
 * @callback 回调函数，默认为空，回调执行时参数为{callback:'',data:{}},callback为当前函数名称，data为result.data
 * @result
 {
   code:0,      //0表示正确执行，其他表示相应错误码
   errorMsg:'', //错误信息
   data:{}      //对于取结果的，数据会以json结构放在data里，否则data为空json
 }
 */

if(navigator.userAgent.toLocaleLowerCase().indexOf('smartlemon_ios')>0){  //ios需要先注入JSApi对象
    document.write('<script src="http://jsapi.com/jsapi.js"><\/script>');
}
(function () {
    var defaultConfig = {
        imgUrl: 'http://m.chinamatop.com/mobile/images/bgq_logo.png',
        link: 'http://m.chinamatop.com/',
        title: '并购帮',
        desc: '专注并购人的生活方式',
        success:function(){},
        cancel:function(){}
    };
    window.shareConfig = defaultConfig;
    window.nativeShare = function (type) {
        LEMON.share[type](window.shareConfig, function(){});
    };
    window.onBottom = window.onBottom || function () {};
    window.onBackView = window.onBackView || function () {};
    window.onActiveView = window.onActiveView || function () {};
    var LEMON = {};
    window.__isAPP = LEMON.isAPP = window.JSApi || navigator.userAgent.toLowerCase().indexOf("smartlemon") >= 0;  //判断页面是否在app的环境中

    var isAPP = LEMON.isAPP;
    var registerAPI = function (obj, names, fun) {
        var n = names.replace(/\..*/, '');
        obj = obj || LEMON;
        obj[n] = obj[n] || {};
        n == names ? obj[n] = fun : registerAPI(obj[n], names.replace(n + '.', ''), fun);
    };

    //apiCB为空的时候  api不会执行回调
    var JSApiInvoke = function (api, param, apiCB, reType) {
        var re = reType == 'string' ? '{"data":""}' : '{"code": 1, "errorMsg": "invoke error", "data": ""}';  //约定的执行报错返回格式
        if (isAPP) {
            try {
                return JSApi.invoke(api, JSON.stringify(param), apiCB);
            }
            catch (e) {
                return re;
            }
        }
        return re;
    };

    var apiCallback = function (func) {
        if (!func) return '';
        var apiCB = 'apiCB' + Math.ceil(Math.random() * 1000000000000);
        window[apiCB] = function (param) {
            func && func(param);
            delete window[apiCB];
        };
        return apiCB;
    };

    //api名称列表
    var apiList = [
        "db.get",
        "db.set",
        "sys.version",
        "sys.isUseLOC",  //是否使用缓存  on  off
        "sys.openLOC",  //开启缓存
        "sys.closeLOC", //关闭缓存
        "sys.showKeyboard",  //显示键盘
        "sys.hideKeyboard",  //隐藏键盘
        "sys.QRcode",  //二维码扫描
        "sys.update",  //android系统更新
        "sys.mediaPlay", //开始播放多媒体
        "sys.back",  //设置返回链接
        "sys.clearWebCatch",
        "sys.logout",
        "sys.device", //获取唯一设备id
        "show.shareIco", //隐藏分享图标
        "share.banner",  //调出分享的层
        "share.QQ",
        "share.QQfriend",
        "share.WX",
        "share.WXfriend",
        "share.WB",
        "env.hasQQ",
        "env.hasWX",
        'login.wx',
        'pay.wx',
        'pay.ali',
        "event.unrefresh",
        "event.back", //后退事件
        "event.getWXCode",
        "event.invite", //短信分享
        "event.getLocation",
        "event.viewImg",
        "event.tel",
        "event.uploadPhoto",
        "event.reuploadPhoto"];

    for (var i = 0, len = apiList.length; i < len && apiList[i]; i++) {
        (function (api) { //api eg:'share.qq'
            switch (api) {
                case "db.get":
                case "db.set":
                    registerAPI(null, api, function () {
                        var param = {
                            'key': arguments[0],
                            'content': arguments[1] || '',
                            'expires': arguments[2] || 999999
                        };
                        if (!param['key']) return '';
                        if (!isAPP) {
                            if (api == 'db.set') localStorage.setItem(param['key'], param['content']);
                            if (api == 'db.get') return localStorage.getItem(param['key']);
                            return '';
                        }

                        //db.get只用到key  LEMON.db.get 只需要传入一个字符串
                        // ** db.set至少用到key value  LEMON.db.set  至少传入两个参数，字符串  **
                        // invoke可以多传几个变量 set  delete不会用到value和get
                        var invokeResult = JSApiInvoke(api, param, '', 'string');
                        if(invokeResult.indexOf('"data"') != -1){
                            var re = JSON.parse(invokeResult);
                            return re.data;
                        }
                        else{
                            return invokeResult;
                        }
                    });
                    break;
                case "sys.version":
                case "sys.device":
                case "sys.isUseLOC":  //是否使用缓存  on  off
                        registerAPI(null, api, function () {
                        var invokeResult = JSApiInvoke(api, '', '', 'string');
                        if(invokeResult.indexOf('"data":') != -1){
                            var re = JSON.parse(invokeResult);
                            return re.data;
                        }
                        else{
                            return invokeResult;
                        }
                    });
                    break;
                //无参数   无回调
                case "event.back":
                    registerAPI(null, api, function () {
                        if (!isAPP) {
                            history.back();
                            return;
                        }
                        return JSApiInvoke(api, {}, '');
                    });
                    break;
                case "event.viewImg":
                    registerAPI(null, api, function () {
                        var imgs = arguments[1], cImg=arguments[0], index=0, i=0;
                        for(;i<imgs.length;i++){
                            if(imgs[i] == cImg) {
                                index = i;
                                break;
                            }
                        }
                        return JSApiInvoke(api, {imgs:imgs, index:index}, '');
                    });
                    break;
                case "event.unrefresh":
                case "sys.clearWebCatch":
                case "sys.openLOC":
                case "sys.mediaPlay":
                case "sys.closeLOC":
                case "share.banner":
                case "show.shareIco":
                case "sys.showKeyboard":
                case "sys.hideKeyboard":
                case "sys.QRcode":
                case "sys.logout":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {}, '');
                    });
                    break;
                //一个字符型参数   无回调
                case "sys.back":
                    registerAPI(null, api, function () {
                        var jump = arguments[0];
                        jump = jump.replace('http://', '').replace('m.chinamatop.com','');
                        return JSApiInvoke(api, {url:jump}, '');
                    });
                    break;
                case "sys.update":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {url:arguments[0]}, '');
                    });
                    break;
                case "event.tel":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {tel:arguments[0]}, '');
                    });
                    break;
                case "event.invite":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {str:arguments[0]}, '');
                    });
                    break;
                //无参数 只用到callback
                case 'login.wx':
                case "event.getWXCode":
                case "event.getLocation":
                case "event.reuploadPhoto":
                    registerAPI(null, api, function () {
                        window.reuploadPhotoCB = arguments[0];
                        return JSApiInvoke(api, {}, apiCallback(arguments[0]));
                        //var re = JSON.parse(JSApiInvoke(api, {}, '', 'string'));
                        //return re.data;
                    });
                    break;
                //有参数 有callback
                case 'pay.wx':
                case 'pay.ali':
                case "event.uploadPhoto":
                    registerAPI(null, api, function () {
                        JSApiInvoke(api, {param:arguments[0]}, apiCallback(arguments[1]));
                    });
                    break;
                case "share.banner":
                case "share.QQ":
                case "share.QQfriend":
                case "share.WX":
                case "share.WXfriend":
                case "share.WB":
                    registerAPI(null, api, function () {
                        var param = arguments[0] || window.shareConfig, cb = arguments[1] || function () {
                            }; //这里ios一定要callback
                        if (!param) return '';
                        if (!isAPP) {
                            //register wx sq
                        }
                        return JSApiInvoke(api, {
                            title: param.title,
                            desc: param.desc,
                            imgUrl: param.imgUrl,
                            link: param.link
                        }, apiCallback(cb));
                    });
                    break;
                default:
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {}, '');
                    });
                    break;
            }
        })(apiList[i]);
    }
    window.LEMON = LEMON;
})();

//LEMON.db.set('name','kate');

//setStringWithKey=>db.set------------key:value==>{key:str, content:str}
//getStringWithKey=>db.get------------key:value==>{key:value}
//getLocation=>db.set------------key:value==>{key:value}
//setStringWithKey=>db.set------------key:value==>{key:value}
//setStringWithKey=>db.set------------key:value==>{key:value}


