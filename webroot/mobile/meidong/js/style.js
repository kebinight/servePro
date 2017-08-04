(function(){
	var oDiv = $('#imgbox');
	var liList =oDiv.find('ul li');
	var ydDiv = $('#yd');
	var imgTab = ydDiv.find('span');
	var iNow = 0;
	var timer=null;
	fnFade();
	autoPlay();
	oDiv.hover(function (){ clearInterval(timer); }, autoPlay);
	imgTab.click(function (){
			iNow = $(this).index();
			fnFade();
		});
	function autoPlay(){
		timer = setInterval(function(){
			iNow++;
			iNow%=liList.length;
			fnFade();
		},3000)
	}
	function fnFade(){
		liList.each(function(i){
			if( i != iNow){
				liList.eq(i).fadeOut().css('zIndex', 1);
				imgTab.eq(i).removeClass('active');
			}else{
				liList.eq(i).fadeIn().css('zIndex', 2);
				imgTab.eq(i).addClass('active');
			}
		})
	}
})();
