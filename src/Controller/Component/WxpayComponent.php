<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use GlobalCode;

/**
 * Wxpay component  用于微信支付
 * @property \App\Controller\Component\BusinessComponent $Business
 * @property \App\Controller\Component\UtilComponent $Util
 */
class WxpayComponent extends Component {

    const WEIXIN_PAY_API_URL = 'https://api.mch.weixin.qq.com';

    protected $app_id;
    protected $app_secret;

    /**
     * 商户id
     * @var type 
     */
    protected $mchid;
    protected $key;
    protected $sslcert_path = '../cert/apiclient_cert.pem';
    protected $sslkey_path = '../cert/apiclient_key.pem';
    protected $notify_url;
    protected $App_id;
    protected $App_secret;
    protected $App_mchid;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    public $components = ['Business', 'Util'];

    public function initialize(array $config) {
        parent::initialize($config);
        $wxconfig = \Cake\Core\Configure::read('weixin');
        $this->app_id = $wxconfig['appID'];
        $this->app_secret = $wxconfig['appsecret'];
        $this->App_id = $wxconfig['AppID'];
        $this->AppSecret = $wxconfig['AppSecret'];
        $this->mchid = $wxconfig['mchid'];
        $this->App_mchid = $wxconfig['App_mchid'];
        $this->sslcert_path = $wxconfig['sslcert_path'];
        $this->sslkey_path = $wxconfig['sslkey_path'];
        $this->key = $wxconfig['key'];
        $this->notify_url = $this->request->scheme() . '://' . $_SERVER['SERVER_NAME'] . $wxconfig['notify_url'];
    }

