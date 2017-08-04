$.util = {
    //获取当前最新版本
    version: function() {
        if($.util.isAndroid) {
            return 3;
        } else if($.util.isIOS) {
            return 1.0;
        } else {
            return 1.0;
        }
    },
    //获取下载链接，不同系统不同链接
    getdownUrl: function() {
        if($.util.isAndroid) {
            return 'http://m.beauty-engine.com/app/app-meiyue-release.apk';
        } else if($.util.isIOS) {
            return 'https://itunes.apple.com/us/app/mei-yue-quan-mei-nu-jing-ying/id1188145960?l=zh&ls=1&mt=8';
        } else {
            return null;
        }
    },
    //由于现在前端把礼物写死了，所以这边只能先这样实现
    getGift: function(giftno) {
        var gifts = [
            {},
            {
                name : '海洋之心',
                pic : '/upload/gift/chatpic/icon_sea.png',
                anim : 'gif-sea.gif'
            },
            {
                name : '梦幻城堡',
                pic : '/upload/gift/chatpic/icon_castle.png',
                anim : 'gif-castle.gif'
            },
            {
                name : '跑车',
                pic : '/upload/gift/chatpic/icon_car.png',
                anim : 'gif-car.gif'
            },
            {
                name : '怦然心动',
                pic : '/upload/gift/chatpic/icon_heart.png',
                anim : 'gif-heart.gif'
            },
            {
                name : '巧克力雨',
                pic : '/upload/gift/chatpic/icon_chocolate.png',
                anim : 'gif-chocolate.gif'
            },
            {
                name : '豪华游轮',
                pic : '/upload/gift/chatpic/icon_yacht.png',
                anim : 'gif-yacht.gif'
            },
            {
                name : '飞吻',
                pic : '/upload/gift/chatpic/icon_kiss.png',
                anim : 'gif-kiss.gif'
            },
            {
                name : 'roseonly',
                pic : '/upload/gift/chatpic/icon_rose.png',
                anim : 'gif-rose.gif'
            }
        ];
        return gifts[giftno];
    },
    //返回id查找原素，没有找到时，防止为空，会构造一个
    id: function (str) {
        return document.getElementById(str) || document.createElement('span');
    },
    getParam: function (e, t) {  //获取url参数
        var n = arguments[1] || window.location.search, r = new RegExp("(^|&)" + e + "=([^&]*)(&|$)", "i"), i = n.substr(n.indexOf("?") + 1).match(r);
        return i != null ? i[2] : "";
    },
    //
    alert: function (str, t) {
        if(str) {
            $('.alert').remove();
            $('body').append('<div class="alert"><span>' + str + '</span></div>');
            setTimeout(function () {
                $('.alert').hide();
            }, t || 1500);
        }
    },
    //双按钮确认框
    confirm: function (title, msg, hR, hL, btnL, btnR) {
        $('.alert7-confirm').remove();
        var bL = btnL ? btnL : '取消';
        var bR = btnR ? btnR : '确定';
        var confirm = '<div class="raper flex flex_center alert7-confirm">' +
            '<div class="popup" style="display: block;">' +
            '<div class="popup_con">' +
            '<p class="aligncenter" style="font: 18px; font-weight: bold">'+ title +'</p>' +
            '<h3 class="aligncenter lagernum" style="font-size: 14px">'+ msg +'</h3></div>' +
            '<div class="popup_footer flex flex_justify">' +
            '<span class="footerbtn color_y btnL alert7-action-item">'+ bL +'</span>' +
            '<span class="footerbtn gopay btnR alert7-action-item">'+ bR +'</span></div></div></div>';
        $('body').append(confirm);
        $(document).on('touchstart',function(e){
        	e.preventDefault();
        });
        $.util.tap($('.alert7-action-item.btnL'), function(e) {
            if (hL && hL instanceof Function) {
                hL();
            }
            $('.alert7-confirm').remove();
            $(document).off('touchstart');
            return false;
        });
        $.util.tap($('.alert7-action-item.btnR'), function(e) {
            if (hR && hR instanceof Function) {
                hR();
            }
            $('.alert7-confirm').remove();
            $(document).off('touchstart');
            return false;
             
        });
    },
    //单按钮确认框
    sconfirm: function (title, msg, btn, calfun) {
        $('.alert7-confirm').remove();
        var btn = btn ? btn : '确定';
        var calfun = calfun ? calfun : null;
        var confirm = '<div class="raper flex flex_center alert7-confirm">' +
            '<div class="popup" style="display: block;">' +
            '<div class="popup_con">' +
            '<p class="aligncenter" style="font: 18px; font-weight: bold">'+ title +'</p>' +
            '<h3 class="aligncenter lagernum" style="font-size: 14px; max-height: 6rem;overflow: auto;">'+ msg +'</h3></div>' +
            '<div class="popup_footer flex flex_justify">' +
                '<span class="gopay alert7-action-item btn" style="display: block;width:100%;text-align: center;box-shadow: 0 -1px 0 rgba(0,0,0,.1);">'+ btn +'</span>' +
            '</div>';
        $('body').append(confirm);
        $(document).on('touchstart',function(e){
            //e.preventDefault();
        });
        $.util.tap($('.alert7-action-item.btn'), function(e) {
            if (calfun && calfun instanceof Function) {
                calfun();
            }
            $('.alert7-confirm').remove();
            $(document).off('touchstart');
            return false;
        });
    },
    confirm2: function (title, msg, hR, hL, btnL, btnR) {
        $('.alert7-confirm').remove();
        var bL = btnL ? btnL : '取消';
        var bR = btnR ? btnR : '确定';
        var confirm = '<div id="Alert7" class="alert7-confirm">' +
            '<div class="alert7-container"><div class="alert7-title">' + title +
            '</div><div class="alert7-message">' + msg +
            '</div><div class="alert7-actions"><button class="alert7-action-item btnL">' + bL +
            '</button><button class="alert7-action-item btnR">' + bR
            + '</button></div></div></div>';
        $('body').append(confirm);
//      $('body,html').css({'height':'100%','overflow-y':'hidden'});
        $(document).off('click', '.alert7-action-item.btnL');
        $(document).on('click', '.alert7-action-item.btnL', function () {
            if (hL && hL instanceof Function) {
                hL();
            }
            $('.alert7-confirm').remove();
//           $('body,html').css({'height':'auto','overflow-y':'scroll'});
        })
        $(document).off('click', '.alert7-action-item.btnR');
        $(document).on('click', '.alert7-action-item.btnR', function () {
            if (hR && hR instanceof Function) {
                hR();
            }
            $('.alert7-confirm').remove();
//           $('body,html').css({'height':'auto','overflow-y':'scroll'});
        })
    },
    showPreloader: function (str) {
        var div = document.createElement('div');
        div.className = 'loader';
        var divInner = document.createElement('div');
        divInner.className = 'loader-inner';
        div.appendChild(divInner);
        div.style.cssText = 'width:120px;';
        var divText = document.createElement('div');
        divText.className = 'loader-text';
        divInner.appendChild(divText);
        var divTitle = document.createElement('div');
        divTitle.className = 'loader-title';
        divTitle.innerHTML = '请稍候';
        if(str) {
            divTitle.innerHTML = str;
        }
        divInner.appendChild(divTitle);
        document.body.appendChild(div);
    },
    showPreloader2: function (str) {
        $.util.hidePreloader();
        var div = document.createElement('div');
        div.className = 'loader';
        var divInner = document.createElement('div');
        divInner.className = 'loader-inner';
        div.appendChild(divInner);
        if (str) {
            div.style.cssText = 'width:120px;height:70px';
            var divTitle = document.createElement('div');
            divTitle.className = 'loader-title';
            divTitle.innerHTML = str;
            divInner.appendChild(divTitle);
        }
        document.body.appendChild(div);
        var divText = document.createElement('div');
        divText.className = 'loader-text';
        divInner.appendChild(divText);
    },
    hidePreloader: function () {
        $('.loader').remove();
    },
    /**
     * 模板处理
     * @param json
     * @param tpl
     * @returns {XML|string|*|void}
     */
    jsonToTpl: function (json, tpl) {
        return tpl.replace(/{#(\w+)#}/g, function (a, b) {
            return json[b] === 0 ? '0' : (json[b] || '');
        });
    },
    /**
     * 批量处理json列表数据
     * @param contentId string   容器id , 传入空字符串‘’的话，会返回组装好的html
     * @param tplId string 模板id
     * @param data json数据列表  array
     * @param func  处理json数据的方法，可选 会传入当前json对象
     * @returns {string}
     */
    dataToTpl: function (contentId, tplId, data, func) {
        if (!data.length)
            return '';
        var html = [], tpl = $.util.id(tplId).text;
        $.each(data, function (i, d) {
            if (func)
                d = func(d);
            html.push($.util.jsonToTpl(d, $.util.id(tplId).text));
        });
        if (contentId)
            $('#' + contentId).html(html.join(''));
        return html.join('');
    },
    /**
     * 去掉字符串两端空格
     * @param str
     */
    trim: function (str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    },
    /**
     * 封装的ajax请求
     * 此函数可以预先处理一些业务外的事务
     * @param {array} obj [
     *
     * ]
     */
    ajax: function (obj) {
        //进行防多次提交处理
        var flagKey = (obj.url) ? obj.url : 'flagKey';
        if(!window.submittingFlag) {
            window.submittingFlag = [];
        }
        if(window.submittingFlag[flagKey]) {
            return false;
        }
        window.submittingFlag[flagKey] = true;

        var successCb = obj.func;
        if (!obj.url) {
            obj.url = '';
        }

        if (!obj.dataType) {
            obj.dataType = 'json';
        }
        if (!obj.type) {
            obj.type = 'post';
        }
        if(obj.showLoading) {
            $.util.showPreloader(obj.loadingText ? obj.loadingText : null);
        }
        obj.success = function (json) {
            $.util.hidePreloader();
            switch (parseInt(json.code)) {
                case 200 :
                    if(successCb) {
                        successCb(json);
                    }
                    window.submittingFlag[flagKey] = false;
                    break;
                case 201 :  //注册步骤未完成
                case 202 :  //账号被限制登录
                case 203 :  //未注册的账号
                    $.util.alert(json.msg);
                   if(json.data.redirect_url) {
                       setTimeout(function() {
                           location.href = json.data.redirect_url;
                       }, 1000);
                   }
                   break;
                case 403 :
                    $.util.alert('请先登录');
                    setTimeout(function () {
                        if ($.util.isAPP) {
                            LEMON.event.login();
                        } else {
                            location.href = json.redirect_url;
                        }
                    }, 1000);
                    break;
                default :
                    if(json.errmsg) {
                        $.util.alert(json.errmsg);
                        window.submittingFlag[flagKey] = false;
                    }
                    break;
            }
        };
        obj.statusCode = {
            404: function () {
                $.util.alert('请求页面不存在');
            },
            500: function () {
                $.util.alert('服务器繁忙');
            }
        };
        obj.error = function (XMLHttpRequest, textStatus, errorThrown) {
            $.util.hidePreloader();
            $.util.alert('服务器错误-' + textStatus);
            window.submittingFlag[flagKey] = false;
        };
        $.ajax(obj);
    },
    //循环轮播
    loop: function (opt) {
        return new scroll(opt);
    },
    //轮播图  传入的都是
    loopImg: function (fatherDom, child, tab, dom, speed) {
        return $.util.loop({
            tp: 'img', //图片img或是文字text
            //min : 5,
            loadImg: true,
            moveDom: fatherDom, // eg: $('#loopImgUl')
            moveChild: child, //$('#loopImgUl li')
            tab: tab, //$('#loopImgBar li')
            loopScroll: this.loopImg.length > 1,
            autoTime: speed,
            lockScrY: true,
            imgInitLazy: 1000,
            //loopScroll:true,
            step: dom.width(),
            //enableTransX:true,
            index: 1,
            viewDom: dom,
            fun: function (index) {
            }
        });
    },
    /**
     * 读取COOKIE
     */
    getCookie: function (name) {
        var reg = new RegExp("(^| )" + name + "(?:=([^;]*))?(;|$)"), val = document.cookie.match(reg);
        return val ? (val[2] ? unescape(val[2]).replace(/(^")|("$)/g, "") : "") : null;
    },
    /**
     * 写入COOKIES
     */
    setCookie: function (name, value, expires, path, domain, secure) {
        var exp = new Date(), expires = arguments[2] || null, path = arguments[3] || "/", domain = arguments[4] || null, secure = arguments[5] || false;
        expires ? exp.setMinutes(exp.getMinutes() + parseInt(expires)) : "";
        document.cookie = name + '=' + escape(value) + (expires ? ';expires=' + exp.toGMTString() : '') + (path ? ';path=' + path : '') + (domain ? ';domain=' + domain : '') + (secure ? ';secure' : '');
    },
    loginWX: function (cb) {
        if (window.__isAPP) {
            LEMON.login.wx(cb);
        } else if ($.util.isWX) {
            location.href = '/wx/get-user-jump';
        }
    },
    /**
     * @param id  容器dom id
     */
    showLoading: function (id) {
        $('#' + id).html('<div class="loading"></div>');
    },
    /**
     * @param id  容器dom id
     */
    hideLoading: function (id) {
        $('#' + id).html('');
    },
    //初始化滚动加载列表图 预加载图片的id和最大每次加载的图片数
    initLoadImg: function (listId, maxNum) {
        var data = {cache: []}, img = $("#" + listId + " img");
        img.each(function (i) {
            var dom = $(this), init_src = dom.attr('init_src');
            init_src && data.cache.push({
                url: init_src,
                dom: dom
            });
        });
        data.num = data.cache.length;
        data.viewHeight = $(window).height();
        data.scrollOffsetH = 500;
        window._images_data = data;
        window._images_maxLoadNum = maxNum || 100;
        $.util.loadImg();
    },
    //滚动加载列表图
    loadImg: function (tp) {
        // 滚动条的高度
        var scrollHeight = document.body.scrollTop, d = window._images_data, count=0;
        if (!d || d.num == 0) {
            return;
        }
        // 已经卷起的高度+可视区域高度+偏移量，即当前显示的元素的高度
        visibleHeight = d.viewHeight + scrollHeight + d.scrollOffsetH;
        $.each(d.cache, function (i, data) {
            var em = data.dom, imgH = em.offset().top;
            // 图片在后面两屏范围内，并且未被加载过
            //if(tp=='detPC')$('#commDesc').append(['^'+visibleHeight, imgH].join('-'));
            if (visibleHeight > imgH && !em.attr("loaded") && count < window._images_maxLoadNum) {
                // 加载图片
                //em.attr('h', [d.viewHeight , scrollHeight , d.scrollOffsetH, visibleHeight, imgH].join(','))
                data.url && em.attr("src", data.url);
                em.removeAttr('init_src');
                em.attr("loaded", d.num + 1);
                count++;
                d.num--;
            }
        });
    },
    //滚动事件
    listScroll: function (listId, loadFunc) {
        if (window.holdLoad) {
            return false;
        }
        var obj = this, st = document.body.scrollTop;
        if (loadFunc && st >= (($(document).height() - $(window).height()) - 400)) {
            //如果开始加载列表则马上停止继续滑动响应
            window.holdLoad = true;
            loadFunc();
            $.util.showLoadingFooter(listId);
            $.util.initLoadImg(listId);
        } else {
            $.util.loadImg();
        }
    },
    showLoadingFooter: function(id) {
        $('#' + id).after('<div id="loading-footer" class="empty_container"><div class="empty-content  mt40"><div class="empty-loade"><img src="/mobile/css/img/loading2.gif"/>努力加载中</div>' +
            '</div></div>');
    },
    hideLoadingFooter: function() {
        $('#loading-footer').remove();
    },
    wxUploadPic: function (func) {
        wx.chooseImage({
            count: 1, // 默认9
            sizeType: ['compressed'], // ['original', 'compressed'] 可以指定是原图还是压缩图，默认二者都有
            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
            success: function (res) {
                var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                wx.uploadImage({
                    localId: localIds[0], // 需要上传的图片的本地ID，由chooseImage接口获得
                    isShowProgressTips: 1, // 默认为1，显示进度提示
                    success: function (res) {
                        var serverId = res.serverId; // 返回图片的服务器端ID
                        func && func(serverId);
                    }
                });
            }
        });
    },
    isLogin: function () {
        if (!$.util.isAPP) {
            return true;
        }
        return !!$.util.getCookie('token_uin');
    },
    goLogin: function() {
        $.util.alert('请先登录');
        setTimeout(function() {
            if ($.util.isAPP) {
                LEMON.event.login();
            } else {
                location.href = '/user/login?redirect_url=' + encodeURI(document.URL);
            }
        }, 1000);
    },
    staticLogin: function () {
        //if($.util.isAPP){
        var ifr = document.createElement('iframe');
        ifr.style.display = 'none';
        ifr.src = '/user/login_status';
        document.body.appendChild(ifr);
        //}
    },
    isMobile: function (str) {
        var reg = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
        return reg.test(str);
    },
    /**
     * obj jq对象(最好不要写body,不要跟轮播的地方重合了,或是跟其他使用touch的地方重合)
     * func是要处理的方法, 防止点透可以return false;
     * tp默认norm  一般选默认
     */
    tap: function (obj, func, tp) {
        var limit = 0, max = 8, //移动超出max像素忽略本次事件
                range, p, ranges = {norm: [50, 1000], solw: [200, 1000]};

        tp = tp ? tp : 'norm';
        range = ranges[tp];
        obj.off('touchstart');
        obj.off('touchend');
        obj.on('touchstart', function (e) {
            limit = (new Date()).getTime();
            p = $.util.getPosition(e);
        });
        obj.on('touchend', function (e) {
            var p2 = $.util.getPosition(e), x = Math.abs(p2.x - p.x), y = Math.abs(p2.y - p.y);
            if (x > max || y > max)
                return;

            limit = (new Date()).getTime() - limit;
            if (limit > range[0] && limit < range[1]) {
                return func(e);
            }
        });

    },
    /**
     * type == 'zp'的时候使用zepto的tap  否则用util的tap
     */
    onbody: function (func, type) {
        var tapfun = function (e) {
            var target = e.srcElement || e.target, em = target, i = 1;
            while (em && !em.id && i <= 3) {
                em = em.parentNode;
                i++;
            }
            if (!em || !em.id)
                return;
            return func(em, target);
        };
        type == 'zp' ? $('body').on('tap', tapfun) : $.util.tap($('body'), tapfun);
    },
    getPosition: function (e) {
        var touch = e.changedTouches ? e.changedTouches[0] : e;
        return {
            x: touch.pageX,
            y: touch.pageY
        };
    },
    viewImg: function (csrc, imgs) {
        if ($.util.isWX) {
            if (window.WeixinJSBridge) {
                WeixinJSBridge.invoke('imagePreview', {
                    'current': csrc,
                    'urls': imgs}
                );
            }
        } else if ($.util.isAPP) {
            LEMON.event.viewImg(csrc, imgs);
        }
    },
    report: function (ptag) {
        var act = 'v', param = ['/admin/report/logger?',
            'screen=' + window.screen.height + '*' + window.screen.width,
            //'refer=' + (document.referrer||document.domain).replace(/http:\/\/|https:\/\//, '').replace(/\/.*|\?.*/, '')
            'refer=' + encodeURI(document.referrer || document.domain)
        ];
        if (ptag) {
            act = 'c';
            param.push('ptag=' + ptag);
        } else if ($.util.getParam('ptag')) {
            param.push('ptag=' + $.util.getParam('ptag'));
        }
        param.push('act=' + act);
        (new Image).src = param.join('&');
    },
    checkUserinfoStatus: function () {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "/home/get-userinfo-status",
            success: function (res) {
                if (res.status) {
                    $('#shadow').hide();
                    $('#checkBtn').hide();
                } else {
                    $('#shadow').show();
                    $('#checkBtn').show();
                    $('#no, #yes, #shadow').on('click', function () {
                        setTimeout(function () {
                            $('#shadow').hide();
                            $('#checkBtn').hide();
                        }, 301);
                    });
                }
            }
        });
    },
    /**
     * 检查登录态
     * @param string url 如果已登录，跳转的页面
     */
    checkLogin: function (url) {
        if ($.util.isLogin()) {
            if (!url) {
                return true;
            }
            location.href = url;
        } else {
            $.util.alert('请登录后再操作', 1000);
            setTimeout(function () {
                if ($.util.isAPP) {
                    LEMON.event.login();
                } else {
                    location.href = '/user/login?redirect_url=' + encodeURI(document.URL);
                }
            }, 1000);
            return false;
        }
    },
    /**
     * 单文件上传预览
     * @param {string} inputId  上传input id
     * @param {string} showId   显示图片的控件 id
     * @param {function} func   回掉方法
     * @author caowenpeng
     * @returns {undefined}
     */
    singleImgPreView: function (inputId, showId, func) {
        var fileinput = document.getElementById(inputId);
        fileinput.addEventListener("change", function (e) {
            var files = this.files;
            var file = files[0];
            // image.classList.add("")
            var image = document.getElementById(showId);
            image.file = file;
            var reader = new FileReader();
            reader.onload = (function (aImg) {
                return function (e) {
                    aImg.src = e.target.result;
                };
            }(image));
            var ret = reader.readAsDataURL(file);
            var canvas = document.createElement("canvas");
            ctx = canvas.getContext("2d");
            image.onload = function () {
                ctx.drawImage(image, 100, 100);
            };
            func && func(inputId);
        }, false);
    },
    /**
     * zepto ajax 不能将formdata 对象成功提交故重新封装了原生xmlRequest
     * @param {string} url 请求路径
     * @param {string} type 请求类型
     * @param {object} data 数据
     * @param {function} func   回调函数 接收json返回
     * @returns {undefined}
     */
    zajax: function (url, type, data, func) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                func(JSON.parse(xhr.responseText));
            }
        };
        xhr.open(type, url, true);
        xhr.send(data);
    },
    /**
     * 异步加载数据列并显示到页面
     * 传入的参数opt为一个数组
     * opt = [
     *      'id': 显示列表项的容器id
     *      'gurl':请求链接,
     *      'page':加载数据页码,
     *      'tpl':显示数据项使用的模板id，这里使用的模板引擎为mustache,
     *      'more':数据加载方式，true为追加，false为直接覆盖,
     *      'func':回调函数,对返回数据进行过滤等操作,
     *      'ifblank':页面无数据时显示,格式为html文本，直接加载到容器中,
     *      'key': 返回的json中数据项的key名称,
     *      'query':
     * ]
     * @param {object} opt
     * @returns {undefined}
     */
    asyLoadData: function (opt) {
        var gurl = opt.gurl;
        var more = opt['more'];
        var template = $(opt.tpl).html();
        var blank = opt['ifblank'];
        var key = opt.key||'datas';
        var page = opt.page || 1;
        window.holdLoad = true;  //禁止滚动调用本函数
        Mustache.parse(template);
        if (!opt['query']) {
            gurl = gurl + opt.page;
        } else {
            gurl = gurl + opt.page + opt['query'];
        }
        $.getJSON(gurl, function (data) {
            if (opt['func']) {
                data = opt['func'](data);
            }
            window.holdLoad = false; //开启滚动调用本函数
            $.util.hideLoadingFooter();
            if (data && data.code === 200) {
                var rendered = Mustache.render(template, data);
                if (!data[key].length) {
                    window.holdLoad = true;  //禁止滚动调用本函数
                    if(page == 1 && blank) {
                        $(opt.id).html(blank);
                    } else if(page > 1){
                        $(opt.id).append('<p class="smallarea aligncenter mt20">已经到底了</p>');
                    }
                } else {
                    curpage++;
                    if (more) {
                        $(opt.id).append(rendered);
                    } else {
                        $(opt.id).html(rendered);
                    }
                }
            }
        });
    },
    /**
     * 初始化点击选中图片组件
     * @param id 需要相应事件的节点id
     * @param max 限制最多能上传的图片张数
     *
     * 注意点：
     * 1) dom.data('max')记录了剩余可选图片张数
     */
    choosImgs: function (id, max) {
        var max = max||9, dom = $('#' + id);
        dom.data('max', max);
        $.util.tap(dom, function (e) {
            var target = e.srcElement || e.target, em = target, i = 1;
            if (em.nodeName == 'IMG')
                em = em.parentNode;
            if (em.nodeName == 'DT')
                em = em.parentNode;
            if (em.nodeName != 'DL')
                return;
            var cid = $(em).data('id');
            if (cid == 'up') {
                if (dom.data('max') == 0)
                    return;
                LEMON.event.choosePic({'key': id, 'max': dom.data('max')}, function (res) {
                    res = JSON.parse(res);
                    var cdom = $('#' + res.key), len = max - cdom.data('max'), up = cdom.find("[data-id=up]");
                    if (res.hasOwnProperty('index')) {
                        cdom.find('img').eq(res.index).attr('src', 'http://image.com/' + res.key + '/' + res.index);
                        return;
                    }

                    if (res.count) {
                        for (var i = 0; i < res.count; i++) {
                            var src = $.util.isIOS ? 'src="http://image.com/' + res.key + '/' + (len + i) + '"' : '';
                            up.before('<dl class="Idcard" data-id="' + (len + i) + '"><dt><img ' + src + '/></dt></dl>');
                        }
                        len = cdom.find('dl').length - 1;
                        cdom.data('max', max - len);
                        //if (len == max)
                        if (len >= 4)
                            up.hide();
                    }
                })
            } else if (cid >= 0 && cid < max) {
                LEMON.event.changePic({'key': id, 'index': cid}, function (res) {
                    res = JSON.parse(res);
                    $('#' + res.key).find('img').eq(res.index).attr('src', 'http://image.com/' + (new Date()).getTime() + '/' + res.key + '/' + res.index);
                })
            }
        });
    },
    /**
     * 判断是否上传过  用属性  data('choosed')
     * @param id
     */
    chooseVideo: function (id) {
        var dom = $('#' + id);
        dom.on('tap', function () {
            LEMON.event.chooseVideo({'key': id}, function (res) {
                res = JSON.parse(res);
                dom.find('img').eq(0).attr('src', 'http://video.com/' + (new Date()).getTime() + '/' + res.key);
                dom.data('choosed', 'ok');
            });
        })
    },
    /**
     * 判断是否上传过  用属性  data('choosed')
     * @param id
     */
    chooseAuthVideo: function (id, str) {
        var dom = $('#' + id);
        dom.on('tap', function () {
            LEMON.event.chooseAuthVideo({'key': id, 'str': str}, function (res) {
                res = JSON.parse(res);
                dom.prev().find('img').eq(0).attr('src', 'http://video.com/' + (new Date()).getTime() + '/' + res.key);
                dom.data('choosed', 'ok');
                dom.prev().show();
            });
        })
    },
    /**
     * 获取当前时间
     * @returns {String}
     */
    getFormatTime: function (date, reType) {
        if (!date)
            date = new Date();
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        m = m < 10 ? ('0' + m) : m;
        var d = date.getDate();
        d = d < 10 ? ('0' + d) : d;
        var h = date.getHours();
        var minute = date.getMinutes();
        minute = minute < 10 ? ('0' + minute) : minute;
        var second = date.getSeconds();
        second = second < 10 ? ('0' + second) : second;
        return reType == 'json' ? {y: y, m: m, d: d, h: h, minute: minute, second: second} : y + '-' + m + '-' + d + ' ' + h + ':' + minute + ':' + second;
    },
    /**
     * 获取当前时间
     * @returns {String}
     */
    getImShowTime: function (date) {
        var imTime = $.util.getFormatTime(date, 'json'),
                cTime = $.util.getFormatTime('', 'json'),
                tmp = imTime.d + ':' + imTime.minute;

        return (imTime.m != cTime.m || imTime.d != cTime.d ? imTime.m + '-' + imTime.d + ' ' : '') + tmp;
    },
    /**
     * 处理js错误
     * @param {type} a
     * @param {type} b
     * @param {type} c
     * @param {type} d
     * @param {type} e
     * @returns {undefined|Boolean}
     */
    windowError: function (a, b, c, d, e) {
        function f(a) {
            var b, c;
            if ("object" == typeof a) {
                if (null === a)
                    return "null";
                if (window.JSON && window.JSON.stringify)
                    return JSON.stringify(a);
                c = g(a), b = [];
                for (var d in a)
                    b.push((c ? "" : '"' + d + '":') + f(a[d]));
                return b = b.join(), c ? "[" + b + "]" : "{" + b + "}";
            }
            return "undefined" == typeof a ? "undefined" : "number" == typeof a || "function" == typeof a ? a.toString() : a ? '"' + a + '"' : '""';
        }

        function g(a) {
            return "[object Array]" == Object.prototype.toString.call(a);
        }

        var h, i = window;
        if (d = d || i.event && i.event.errorCharacter || 0, e && e.stack) {
            a = e.stack.toString();
        } else if (arguments.callee) {
            for (var j = [a], k = arguments.callee.caller, l = 3; k && --l > 0 && (j.push(k.toString()), k !== k.caller); )
                k = k.caller;
            j = j.join(","), a = j;
        }
        if (h = f(a) + (b ? ";URL:" + b : "") + (c ? ";Line:" + c : "") + (d ? ";Column:" + d : ""), i._last_err_msg) {
            if (i._last_err_msg.indexOf(a) > -1)
                return;
            i._last_err_msg += "|" + a;
        } else
            i._last_err_msg = a;

        setTimeout(function () {
            console.log("ERROR:" + h);
//            alert("JS ERROR:" + h);
            (new Image).src = '/wx/jslog?content=' + encodeURIComponent(h);
            //var a = encodeURIComponent(h), b = new Image;
            //b.src = "//wq.jd.com/webmonitor/collect/badjs.json?Content=" + a + "&t=" + Math.random();
            //当前用户登录ID、时间、手机号码、上报URL
            //b.src = "/BadJS/index.html?Content=" + a + "&t=" + Math.random();

        }, 500);

        return !1;
    },
    lmlogin: function () {
        if ($.util.isWX) {
            window.location.href = '/user/login';
        } else {
            LEMON.event.login(function (res) {
//                          res = JSON.parse(res);
                $.util.setCookie('token_uin', res.token_uin, 99999999);
                LEMON.db.set('token_uin', res.token_uin);
                //window.location.reload();
            });
        }
    },
    checkShare: function () {
        if ($.util.getParam('ivc')) {
            $.util.setCookie('IVCOD', $.util.getParam('ivc'), 15);
        }
    },
    getShareCode: function () {
        return $.util.getCookie('IVCOD');
    },
    openTalk: function (res) {
        var param = {};
        param['accid'] = res.data.obj.imaccid;
        if(!$.util.isAPP){
            location.href = '/chat/chat-detail/'+ param.accid;
            return;
        }
        param['nick'] = res.data.obj.nick;
        param['avatar'] = res.data.obj.avatar;
        param['id'] = res.data.obj.id;
        LEMON.event.imTalk(param);
    },
    setWH: function (img) {
        img.height < img.width ? $(img).css({'height': '100%'}) : $(img).css({'width': '100%'})
    },
    addZero: function (p, len) {
        var str = '0000000000000000000' + p;
        return str.substr(str.length - len, len);
    },
    /**
     * eg: $.util.dateformat('yyyy/mm/dd hh:ii:ss', new Date())
     */
    dateformat: function (formatStr, date) {
        var arrWeek = ['日', '一', '二', '三', '四', '五', '六'],
                str = formatStr
                .replace(/yyyy|YYYY/, date.getFullYear()).replace(/yy|YY/, $.util.addZero(date.getFullYear() % 100, 2))
                .replace(/mm|MM/, $.util.addZero(date.getMonth() + 1, 2)).replace(/m|M/g, date.getMonth() + 1)
                .replace(/dd|DD/, $.util.addZero(date.getDate(), 2)).replace(/d|D/g, date.getDate())
                .replace(/hh|HH/, $.util.addZero(date.getHours(), 2)).replace(/h|H/g, date.getHours())
                .replace(/ii|II/, $.util.addZero(date.getMinutes(), 2)).replace(/i|I/g, date.getMinutes())
                .replace(/ss|SS/, $.util.addZero(date.getSeconds(), 2)).replace(/s|S/g, date.getSeconds())
                .replace(/w/g, date.getDay()).replace(/W/g, arrWeek[date.getDay()]);
        return str;
    },
    date2ios: function (time) {
        return time.replace(/\-/g, "/");
    }
};

$.util.isWX = navigator.userAgent.toLowerCase().indexOf('micromessenger') != -1;
$.util.isQQ = navigator.userAgent.toLowerCase().indexOf('qq') != -1;
$.util.isAPP = navigator.userAgent.toLowerCase().indexOf('smartlemon') != -1;
$.util.isIOS = !!$.os.ios;
$.util.isAndroid = !!$.os.android;