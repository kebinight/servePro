//可用于（img、div、其他dom）元素的滑动、轮播，还有tab的点击切换
//支持动态插入一个子轮播元素  插在第二个


var scroll = function(o) {
    //可配置的项,moveDom  moveChild必选
    this.opt = {
        tp : 'text', //图片img或是文字text  默认text
        moveDom : null, //必选  待移动父元素zepto查询对象
        moveChild : [], //必选  zepto查询对象列表
        tab : [], //zepto查询对象列表  导航tab元素  没有tab可以不要
        viewDom : null, //在那个容器里滑动，算宽度用，默认window  如果你的默认位置不对  那就要检查下这个
        touchDom2:[], //滑动事件的第二控制器   第一控制器是moveDom，   （dom原生对象数组，不建议搞太多）
        min : 0, //响应滑动的最小移动距离
        minp : 0, //翻页的最小移动距离
        step : 0, //移动的步长 一般是每个元素的长度
        index : 1, //当前位移的元素
        loadImg:false,
        touchEvent : true, //是否要禁用touch事件  禁用touch后,仍然可以设置autoTime,自动轮播
        loopScroll : false, //是否要循环滚动
        lockScrY : false, //是否让竖向滚动
        smartTouch: false, //是否要智能判断上下滑,还是左右滑,判断上下滑的时候,会屏蔽左右行为
        autoTime : 0,    //自动轮播， 默认不自动， 需要的话就传毫秒值 如5000
        tabClass : 'cur',
        transition : 0.5,
        imgInitLazy:4000, //第一次预加载图片延时
        enableTransX : false,//使用translateX(-n*100%)方式
        fun : function() {
        }
    };
    //下面的属性是过程中的变量  不需要配置
    this.privateOpt = {
        sp : null, //当前触发点的position
        len : 1, //总元素
        offset:0,
        image:[],
        smartTouchHoldX: false, //配合smartTouch蔽左右行为
        moveCount: 0, //一个周期内手指触发移动的次数
        stopOnce : false, //hold时停一次
        holdAuto : false,    //自动轮播锁定  当滑出轮播区域后  或是手指在滑动的时候 可以屏蔽自动轮播
        imgInit:true, //第一次加载图片
        hasTab : true
    };
    $.extend(this, this.opt, this.privateOpt, o);
    this.len = this.moveChild.length;
    this.hasTab = this.tab.length > 0;
    this.min = this.min || {'text':100, 'img':1}[this.tp]; //min30是  andiord手Q划不动
    this.minp = this.minp || Math.max(this.min,30); //最少30像素翻页  注意一定要继承min值  很多地方没有给minp赋值
    if(!this.viewDom) this.viewDom = $(window);
    if(this.len > 1) this.startEvent();  //只有一个的时候  不轮播  设置第一个的位置居中
    if(this.loadImg) this.image = this.moveDom.find('img');
    this.resize(this.step || this.moveChild.eq(0).width());
    if(this.len > 1 && this.autoTime){
        var obj = this;
        setInterval(function(){
            if(!obj.holdAuto){
                if(!obj.stopOnce) obj.stepMove(obj.index+1);
                obj.stopOnce = false;
            }
        }, this.autoTime);
    }
};
$.extend(scroll.prototype, {
    resize:function(step){
        this.step = step || this.step;
        var harf = (this.viewDom.width() - this.step)/2;
        this.offset = this.loopScroll ? this.step - harf : harf;
        if(this.len == 1) this.offset=-harf;
        this.stepMove(this.index, true);
    },
    addChild:function(dom, tabDom){
        if (!this.loopScroll) return;
        this.moveChild.eq(0).after(dom);
        this.len += 1;
        this.hasTab && this.tab.eq(this.len-2).after(tabDom);
        if(this.hasTab) this.tab = this.tab.parent().children();

        if(this.len == 2){
            this.moveChild = this.moveDom.children();
            this.startEvent();
        }
        else{
            this.stepMove(2);
        }
    },
    startEvent : function() {
        var obj = this, mid = this.moveDom.get(0), ael=function(dom){
          if(!obj.touchEvent) return;
            dom.addEventListener("touchstart", obj, false);
            dom.addEventListener("touchmove", obj, false);
            dom.addEventListener("touchend", obj, false);
            dom.addEventListener("touchcancel", obj, false);
            dom.addEventListener("webkitTransitionEnd", obj, false);
        };
        ael(mid);

        this.hasTab && this.tab.each(function(i, em) {
            $(em).attr('no', i + 1);
            $(em).click(function() {
                obj.stepMove($(this).attr('no'));
            });
        });

        if (this.loopScroll) {
            this.moveDom.append(this.moveChild.eq(0).clone());
            var last = this.moveChild.eq(this.len - 1).clone();
            this.moveDom.prepend(last);
        }
        for (var i=0; i < this.touchDom2.length; i++) {
            ael(this.touchDom2[i]);
        };
    },
    // 默认事件处理函数，事件分发用
    handleEvent : function(e) {
        switch(e.type) {
            case "touchstart":
                this.sp = this.getPosition(e);
                this.holdAuto = true;
                this.stopOnce = true;
                this.smartTouchHoldX = false;
                this.moveCount = 0;
                break;
            case "touchmove":
                this.touchmove(e);
                break;
            case "touchend":
            case "touchcancel":
                if(this.smartTouchHoldX) break;
                this.move(e);
                this.holdAuto = false;
                break;
            case "webkitTransitionEnd":
                e.preventDefault();
                break;
        }
    },
    getPosition : function(e) {
        var touch = e.changedTouches ? e.changedTouches[0] : e;
        return {
            x : touch.pageX,
            y : touch.pageY
        };
    },
    touchmove : function(e) {
        var mp = this.getPosition(e), x = mp.x - this.sp.x, y = mp.y - this.sp.y;
        if(this.moveCount === 0 && this.smartTouch) {
            this.smartTouchHoldX = Math.abs(y) > Math.abs(x);
        }
        this.moveCount++;
        if(this.smartTouchHoldX) return;

        if (Math.abs(x) - Math.abs(y) > this.min) {
        //if (Math.abs(x) > Math.abs(y)) {
            e.preventDefault();
            var offset = x - this.step * (this.index - 1) - this.offset;
            //console.log(x)
            this.moveDom.css({
                "-webkit-backface-visibility": "hidden",
                "-webkit-transform" : this.enableTransX ? "translateX(" + (this.loopScroll?this.index:this.index-1)*(-100) + "%)" :
                                        "translate3D(" + offset + "px,0,0)",
                "-webkit-transition" : "0"
            });
        } else {
            if (!this.lockScrY) e.preventDefault();
        }
    },
    move : function(e) {
        var mp = this.getPosition(e), x = mp.x - this.sp.x, y = mp.y - this.sp.y;
        if (Math.abs(x) < Math.abs(y) || Math.abs(x) < this.minp) {
            this.stepMove(this.index); //状态还原
            return;
        }
        if (x > 0) {
            e.preventDefault();
            this.stepMove(this.index - 1);
        } else {
            e.preventDefault();
            this.stepMove(this.index + 1);
        }
    },
    loadImage: function(no){
        var img = this.image;
        var setImg = function(i){
            if(img[i] && $(img[i]).attr('back_src')){
                img[i].src = $(img[i]).attr('back_src');
                $(img[i]).removeAttr('back_src');
            }
        };
        setImg(no);
        (function(n, flag, t){
            setTimeout(function(){setImg(n-1); setImg(n+1);}, flag ? t : 0);
        })(no, this.imgInit, this.imgInitLazy);  //用闭包是为了防止n被更改，导致某个图不会被加载
        this.imgInit = false;
    },
    stepMove : function(no, isSetOffsetIndex) {
        this.index = no > this.len ? this.len : no < 1 ? 1 : no;
        this.hasTab && this.tab.removeClass(this.tabClass);
        this.hasTab && this.tab.eq(this.index - 1).addClass(this.tabClass);
        var tran = - this.step * ((this.loopScroll?no:this.index) - 1) - this.offset;
        this.moveDom.css({
            "-webkit-transform" : this.enableTransX ?
                "translateX(" + (this.loopScroll?no:this.index-1)*(-100) + "%)" :"translate3D(" + tran + "px,0,0)" ,
            "-webkit-transition" : isSetOffsetIndex ? "0ms" : "all "+this.transition+"s ease"
        });
        if(this.loadImg) this.loadImage(this.index);
        this.fun(this.index);  //还原位置的时候  也调用了这个  要小心
        if(this.loopScroll && !isSetOffsetIndex){  //循环到头的时候 从复制的位置切换到实际的位置
            var obj = this, cindex=no;
            if(no <= 0) cindex = this.len;
            if(no > this.len) cindex = 1;
            if(cindex != no)
            setTimeout(function(){
                obj.stepMove(cindex, true);
            }, this.transition*1000);
        }
    }
});

