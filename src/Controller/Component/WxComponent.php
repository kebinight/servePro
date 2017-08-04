<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Wx component  wx组件 
 *  获取access_token,用户openId, jsapi 签名信息
 * @author caowenpeng <caowenpeng1990@126.com>
 * @property \App\Controller\Component\EncryptComponent $Encrypt
 */
class WxComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    public $components = ['Encrypt'];

    const TOKEN_NAME = 'wx.access_token';
    const WXGZ_API_URL = 'https://api.weixin.qq.com/cgi-bin/';   //公众号api
    const JSAPI_TICKET_NAME = 'wx.jsapi_ticket';
    const MASTER_TOKEN_API = '/get-token';

    protected $app_id;
    protected $app_secret;
    protected $wxconfig;
    /**
     * 中控服务器ip
     * @var type 
     */
    protected $master_ip;  
    /**
     * 中空服务器域名
     * @var type 
     */
    protected $master_domain;

    public function initialize(array $config) {
        parent::initialize($config);
        $wxconfig = \Cake\Core\Configure::read('weixin');
        $this->app_id = $wxconfig['appID'];
        $this->app_secret = $wxconfig['appsecret'];
        $this->master_ip = $wxconfig['master_ip'];
        $this->master_domain = $wxconfig['master_domain'];
        $this->wxconfig = $wxconfig;
    }

    /**
     *  验证服务器安全性  微信验证服务器是你的服务器 验证通过输出微信返回的字符串
     * @param type $token  公众号上填写的token值 
     * @return boolean
     */
    public function checkSignature($token) {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            echo $_GET['echostr'];
            exit();
        } else {
            return false;
        }
    }

    /**
     * 
     * 前往微信验证页 前去获取code
     * @param type $base  是否base 静默获取
     * @param string $redirect_url 跳转url
     */
    public function  getUserJump($base=false, $self=false, $redirect = true) {
        if(!$base){
            $redirect_url = 'http://' . $_SERVER['SERVER_NAME'] . '/mobile/wx/getUserCode';
            $scope = 'snsapi_userinfo';
        }else{
            $redirect_url = 'http://' . $_SERVER['SERVER_NAME'] . '/wx/getUserCodeBase';
            $scope = 'snsapi_base';
        }
        if($self) {
            $redirect_url = $this->request->scheme() . '://' . $_SERVER['SERVER_NAME'] . '/' . $this->request->url;
        }
        $wx_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
                . $this->app_id . '&redirect_uri=' . urlencode($redirect_url) . '&response_type=code&scope=' . $scope . '&state=STATE#wechat_redirect';
        if($redirect) {
            $this->_registry->getController()->redirect($wx_code_url);
        } else {
            return $wx_code_url;
        }
    }

    /**
     * 获取用于code的访问链接
     * @param {boolean} $base  是否base 静默获取
     * @param string $redirect_url 跳转url
     */
    public function  getUserCodeUrl($base=false, $redirectUrl='') {
        if(!$base){
            $scope = 'snsapi_userinfo';
        }else{
            $scope = 'snsapi_base';
        }
        if(!$redirectUrl) {
            $redirect_url = $this->request->scheme() . '://' . $_SERVER['SERVER_NAME'] . '/' . $this->request->url;;
        } else {
            $redirect_url = $this->request->scheme() . '://' . $_SERVER['SERVER_NAME'] . $redirectUrl;
        }
        $wx_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='
            . $this->app_id . '&redirect_uri=' . urlencode($redirect_url) . '&response_type=code&scope=' . $scope . '&state=STATE#wechat_redirect';
        return $wx_code_url;
    }

    /**
     * 通过返回的code 获取access_token 再异步获取openId 和 用户信息
     * @return boolean|stdClass 出错则返回false 成功则返回带有openId 的用户信息 json std对象
     */
    public function getUser($code=null, $isApp=false, $userinfoScope = false) {
        $code = !empty($code) ? $code : $this->request->query('code');
        $httpClient = new \Cake\Network\Http\Client(['ssl_verify_peer' => false]);
        $appid = $this->app_id;
        $app_secret = $this->app_secret;
        if($isApp){
            $wxconfig = $this->wxconfig;
            $appid = $wxconfig['AppID'];
            $app_secret = $wxconfig['AppSecret'];
        }
        //通过code获取特殊网页授权access_token和开放平台中app通过code获取access_token是一样的，但是与普通的接口凭证access_token不一样
        //此链接可以用来拉取用户的openid
        $wx_accesstoken_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $app_secret .
                '&code=' . $code . '&grant_type=authorization_code';
        tmpLog('wx_accesstoken_url:' . $wx_accesstoken_url);
        $response = $httpClient->get($wx_accesstoken_url);
        if ($response->isOk()) {
            tmpLog('app获取:' . json_encode($response));
            $open_id = json_decode($response->body())->openid;
            if($isApp){
                $access_token = json_decode($response->body())->access_token;
                $wx_user_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $open_id . '&lang=zh_CN';
            }else{
                //网页授权获取到的access_token如果scope是snsapi_userinfo的话，可以获取到用户信息
                if($userinfoScope) {
                    $access_token = json_decode($response->body())->access_token;
                    $wx_user_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $open_id . '&lang=zh_CN';
                } else {
                    $access_token = $this->getAccessToken();
                    $wx_user_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $open_id . '&lang=zh_CN';
                }
            }
            $res = $httpClient->get($wx_user_url);
            if ($res->isOk()) {
                $union_res = json_decode($res->body());
                if(property_exists($union_res, 'errcode')){
                    //第二步获取失败
                    runLog('获取weixin用户信息时unionId接口返回结果显示有误', json_encode($res->body()), 'open_id:' . $open_id);
                    return json_decode($response->body());
                }
                return json_decode($res->body());
            } else {
                runLog('获取weixin用户信息时unionId接口请求失败', json_decode($response->body()), 'open_id:' . $open_id);
                return json_decode($response->body());
            }
        } else {
            return false;
        }
    }

    /**
     * 此accessToken仅作为公众号的全局唯一票据,与开放平台的accessToken是不一样的
     * 获取普通accessToken
     */
    public function getAccessToken() {
        //\Cake\Log\Log::notice('获取普通accessToken','devlog');
        tmpLog('获取普通accessToken--------------------');
        $httpClient = new \Cake\Network\Http\Client(['ssl_verify_peer' => false]);
        if($this->wxconfig['master_model']){
            if($this->request->env('SERVER_ADDR') != $this->master_ip){
                //非中控服务器请求
                //\Cake\Log\Log::notice('中控请求','devlog');
                return $this->handMasterRequest();
            }
        }
        $access_token = \Cake\Cache\Cache::read(self::TOKEN_NAME);
        $url = self::WXGZ_API_URL . 'token?grant_type=client_credential&appid=' . $this->app_id . '&secret=' . $this->app_secret;
        $isExpires = true;   //过期标志
        if (is_array($access_token)) {
            $isExpires = ($access_token['expires_in'] - time()) < 1200 ? true : false;
        }
        if ($access_token === false || $isExpires) {
            $response = $httpClient->get($url);
            if ($response->isOk()) {
                $body = json_decode($response->body());
                //\Cake\Log\Log::debug($body,'devlog');
                if (!property_exists($body, 'access_token')) {
                    \Cake\Log\Log::error('未获取access_token属性','devlog');
                    \Cake\Log\Log::error($response,'devlog');
                    return false;
                }
                $token = $body->access_token;
                $expires = $body->expires_in;
                $expires = time() + $expires;
                \Cake\Cache\Cache::write(self::TOKEN_NAME, [
                    'access_token' => $token,
                    'expires_in' => $expires,
                    'ctime' => date('Y-m-d H:i:s')
                ]);
                tmpLog('获取accessToken成功:' . $token);
                return $token;
            } else {
                \Cake\Log\Log::error($response);
                return FALSE;
            }
        } else {
            //\Cake\Log\Log::debug($access_token);
            tmpLog('获取本地accessToken成功:' . $access_token['access_token']);
            return $access_token['access_token'];
        }
    } 
    
    /**
     * 中控获取机制
     * 如若开发服a、b和线上服务器c .其中有任意1个在另一个获取access_token后获取了，由于使用的是相同的appid 等信息，
     * 所以前一个获取的服务器的token便会在，5分钟之后失效，由于没有超出7200秒的过期时间又不会重新获取，所以便会出现
     * 经常性的有access_token 失效的情况。
     * 中控服务器用来解决此问题，原理是保证线上和开发服务器使用的access_token是同一份。正式服务器取(或中控服务器)本地的文件缓存，非
     * 正式(中控)服务器便采取接口形式从中控(线上)服务器获取access_token .
     * 其中为了保证access_token安全，接口调用会有token 和时效验证，并且token还会被rsa加密，需用相同salt和key解密。
     * 防止http 抓包盗用。
     * @author caowenpeng <caowenpeng1990@126.com>
     * @return boolean
     */
    protected function handMasterRequest(){
        $httpClient = new \Cake\Network\Http\Client(['ssl_verify_peer' => false]);
        $api_url = 'http://' . $this->master_domain . self::MASTER_TOKEN_API;
        //\Cake\Log\Log::debug($api_url,'devlog');
        $time = time();
        $res = $httpClient->post($api_url,[
            'timestamp'=>$time,
            'access_token'=>strtoupper(md5($time . '64e3f4e947776b2d6a61ffbf8ad05df4'))
        ]);
        //\Cake\Log\Log::error($res,'devlog');
        if(!$res->isOk()){
            return false;
        }else{
            //\Cake\Log\Log::info($res->body(),'devlog');
            return $this->Encrypt->decrypt($res->body());
        }
    }

    /**
     * 获取jsapi_ticket
     */
    public function getJsapiTicket() {
        $jsapi_tickt = \Cake\Cache\Cache::read(self::JSAPI_TICKET_NAME);
        if (is_array($jsapi_tickt)) {
            $isExpires = $jsapi_tickt['expires_in'] <= time() ? true : false;
        }
        if ($jsapi_tickt !== false && !$isExpires) {
            //存在缓存并且没过期
            return $jsapi_tickt['jsapi_ticket'];
        }
        //否则 再次请求获取
        $access_token = $this->getAccessToken();
        if (!$access_token) {
            \Cake\Log\Log::error('获取access_token 出错');
            return false;
        }
        $httpClient = new \Cake\Network\Http\Client(['ssl_verify_peer' => false]);
        $url = self::WXGZ_API_URL . 'ticket/getticket?access_token=' . $access_token . '&type=jsapi';
        $response = $httpClient->get($url);
        if (!$response->isOk()) {
            \Cake\Log\Log::error('请求获取jsapi_ticket出错');
            \Cake\Log\Log::error($response);
            return false;
        }
        $body = json_decode($response->body());
        if ($body->errmsg == 'ok') {
            $expires = $body->expires_in;
            $expires = time() + $expires;
            \Cake\Cache\Cache::write(self::JSAPI_TICKET_NAME, [
                'jsapi_ticket' => $body->ticket,
                'expires_in' => $expires,
                'ctime' => date('Y-m-d H:i:s')
            ]);
            return $body->ticket;
        } else {
            \Cake\Log\Log::error('获取jsapi_ticket返回信息有误');
            \Cake\Log\Log::error($body);
            return false;
        }
    }

    
    /**
     * 用于jsapi 调用的 签名等信息
     * @return {array} type
     */
    public function setJsapiSignature() {
        $ticket = $this->getJsapiTicket();
        $noncestr = createRandomCode(16, 3);
        $timestamp = time();
        $url = $this->request->scheme().'://'.$_SERVER['SERVER_NAME'].$this->request->here(false);
        $param = [
            'noncestr' => $noncestr,
            'jsapi_ticket' => $ticket,
            'timestamp' => $timestamp,
            'url' => $url
        ];
        ksort($param);
        $signature = sha1(urldecode(http_build_query($param))); //不要转义的
        return [
            'signature' => $signature,
            'nonceStr' => $noncestr,
            'timestamp' => $timestamp,
            'appId'=>  $this->app_id,
        ];
    }
    
    /**
     * 微信配置信息
     * @param array $apiList @link http://mp.weixin.qq.com/wiki/11/74ad127cc054f6b80759c40f77ec03db.html 所有api参数名列表
     * @param boolean $debug
     * @return array
     */
    public function wxconfig(array $apiList, $debug=true){
        $wxsign = $this->setJsapiSignature();
        $wxsign['debug'] = $debug;
        $wxsign['jsApiList'] = $apiList;
        return $wxsign;
    }

    
    /**
     *  处理微信上传
     */
    public function wxUpload($id) {
        $dir = $this->request->query('dir');
        $zip = $this->request->query('zip');
        $today = date('Y-m-d');
        $urlpath = '/upload/tmp/' . $today . '/';
        if (!empty($dir)) {
            $urlpath = '/upload/' . $dir . '/' . $today . '/';
        }
        $savePath = ROOT . '/webroot' . $urlpath;
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }
        $uniqid = uniqid();
        $filename = $uniqid.'.jpg';
        $token = $this->getAccessToken();
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $token . '&media_id=' . $id;
        $httpClient = new \Cake\Network\Http\Client();
        $response = $httpClient->get($url);
        if($response->isOk()){
            $res = $response->body();
            \Cake\Log\Log::debug($res,'devlog');
        }
       $image = \Intervention\Image\ImageManagerStatic::make($res);
        if($zip){
            $image->resize(60,60);
            $filename = 'thumb_'.$uniqid.'.jpg';
        }
        $image->save($savePath.$filename);
        return $urlpath.$filename;
    }

}
