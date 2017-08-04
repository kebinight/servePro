(function (win) {
    var h;
    var docEl = document.documentElement;
    function setUnitA() {
        var clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
       
        if(clientWidth >750){
        	docEl.style.fontSize =100 +'px';
        }else{
        	 docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
        }
    };
    win.addEventListener('resize', function () {
        clearTimeout(h);
        h = setTimeout(setUnitA, 300);
    }, false);
    win.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            clearTimeout(h);
            h = setTimeout(setUnitA, 300);
        }
    }, false);

    setUnitA();
})(window);
