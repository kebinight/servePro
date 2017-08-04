$.bmap = {
    ak: '474572ab0a64485f5b02d3e8accaf09c',
    initScript: function loadJScript() {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "http://api.map.baidu.com/api?v=2.0&ak=" + this.ak + '&callback=initBmap';
        document.body.appendChild(script);
    },
    getPos: function (handlePos, posMsg) {
        var self = this;
        if (window.navigator.geolocation) {
            if (typeof posMsg == 'function') {
                posMsg();
            }
            var options = {
                enableHighAccuracy: true,
            };
            window.navigator.geolocation.getCurrentPosition(handleSuccess, handleError, options);
        } else {
            alert("浏览器不支持html5来获取地理位置信息");
        }
        function handleSuccess(position) {
            //设置一个提示
            // 获取到当前位置经纬度  本例中是chrome浏览器取到的是google地图中的经纬度
            var lng = position.coords.longitude;
            var lat = position.coords.latitude;
            var gpsPoint = new BMap.Point(lng, lat);
            // 将gps坐标转化为百度地图的经纬度
            var convertor = new BMap.Convertor();
            var pointArr = [];
            pointArr.push(gpsPoint);
            convertor.translate(pointArr, 1, 5, function (point) {
                if (point.status === 0) {
                    lng = point.points[0].lng;
                    lat = point.points[0].lat;
                    $.getJSON('http://api.map.baidu.com/geocoder/v2/?ak=' + self.ak + '&location='
                            + lat + ',' + lng + '&output=json&pois=1&callback=?', handlePos);
                } else {
                    //坐标转换失败
                }
            });
        }
        function handleError(error) {
            //获取坐标失败
            alert(error.message);
        }
    },
    /**
     * 转化成百度坐标
     * @param {type} lng
     * @param {type} lat
     * @param {type} cb 转换成功后的回调
     * @returns {undefined}
     */
    convertor: function (lng, lat, cb) {
        // 将gps坐标转化为百度地图的经纬度
        var gpsPoint = new BMap.Point(lng, lat);
        var convertor = new BMap.Convertor();
        var pointArr = [];
        pointArr.push(gpsPoint);
        convertor.translate(pointArr, 1, 5, function (point) {
            if (point.status === 0) {
                lng = point.points[0].lng;
                lat = point.points[0].lat;
                cb(lng, lat);
            } else {
                //坐标转换失败
                alert('坐标转换失败');
            }
        });
    },
    /**
     * 
     * @param {type} lng 百度经度
     * @param {type} lat 百度纬度
     * @param {function} resCb 结果回调函数
     * @returns {GeocoderResult} 
     * point	Point	坐标点。(自 1.1 新增)
     * address	String	地址描述。(自 1.1 新增)
     * addressComponents AddressComponent	结构化的地址描述。(自 1.1 新增)
     * surroundingPois	Array<LocalResultPoi>	附近的POI点。(自 1.1 新增)
     * business	String	商圈字段，代表此点所属的商圈
     */
    geocoder: function (lng, lat, resCb) {
        var point = new BMap.Point(lng, lat);
        var geoc = new BMap.Geocoder();
        geoc.getLocation(point, resCb);
    },
    test: function (test) {
        console.log(this);
    }
};
//初始化脚本
$.bmap.initScript();

/**
 * 
 * @param {type} point
 * @param {type} obj
 * @returns {avatarOverlay}
 */
