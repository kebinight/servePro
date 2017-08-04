var imsession = function(o) {
    var opt = {
        target : null,
        backUnread : {},
        sessionsList : null,
        infoProvider : null,
        nim : null,
        onConnect : null,
        onWillReconnect : null,
        onDisconnect : null,
        onSessions : null,
        onUpdateSession : null,
        getUsersDone : null,
        getNewUsersDone : null,
        onUsers : null,
        onUpdateUser : null,
        updateSessionsUI : null,
        onError : null
    };
    $.extend(this, this.opt, o);
};

$.extend(imsession.prototype, {
    init : function(appkey, account, token, el) {
        var obj = this;
        this.target = el;
        this.infoProvider = function(data, type){
            var info = {};
            switch(type){
                case "session":
                    var msg = data.lastMsg;
                    info.scene = msg.scene;
                    info.target = msg.scene+"-"+msg.target;
                    info.account = msg.target;
                    info.time =  obj.formatTime(msg.time);
                    info.unread = data.unread>99?"99+":data.unread;
                    if(info.scene==="p2p"){
                        //点对点
                        var userInfo =data.target;
                        info.nick = msg.fromNick;
                        info.avatar = 'http://b12026.nos.netease.com/MTAxMTAxMA==/bmltYV8xMTkwNTlfMTQ0NzMxNDU5NjgyNV9lOTc5OTE1NC02MjU4LTQzYTUtOWYzOS04ZTVhODAxMmFmMjA=?imageView&amp;thumbnail=80x80&amp;quality=85';
                        info.text = msg.text;
                    }
                    break;
            }
            return info;
        };

        this.onConnect = function onConnect() {
            console.log('onConnect-----------------------');
        };

        this.onWillReconnect = function onWillReconnect(obj) {
            // 此时说明 SDK 已经断开连接, 请开发者在界面上提示用户连接已断开, 而且正在重新建立连接
            console.log('onWillReconnect-------------------------');
            console.log('即将重连');
            console.log(obj.retryCount);
            console.log(obj.duration);
        };

        this.onDisconnect = function onDisconnect(error) {
            console.log('onDisconnect--------------------------');
            // 此时说明 SDK 处于断开状态, 开发者此时应该根据错误码提示相应的错误信息, 并且跳转到登录页面
            console.log('丢失连接');
            console.log(error);
            if (error) {
                switch (error.code) {
                    // 账号或者密码错误, 请跳转到登录页面并提示错误
                    case 302:
                        break;
                    // 重复登录, 已经在其它端登录了, 请跳转到登录页面并提示错误
                    case 417:
                        break;
                    // 被踢, 请提示错误后跳转到登录页面
                    case 'kicked':
                        break;
                    default:
                        break;
                }
            }
        };

        //收到会话列表
        this.onSessions = function onSessions(sessionsData) {
            console.log('onSessions--------------------------');
            console.log('收到会话列表', sessionsData);
            obj.show(sessionsData);
            obj.getUserInfo(obj.getAccounts(sessionsData));
        };

        //会话更新了
        this.onUpdateSession = function onUpdateSession(session) {
            console.log('onUpdateSession----------------------------');
            data.sessions = nim.mergeSessions(data.sessions, session);
        };


        this.getUsersDone = function getUsersDone(error, users) {
            console.log('getUsersDone----------------------------------');
            console.log(error);
            console.log(users);
            var res = {users:users};
            console.log('获取用户名片数组' + (!error ? '成功' : '失败'));
        };

        this.getNewUsersDone = function getNewUsersDone(error, users) {
            console.log('getNewUsersDone----------------------------------');
            //获取新会话
            console.log(error);
            console.log(users);
            console.log(sessList);
            var res = {users:users};
            render = getRender(sessList, res);
            $('#chat-list').prepend(render);
            console.log('获取用户名片数组' + (!error ? '成功' : '失败'));
            if (!error) {
                onUsers(users);
            }
        };

        this.onUsers = function onUsers(users) {
            console.log('onUsers---------------------------------------');
            console.log('收到用户名片列表', users);
            //data.users = nim.mergeUsers(data.users, users);
        }

        function updateSessionsUI() {
            // 刷新界面
        }

        this.onError = function onError(error) {
            console.log('onError-------------------------------------------');
            console.log(error);
        }

        this.nim = NIM.getInstance({
            debug: true,
            appKey: appkey,
            account: account,
            token: token,
            onconnect: obj.onConnect,
            onwillreconnect: obj.onWillReconnect,
            ondisconnect: obj.onDisconnect,
            onerror: obj.onError,
            onsessions: obj.onSessions,
            onupdatesession: obj.onUpdateSession,
            onusers : obj.onUsers
        });
    },
    formatTime : function(timestamp) {
        var msgtime = new Date(timestamp);
        var curtime = new Date();
        var formatstr = '';
        if(msgtime.getFullYear() != curtime.getFullYear()) {
            formatstr += msgtime.getFullYear() + '年';
        }
        if(msgtime.getMonth() != curtime.getMonth()) {
            formatstr += msgtime.getMonth() + '月';
        }
        if(msgtime.getDate() != curtime.getDate()) {
            formatstr += msgtime.getDate() + '日';
        }
        formatstr += ' ' + msgtime.getHours() + ':' + msgtime.getSeconds();
        return formatstr;
    },
    genSesDatas : function(sessionsList) {
        var obj = this;
        return {
            //必填，显示UI所需要的数据，具体内容见下文|
            data:{
                sessions : sessionsList
            },
            //必填，返回一个对象，由上层来控制显示规则，呈现数据
            infoprovider : obj.infoProvider,
            //
            onclickavatar : function() {},
            //可选，用来自定义点击列表项回调函数
            onclickitem : function() {},
            //可选，组件样式名，组件元素样式都基于该命名空间，默认样式为m-pannel
            clazz : null,
            //可选，定义组件插入的节点，也可在实例化后 调用inject方法
            parent : null
        };
    },
    getAccounts : function(sessionsData) {
        var accounts = [];
        $.each(sessionsData, function(index, value, array) {
            accounts.push(value.lastMsg.from);
        });
        return accounts;
    },
    getUserInfo : function($accounts) {
        console.log('getUserInfo：=--------------------------------------');
        console.log($accounts);
        var obj = this;
        this.nim.getUsers({
            accounts: $accounts,
            done: obj.getUsersDone
        });
    },
    show : function(sessionsData) {
        var sessionsObject = new NIMUIKit.SessionList(this.genSesDatas(sessionsData));
        sessionsObject.inject(this.target, sessionsData);
    }
});