// 入口函数  new scroll(opt);

//var aa = new simpleScroll({moveDom:$('#outerUL'), viewDom:$('#outer')});

var simpleScroll = function(o) {
    this.opt = {
        moveDom : null, //必选  待移动元素zepto查询对象
        viewDom : null, //在那个容器里滑动，算宽度用，默认window  如果你的默认位置不对  那就要检查下这个
        left : $(new Image()), //左按钮  zepto查询对象列表
        right : $(new Image()), //右按钮  zepto查询对象列表
        fix:0,  //有时候右边被盖住了  显示不全  就加以修正
        viewWidth:0, //可视区宽度
        width : 0, //总宽度
        sp : null, //当前触发点的position

        offset:0,  //当前偏移量
        tabClass : 'cur',
        transition : 0.5,
        fun : function() {
        }
    };
    $.extend(this, this.opt, o);
    var obj = this;
    if(!this.viewDom) this.viewDom = $(window);
    this.viewWidth = this.viewDom.width();
    //this.width = this.moveDom.width();
    this.moveDom.children().each(function(){obj.width += $(this).width()});
    //this.width -= this.viewWidth;
    if(this.viewWidth >= this.width) {  //只有一个的时候  不滚动
        this.left.addClass(this.tabClass);
        this.right.addClass(this.tabClass);
        return;
    }

    this.startEvent();

};
$.extend(simpleScroll.prototype, {
    startEvent : function() {
        var obj = this, dom = this.moveDom.get(0);
        dom.addEventListener("touchstart", obj, false);
        dom.addEventListener("touchmove", obj, false);
        dom.addEventListener("touchend", obj, false);
        dom.addEventListener("touchcancel", obj, false);
        dom.addEventListener("webkitTransitionEnd", obj, false);
        this.left && this.left.tap(function(){obj.move('left')});
        this.right && this.right.tap(function(){obj.move('right')});
    },
    // 默认事件处理函数，事件分发用
    handleEvent : function(e) {
        switch(e.type) {
            case "touchstart":
                this.sp = this.getPosition(e);
                break;
            case "touchmove":
                this.touchmove(e);
                break;
            case "touchend":
            case "touchcancel":
                this.move();
                break;
            case "webkitTransitionEnd":
                e.preventDefault();
                break;
        }
    },
    getPosition : function(e) {
        var touch = e.changedTouches ? e.changedTouches[0] : e;
        return {
            x : touch.pageX,
            y : touch.pageY
        };
    },
    touchmove : function(e) {
        var mp = this.getPosition(e), x = mp.x - this.sp.x, y = mp.y - this.sp.y;
        this.sp = mp;
        if (Math.abs(x) > Math.abs(y)) {
            e.preventDefault();
            this.offset += x;
            this.moveDom.css({
                //"-webkit-backface-visibility": "hidden",
                "-webkit-transform" : "translate3D(" + this.offset + "px,0,0)",
                "-webkit-transition" : "0"
            });
        }
    },
    move : function(tp) {
        var offset=this.offset, maxWidth = this.width - this.viewWidth + this.fix;
        if(tp){
            var step = this.viewWidth;
            if(tp == 'right') step = -step;
            this.offset += step;
        }

        if(this.offset > 0) this.offset = 0
        if(-this.offset > maxWidth) this.offset = -maxWidth;

        if(this.offset == offset) return;

        this.moveDom.css({
            //"-webkit-backface-visibility": "hidden",
            "-webkit-transform" : "translate3D(" + this.offset + "px,0,0)",
            "-webkit-transition" : "all "+this.transition+"s ease"
        });

        this.showTab();
    },
    showTab:function(){
        if(this.offset == 0){
            this.left.addClass(this.tabClass);
            this.right.removeClass(this.tabClass);
        }
        if(this.offset == -this.width){
            this.right.addClass(this.tabClass);
            this.left.removeClass(this.tabClass);
        }
    },
    showDef:function(dom) {
        this.offset = -dom.offset().left;
        this.moveDom.css({
            //"-webkit-backface-visibility": "hidden",
            "-webkit-transform" : "translate3D(" + this.offset + "px,0,0)",
            "-webkit-transition" : "0"
        });
    }
});

