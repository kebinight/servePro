/**
 * @作者：xiaofeng
 * @注释：kebin
 * @时间：2017-03-17 14:19
 * @功能描述：
 *      用于调用APP部分原生功能，需要APP实现相应的方法并将方法对象提前注入页面
 *
 * @添加接口：
 *      1)与APP开发组商定方法功能及方法调用方式
 *      2)将方法名加入apiList数组中
 *      3)apiList数组下面的for循环中实现方法
 *
 * @调用接口：
 *      默认是LEMON.方法名，参数根据实现而定
 *
 * JSAPI内部实现主要核心方法：
 * 1)registerAPI 将需要新增的api进行实现并注册
 * 2)JSApiInvoke 调用APP注册对象相应方法执行函数
 *
 */
if (navigator.userAgent.toLocaleLowerCase().indexOf('smartlemon_ios') > 0) {  //ios需要先注入JSApi对象
    document.write('<script src="http://jsapi.com/jsapi.js"><\/script>');
}

(function () {
    //配置分享描述用语
    var defaultConfig = {
        imgUrl: 'http://m.beauty-engine.com/upload/ico/meiyue.png',
        link: 'http://m.chinamatop.com/',
        title: '约见吧',
        desc: '专注高端社交圈',
        success: function () {
        },
        cancel: function () {
        }
    };
    window.shareConfig = defaultConfig;
    window.nativeShare = function (type) {
        LEMON.share[type](window.shareConfig, function () {
        });
    };

    //暂时不知道这个是干嘛的
    window.onBottom = window.onBottom || function () {};
    window.onTopRight = window.onTopRight || function () {};
    window.onBackView = window.onBackView || function () {};
    window.onActiveView = window.onActiveView || function () {};

    //定义LEMON全局变量，用语全局调用jsapi接口函数
    var LEMON = {};
    window.__isAPP = LEMON.isAPP = window.JSApi || navigator.userAgent.toLowerCase().indexOf("smartlemon") >= 0;  //判断页面是否在app的环境中
    var isAPP = LEMON.isAPP;

    //注册api接口
    //obj为全局对象名，填null默认为LEMON
    //names调用接口名，命名规则为xxx.xxx[.xxx]
    var registerAPI = function (obj, names, fun) {
        var n = names.replace(/\..*/, '');
        obj = obj || LEMON;
        obj[n] = obj[n] || {};
        n == names ? obj[n] = fun : registerAPI(obj[n], names.replace(n + '.', ''), fun);
    };

    //调用APP注入的对象对应的方法
    //api 方法名
    //param 参数
    //apiCB 回调方法，apiCB为空的时候  api不会执行回调
    //reType 约定的执行报错返回格式
    var JSApiInvoke = function (api, param, apiCB, reType) {
        var re = reType == 'string' ? '{"data":""}' : '{"code": 1, "errorMsg": "invoke error", "data": ""}';
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


    //这个callback有局限性，就是用户不能离开当前页面
    var apiCallback = function (func) {
        if (!func)
            return '';
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
        'sys.versionName',  //安卓使用
        "sys.isUseLOC", //是否使用缓存  on  off
        "sys.openLOC", //开启缓存
        "sys.closeLOC", //关闭缓存
        "sys.showKeyboard", //显示键盘
        "sys.hideKeyboard", //隐藏键盘
        "sys.QRcode", //二维码扫描
        "sys.update", //android系统更新
        "sys.mediaPlay", //开始播放多媒体
        "sys.back", //设置返回链接
        "sys.clearWebCatch",
        "sys.setTopRight", //设置右上角文字&功能
        "sys.setSex",   //设置性别，并进行相应的操作
        "sys.getSex",   //获取性别
        "sys.copy2Clipper",  //复制到剪切板
        "sys.logout",    //退出
        "sys.device", //获取唯一设备id
        "sys.endReg",    //完成注册
        "sys.delTalk",    //删除会话
        "sys.loginSuccess", //js 端成功执行登录操作
        "show.shareIco", //隐藏分享图标
        "show.shareBanner",  //显示分享banner
        "share.banner", //调出分享的层
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
        "event.imList",
        "event.imTalk",
        "event.login",
        "event.unrefresh",   //禁止下拉刷新
        "event.refreshable",   //允许下拉刷新
        "event.back", //后退事件
        "event.getWXCode",
        "event.invite", //短信分享
        "event.getLocation",
        "event.viewImg",
        "event.viewImgExt",
        "event.tel",
        "event.uploadPic", //选择并上传单个图片 返回服务器地址
        "event.uploadPics", //上传9个图片
        "event.uploadVideo",  //上传视频
        "event.uploadAvatar",  //上传头像 有正方形选择框
        "event.choosePic",  //选择多个图片, 返回json字符串{}
        "event.chooseVideo",  //选择视频
        "event.chooseAuthVideo",  //选择认证视频
        "event.changePic",  //替换某张图片
        "event.toWholeWin",  //跳转页面，该页面可以配置显示范围
        ];

    for (var i = 0, len = apiList.length; i < len && apiList[i]; i++) {
        (function (api) { //api eg:'share.qq'
            switch (api) {
                //获取app本地数据库中数据
                case "db.get":
                //设置相应数据到app本书数据库中
                case "db.set":
                    registerAPI(null, api, function () {
                        var param = {
                            'key': arguments[0],
                            'content': arguments[1] || '',
                            'expires': arguments[2] || 999999
                        };
                        if (!param['key'])
                            return '';
                        if (!isAPP) {
                            if (api == 'db.set')
                                localStorage.setItem(param['key'], param['content']);
                            if (api == 'db.get')
                                return localStorage.getItem(param['key']);
                            return '';
                        }

                        //db.get只用到key  LEMON.db.get 只需要传入一个字符串
                        // ** db.set至少用到key value  LEMON.db.set  至少传入两个参数，字符串  **
                        // invoke可以多传几个变量 set  delete不会用到value和get
                        var invokeResult = JSApiInvoke(api, param, '', 'string');
                        if (invokeResult.indexOf('"data"') != -1) {
                            var re = JSON.parse(invokeResult);
                            return re.data;
                        }
                        else {
                            return invokeResult;
                        }
                    });
                    break;
                case "sys.getSex":
                case "sys.version":
                case "sys.versionName":
                case "sys.device":
                case "sys.isUseLOC":  //是否使用缓存  on  off
                    registerAPI(null, api, function () {
                        var invokeResult = JSApiInvoke(api, '', '', 'string');
                        if (invokeResult.indexOf('"data":') != -1) {
                            var re = JSON.parse(invokeResult);
                            return re.data;
                        }
                        else {
                            return invokeResult;
                        }
                    });
                    break;
                    //无参数   无回调
                case "event.back":
                case "sys.delTalk":
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
                        var imgs = arguments[1], cImg = arguments[0], index = 0, i = 0;
                        for (; i < imgs.length; i++) {
                            if (imgs[i] == cImg) {
                                index = i;
                                break;
                            }
                        }
                        return JSApiInvoke(api, {imgs: imgs, index: index}, '');
                    });
                    break;   
                /**
                 * 特殊接口，仅限用与约见吧业务
                 * status 状态（包括：1:可以继续访问 2: 需要消耗名额 3: 需要购买会员）
                 *
                 */
                case "event.viewImgExt":
                    registerAPI(null, api, function () {
                        var imgs = arguments[1], cImg = arguments[0], index = 0, i = 0, status = arguments[2], user_id = arguments[3],
                            view_id = arguments[4], to_url = arguments[5];
                        for (; i < imgs.length; i++) {
                            if (imgs[i] == cImg) {
                                index = i;
                                break;
                            }
                        }
                        return JSApiInvoke(api, {imgs: imgs, index: index, status: status, user_id: user_id, view_id: view_id, to_url: to_url}, '');
                    });
                    break;
                case "sys.endReg":     
                case "event.imList":
                case "event.unrefresh":
                case "event.refreshable":
                case "sys.clearWebCatch":
                case "sys.openLOC":
                case "sys.mediaPlay":
                case "sys.closeLOC":
                case "share.banner":
                case "show.shareIco":
                case "show.shareBanner":
                case "sys.showKeyboard":
                case "sys.hideKeyboard":
                case "sys.QRcode":
                case "sys.logout":
                case "sys.loginSuccess":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {}, '');
                    });
                    break;
                    //一个字符型参数   无回调
                case "sys.back":
                    registerAPI(null, api, function () {
                        var jump = arguments[0];
                        jump = jump.replace('http://', '').replace('m.chinamatop.com', '');
                        return JSApiInvoke(api, {url: jump}, '');
                    });
                    break;
                case "event.tel":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {tel: arguments[0]}, '');
                    });
                    break;
                //有1个参数
                case "event.imTalk":
                case "sys.setSex":
                case "sys.setTopRight":
                case "event.invite":
                case "sys.copy2Clipper":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {str: arguments[0]}, '');
                    });
                    break;
                case "sys.update":
                    registerAPI(null, api, function () {
                        return JSApiInvoke(api, {url: arguments[0]}, '');
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
                case "event.uploadPic":
                case "event.uploadAvatar":
                case "event.uploadPics":
                case "event.uploadVideo":
                case "event.changePic":
                case "event.choosePic":
                case "event.chooseVideo":
                case "event.chooseAuthVideo":
                case "event.addBtn":
                case 'event.login':
                    registerAPI(null, api, function () {
                        if(api == 'event.choosePic' || api == 'event.changePic') window.lemonChoosePic = arguments[1]; //这里使用固定回调  android会一次选择 多次回调
                        JSApiInvoke(api, {param: arguments[0]}, apiCallback(arguments[1]));
                    });
                    break;
                /**
                 *  传入3个参数，
                 *  @param url 跳转的url(全地址)
                 *  @param type 页面显示方式:1#全屏显示，连时间栏都不要显示 2#全屏显示，显示时间栏 3#显示标题栏
                 *  @param ref 原地址
                 */
                case 'event.toWholeWin':
                    registerAPI(null, api, function () {
                        JSApiInvoke(api, {url: arguments[0], type: arguments[1], ref : arguments[2]});
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
                        if (!param)
                            return '';
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


