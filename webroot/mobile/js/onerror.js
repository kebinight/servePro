window.onerror = function (a,b,c,d,e) {
	function f(a) {
		var b, c;
		if ("object" == typeof a) {
			if (null === a)
				return "null";
			if (window.JSON && window.JSON.stringify)
				return JSON.stringify(a);
			c = g(a), b = [];
			for (var d in a)
			b.push(( c ? "" : '"' + d + '":') + f(a[d]));
			return b = b.join(), c ? "[" + b + "]" : "{" + b + "}";
		}
		return "undefined" == typeof a ? "undefined" : "number" == typeof a || "function" == typeof a ? a.toString() : a ? '"' + a + '"' : '""';
	}

	function g(a) {
		return "[object Array]" == Object.prototype.toString.call(a);
	}

	var h, i = window;
	if ( d = d || i.event && i.event.errorCharacter || 0, e && e.stack){
		a = e.stack.toString();
	}
	else if (arguments.callee) {
		for (var j = [a], k = arguments.callee.caller, l = 3; k && --l > 0 && (j.push(k.toString()), k !== k.caller); )
			k = k.caller;
		j = j.join(","), a = j;
	}
	if ( h = f(a) + ( b ? ";URL:" + b : "") + ( c ? ";Line:" + c : "") + ( d ? ";Column:" + d : ""), i._last_err_msg) {
		if (i._last_err_msg.indexOf(a) > -1)
			return;
		i._last_err_msg += "|" + a;
	} else
		i._last_err_msg = a;
	
	setTimeout(function() {
			console.log("ERROR:" + h);
			var a = encodeURIComponent(h), b = new Image;
			//b.src = "//wq.jd.com/webmonitor/collect/badjs.json?Content=" + a + "&t=" + Math.random();
			//当前用户登录ID、时间、手机号码、上报URL
		    b.src = "/BadJS/index.html?Content=" + a + "&t=" + Math.random();

		}, 500);
		
	return !1
};