// 入口函数  new simpleScroll(opt);



var ranger = function(o) {
    this.opt = {
        dom : null, //父容器
        range:[], //数组  开始和末尾
        reRange:[],//滑动的结果
        highlightBar : null, //高亮的容器
        moverleft : null, //滑块按钮
        moverright : null, //滑块按钮
        mover:null, //当前滑动的元素
        left:0,  // highlightBar  css left
        right:0, // highlightBar  css right
        width : 0, //总宽度
        movex :0,  //滑动时  移动的x距离
        fun : function() {
        }
    };
    $.extend(this, this.opt, o);
    var obj = this, divs=this.dom.find('div');
    this.reRange = this.range;
    this.width = this.dom.width();
    this.highlightBar = divs.eq(0);
    this.moverleft = divs.eq(1);
    this.moverright = divs.eq(2);
    this.startEvent();

};
$.extend(ranger.prototype, {
    startEvent : function() {
        var obj = this, dom = this.dom.get(0);
        dom.addEventListener("touchstart", obj, false);
        dom.addEventListener("touchmove", obj, false);
        dom.addEventListener("webkitTransitionEnd", obj, false);
    },
    // 默认事件处理函数，事件分发用
    handleEvent : function(e) {
        var em = e.srcElement || e.target, bar=$(em).data('bar');
        this.mover = $(em).data('bar');
        if(!this.mover) return;
        switch(e.type) {
            case "touchstart":
                this.movex = this.getPosition(e);
                break;
            case "touchmove":
                this.touchmove(e);
                break;
            case "webkitTransitionEnd":
                e.preventDefault();
                break;
        }
    },
    getPosition : function(e) {
        var touch = e.changedTouches ? e.changedTouches[0] : e;
        return touch.pageX;
    },
    touchmove : function(e) {
        var mp = this.getPosition(e), x = mp - this.movex, m=this['mover'+this.mover], r=this.range[1] - this.range[0];
        this.movex = mp;
        this.mover == 'left' ? this.left += x : this.right -= x;
        this.left = Math.min(this.left, this.width);
        this.right = Math.min(this.right, this.width);
        this.left = Math.max(this.left, 0);
        this.right = Math.max(this.right, 0);
        this.mover == 'left' ? m.css({left : this[this.mover]+'px'}) : m.css({right : this[this.mover]+'px'});
        this.left+this.right>this.width ?
            this.highlightBar.css({left: (this.width-this.right)+'px', right: (this.width-this.left)+'px'}):
            this.highlightBar.css({left: this.left+'px', right: this.right+'px'});

        var l = this.left, r = this.width-this.right, rg=this.range[1]-this.range[0];
        this.reRange = [this.range[0] + rg*(Math.min(l,r)/this.width), this.range[0]+ rg*(Math.max(l,r)/this.width)];
        console.log(this.reRange);
    }
});


// 入口函数  new ranger({dom:$('#age'), range:[18, 40]});