<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Bdmap component 百度地图web服务 组件
 */
class BdmapComponent extends Component {
// 详情页    http://map.baidu.com/mobile/webapp/search/search/qt=inf&uid=08fc93dfb0b4ef889b68b441/?third_party=uri_api

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    protected $ak;

    /**
     * 请求参数中坐标的类型，1（wgs84ll即GPS经纬度），2（gcj02ll即国测局经纬度坐标），3（bd09ll即百度经纬度坐标），
     * 4（bd09mc即百度米制坐标）
     * @var type 
     */
    protected $coord_type = 3;
    protected $page_num = 0;
    protected $page_size = 10;
    protected $scope = 2;

    const BAIDU_MAP_API_URL = 'http://api.map.baidu.com';

    public function initialize(array $config) {
        parent::initialize($config);
        $conf = \Cake\Core\Configure::read('baidu');
        $this->ak = $conf['mapkey'];
    }

    //http://api.map.baidu.com/place/v2/search?ak=tWrZdNVPFi5kZDxTnErP9VWq&filter=&location=23.120589%2C113.331059&mcode=com.juhui.yiqi&output=json&page_num=0&page_size=5&query=%E7%BD%91%E5%90%A7&radius=200000000&region=%E5%B9%BF%E5%B7%9E&scope=2&tag=%E6%B1%9F
    //http://lbsyun.baidu.com/index.php?title=webapi/guide/webservice-placeapi
    //http://api.map.baidu.com/place/v2/search?query=银行&location=39.915,116.404&radius=2000&output=xml&ak={您的密钥}
    public function placeSearchNearBy($query, $location, $page_num = null, $tag = null, $scope = null, $radius = 10000) {
        $api_url = '/place/v2/search?';
        $location = $this->formatCoord($location);
        $params = [
            'ak' => $this->ak,
            'location' => $location,
            'coord_type' => $this->coord_type,
            'output' => 'json',
            'radius' => $radius,
            'page_num' => $this->page_num,
            'page_size' => $this->page_size,
            'query' => $query,
            'scope' => $this->scope
        ];
        if ($tag) {
            $params['tag'] = $tag;
        }
        if ($page_num) {
            $params['page_num'] = $page_num-1;
        }
        if ($scope) {
            $params['scope'] = $scope;
        }
        $query_str = http_build_query($params);
        $url = self::BAIDU_MAP_API_URL . $api_url . $query_str;
        $httpClient = new \Cake\Http\Client();
        $res = $httpClient->get($url);
        if ($res->isOk()) {
            if ($data = json_decode($res->body())) {
                if ($data->status === 0) {
                    return $data->results;
                } else {
                    \Cake\Log\Log::error($data->message);
                    return false;
                }
            }
        } else {
            \Cake\Log\Log::error('请求百度api失败', 'devlog');
            return false;
        }
    }

    public function buildLinkString($params) {
        $string = '';
        foreach ($params as $key => $value) {
            $string.= $key . '=' . $value . '&';
        }
        //去掉最后一个&字符
        $string = substr($string, 0, count($string) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $string = stripslashes($string);
        }
        return $string;
    }
    
    
    /**
     * 坐标格式化成纬度在前 经度在后
     * @param type $coord
     * @return type
     * @throws Exception
     */
    public function formatCoord($coord){
        $coord_arr = explode(',', $coord);
        if(!$coord_arr){
            throw new Exception('坐标参数不正确');
        }
        return $coord_arr[1].','.$coord_arr[0];
    }

}