    /**
     * 
     * @param type $body   商品信息
     * @param type $openid  发起支付的用户openid
     * @param type $out_trade_no  商户上的订单号
     * @param type $fee   支付金额
     * @param type $notify_url  异步回调地址
     * @param boolean $isApp  异步回调地址
     * @return type
     */
    public function unifiedorder($body, $openid, $out_trade_no, $fee, $notify_url = null, $isApp = false) {
        $apiurl = self::WEIXIN_PAY_API_URL . '/pay/unifiedorder';
        if ($isApp) {
            $xmlText = '<xml>
                    <appid>%s</appid>
                    <body>%s</body>
                    <mch_id>%s</mch_id>
                    <nonce_str>%s</nonce_str>
                    <notify_url>%s</notify_url>
                    <out_trade_no>%s</out_trade_no>
                    <spbill_create_ip>%s</spbill_create_ip>
                    <total_fee>%d</total_fee>
                    <trade_type>%s</trade_type>
                    <sign>%s</sign>
                    </xml>';
        } else {
            $xmlText = '<xml>
                    <appid>%s</appid>
                    <body>%s</body>
                    <mch_id>%s</mch_id>
                    <nonce_str>%s</nonce_str>
                    <notify_url>%s</notify_url>
                    <openid>%s</openid>
                    <out_trade_no>%s</out_trade_no>
                    <spbill_create_ip>%s</spbill_create_ip>
                    <total_fee>%d</total_fee>
                    <trade_type>%s</trade_type>
                    <sign>%s</sign>
                    </xml>';
        }


        $ip = $this->request->clientIp();
        $nonce_str = createRandomCode(16);
        $notify_url = empty($notify_url) ? $this->notify_url : $notify_url;
        $trade_type = $isApp ? 'APP' : 'JSAPI';
        $appid = $isApp ? $this->App_id : $this->app_id;
        $mchid = $isApp ? $this->App_mchid : $this->mchid;
        if($isApp){
            $params = [
                'appid' => $appid,
                'body' => $body,
                'mch_id' => $mchid,
                'nonce_str' => $nonce_str,
                'notify_url' => $notify_url,
                'out_trade_no' => $out_trade_no,
                'spbill_create_ip' => $ip,
                'total_fee' => $fee,
                'trade_type' => $trade_type
            ];
            $sign = $this->setSign($params);
            $xmlString = sprintf($xmlText, $appid, $body, $mchid, $nonce_str, $notify_url,
                    $out_trade_no, $ip, $fee, $trade_type, $sign);
        }else{
            $params = [
                'appid' => $appid,
                'body' => $body,
                'mch_id' => $mchid,
                'nonce_str' => $nonce_str,
                'notify_url' => $notify_url,
                'openid'=>$openid,
                'out_trade_no' => $out_trade_no,
                'spbill_create_ip' => $ip,
                'total_fee' => $fee,
                'trade_type' => $trade_type
            ];
            $sign = $this->setSign($params);
            $xmlString = sprintf($xmlText, $appid, $body, $mchid, $nonce_str, $notify_url,$openid, $out_trade_no, $ip, $fee, $trade_type, $sign);
        }
        $httpClient = new \Cake\Network\Http\Client(['ssl_verify_peer' => false]);
        $res = $httpClient->post($apiurl, $xmlString);
        if (!$res->isOk()) {
            \Cake\Log\Log::error(__FILE__ . __LINE__, 'devlog');
            \Cake\Log\Log::error($res, 'devlog');
            return false;
        }
        $body = (array) simplexml_load_string($res->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($body['return_code'] == 'SUCCESS' && $body['result_code'] == 'SUCCESS') {
            \Cake\Log\Log::debug($xmlString, 'devlog');
            \Cake\Log\Log::debug($body, 'devlog');
            return $body;
        } else {
            \Cake\Log\Log::error('微信支付统一下单:', 'devlog');
            \Cake\Log\Log::error(__FUNCTION__, 'devlog');
            \Cake\Log\Log::error($body, 'devlog');
            \Cake\Log\Log::error($xmlString, 'devlog');
            \Cake\Log\Log::error($sign, 'devlog');
            return false;
        }
    }

    /**
     *  生成签名
     * @param type $params
     * @return type
     */
    public function setSign($params) {
        ksort($params);
        $stringA = urldecode(http_build_query($params)); //不要转义的
        $stringB = $stringA . '&key=' . $this->key;
        $sign = strtoupper(md5($stringB));
        return $sign;
    }

    /**
     * 生成支付参数 供页面JSapi 调用发起支付 (!!并且该页面URL 需是在微信公众号的微信支付那里配置了支付域)
     */
    public function setPayParameter($prepay_id, $isApp = false) {
        $timestamp = time();
        $nonceStr = createRandomCode(16);
        if (!$isApp) {
            $params = [
                'appId' => $this->app_id,
                'timeStamp' => "$timestamp",
                'nonceStr' => $nonceStr,
                'package' => 'prepay_id=' . $prepay_id,
                'signType' => 'MD5'
            ];
        } else {
            $params = [
                'appid' => $this->App_id,
                'partnerid' => $this->mchid,
                'prepayid' => $prepay_id,
                'timestamp' => "$timestamp",
                'noncestr' => $nonceStr,
                'package' => 'Sign=WXPay',
            ];
        }
        $sign = $this->setSign($params);
        if ($isApp) {
            $params['sign'] = $sign;
        } else {
            $params['paySign'] = $sign;
        }
        return $params;
    }


    /**
     * 直接获取 支付参数
     * @param type $body
     * @param type $openid
     * @param type $out_trade_no
     * @param type $fee
     * @param type $notify_url
     * @param boolean $isApp
     * @return boolean
     */
    public function getPayParameter($body, $openid, $out_trade_no, $fee, $notify_url = null, $isApp = false) {
        $fee = intval($fee * 100);
        $res = $this->unifiedorder($body, $openid, $out_trade_no, $fee, $notify_url, $isApp);
        if ($res) {
            $prepay_id = $res['prepay_id'];
            $pay_param = $this->setPayParameter($prepay_id, $isApp);
            return $pay_param;
        } else {
            \Cake\Log\Log::error(__FUNCTION__, 'devlog');
            \Cake\Log\Log::error($res, 'devlog');
            return false;
        }
    }


    /**
     *  付款 状态更改为完成 流程完毕
     * @return boolean
     */
    public function notify() {
        $data = $this->request->input('Cake\Utility\Xml::build');
        if ($data->result_code != 'SUCCESS') {
            \Cake\Log\Log::error('微信回调失败', 'devlog');
            \Cake\Log\Log::error($data->result_msg, 'devlog');
            return false;
        }
        $order_no = $data->out_trade_no;
        $out_trade_no = $data->transaction_id;
        $realFee = $data->total_fee / 100;
        $OrderTable = \Cake\ORM\TableRegistry::get('Payorder');
        $order = $OrderTable->find()->contain(['User', 'Seller'])
                    ->where(['Payorder.status' => 0, 'order_no' => $order_no])->first();
        $resXml = $this->arr2xml([
            'return_code' => 'FAIL',
            'return_msg' => 'error'
        ]);
        if ($order) {
            //检查订单是否处理过
            if(!$order->status) {
                $res = $this->Business->handOrder($order, $realFee, GlobalCode::PAY_TYPE_WX, $out_trade_no);
                if ($res) {
                    $resXml = $this->arr2xml([
                        'return_code' => 'SUCCESS',
                        'return_msg' => 'OK'
                    ]);
                } else {
                    $msg = '微信支付回调订单处理失败:' . $order->id;
                    runLog('微信支付', $msg, 'order_no:' . $order_no);
                    //$this->Util->dblog('order', $msg, $data);
                }
            }
        } else {
            $msg = '微信订单回调订单未找到，订单号:' . $order_no;
            runLog('微信支付', $msg, 'order_no:' . $order_no);
            //$this->Util->dblog('order', $msg, $data);
        }
        $this->response->body($resXml);
        $this->response->send();
        $this->response->stop();
    }

    /**
     *   简易将数组转换为xml，仅支持1级，适用于微信开发
     *    @param array $data    要转换的数组
     *   @param bool $root     是否要根节点
     *   @return string         xml字符串
     */
    protected function arr2xml($data, $root = true) {
        $str = "";
        if ($root) {
            $str .= "<xml>";
        }
        foreach ($data as $key => $val) {
            $str.= "<$key><![CDATA[$val]]></$key>";
        }
        if ($root) {
            $str .= "</xml>";
        }
        return $str;
    }

}
