<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use GlobalCode;

/**
 * 支付宝
 * 商家签约支付
 * Alipay component
 * @property \App\Controller\Component\BusinessComponent $Business
 * @property \App\Controller\Component\UtilComponent $Util
 */
class AlipayComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    public $components = ['Business','Util'];

    /**
     * 合作者id
     * @var type 
     */
    protected $partner;

    /**
     *  异步通知url
     * @var type 
     */
    protected $notify_url;

    /**
     * 卖家支付宝帐号
     * @var type 
     */
    protected $seller_id;

    /**
     * 私钥
     * @var type 
     */
    protected $private_key;

    /**
     * appid
     */
    protected $app_id;

    /**
     * 支付宝公钥
     * @var type 
     */
    protected $public_key;

    public function initialize(array $config) {
        parent::initialize($config);
        $conf = \Cake\Core\Configure::read('alipay');
        $this->partner = $conf['partner'];
        $this->seller_id = $conf['seller_id'];
        $this->private_key = file_get_contents($conf['private_key']);
        $this->public_key = file_get_contents($conf['public_key']);
        $this->notify_url = $this->request->scheme() . '://' . $_SERVER['SERVER_NAME'] . $conf['notify_url'];
        $this->app_id = $conf['appid'];
    }


    /**
     * 生成支付参数(旧版快捷支付接口)
     * @param type $order_no
     * @param type $order_title
     * @param type $order_fee
     * @param type $order_body
     * @return string
     */
    public function setPayParameter($order_no, $order_title, $order_fee, $order_body) {
        $params = [
            'service' => 'mobile.securitypay.pay',
            'partner' => $this->partner,
            '_input_charset' => 'utf-8',
            'notify_url' => $this->notify_url,
            'out_trade_no' => "$order_no",
            'subject' => $order_title,
            'payment_type' => '1',
            'seller_id' => $this->seller_id,
            'total_fee' => $order_fee,
            'body' => $order_body,
        ];
        ksort($params);
        $stringA = $this->buildLinkString($params); //不要转义的
        //$stringB = $stringA . '&key=' . $this->key;
        //$sign = strtoupper(md5($stringB));
        $res = openssl_get_privatekey($this->private_key);
        openssl_sign($stringA, $sign, $res);
        openssl_free_key($res);
        //base64编码
        $sign = urlencode(base64_encode($sign));
        $stringB = $stringA . '&sign="' . $sign . '"&sign_type="RSA"';
        return $stringB;
    }


    /**
     * 新版支付宝app支付接口参数初始化（还未调通的，禁用）
     * @param $order_no
     * @param $order_title
     * @param $order_fee
     * @param $order_body
     * @return string
     */
    public function setPayParameter4New($order_no, $order_title, $order_fee, $order_body) {
        //业务请求参数
        $biz_params = [
            'body' => $order_body,
            'subject' => $order_title,
            'out_trade_no' => "$order_no",
            'total_amount' => "$order_fee",
            'seller_id' => $this->seller_id,
            'product_code' => 'QUICK_MSECURITY_PAY',
        ];

        //公共请求参数
        $params = [
            'app_id' => $this->app_id,
            'method' => 'alipay.trade.app.pay',
            'charset' => 'utf-8',
            'timestamp' => date("Y-m-d H:i:s"),
            'version' => '1.0',
            'notify_url' => $this->notify_url,
            'biz_content' => json_encode($biz_params),
            'sign_type' => 'RSA2'
        ];

        //对原始字符串进行RSA2签名

        ksort($params);
        $stringA = $this->buildLinkString($params); //不要转义的
        $sign = $this->getRsa2Sign($stringA);
        //base64编码
        $sign = urlencode(base64_encode($sign));

        //新版支付宝接口，还未调通的
        //$stringB = $this->buildLinkString($params, true) . '&sign=' . $sign;

        $stringB = $stringA . '&sign="' . $sign . '"&sign_type="RSA"';
        return $stringB;
    }


    public function getRsaSign($paramstr) {
        $res = openssl_get_privatekey($this->private_key);
        openssl_sign($paramstr, $sign, $res);
        openssl_free_key($res);
        return $sign;
    }


    public function getRsa2Sign($paramstr) {
        tmpLog('待签名字符串：' . $paramstr);
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->private_key, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        //$priKey = openssl_get_privatekey($res);
        openssl_sign($paramstr, $sign, $res, OPENSSL_ALGO_SHA256);
        //openssl_free_key($res);
        $charset = mb_detect_encoding($sign, "UTF-8, GBK") == 'UTF-8' ? 'UTF-8' : 'GBK';
        if(!strcasecmp($charset, 'GBK')) {
            $sign = mb_convert_encoding($sign, "UTF-8", "GBK");
        }
        tmpLog('signCharset:' . (mb_detect_encoding($sign, "UTF-8, GBK") == 'UTF-8' ? 'UTF-8' : 'GBK'));
        return $sign;
    }

    /**
     * 
     * @param type $params
     */
    public function buildLinkString($params, $encode = false) {
        $string = '';
        if($encode) {
            foreach ($params as $key => $value) {
                $string.= $key . '=' . urlencode($value) . '&';
            }
        } else {
            foreach ($params as $key => $value) {
                $string.= $key . '=' . $value . '&';
            }
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
     * 说起来你可能不信，支付宝这一步拼接字符串又不需要引号，而它的demo版例子其实错的，我*
     * @param type $params
     */
    public function buildLinkStringNoQuota($params) {
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
     * RSA验签
     * @param $data 接收的数据
     * return 验证结果
     */
    public function rsaVerify($data) {
        $sign = $data['sign'];
        $para_filter = array();
        //过滤不被签名的数据
        foreach ($data as $key => $val) {
            if ($key == "sign" || $key == "sign_type" || $val == "") {
                continue;
            } else {
                $para_filter[$key] = $data[$key];
            }
        }
        //排序
        ksort($para_filter);
        reset($para_filter);
        $dataWait = $this->buildLinkStringNoQuota($para_filter);
        $res = openssl_get_publickey($this->alipay_public_key);
        $result =  openssl_verify($dataWait, base64_decode($sign), $res);
        $result = (bool) $result;
        openssl_free_key($res);
        if(!$result){
            \Cake\Log\Log::error(__FUNCTION__,'devlog');
            \Cake\Log\Log::error('验签失败','devlog');
        }
        return $result;
    }

    /**
     * 支付宝的回调验证，验证通过则进行 接下来的业务处理
     * @return boolean
     */
    public function notifyVerify() {
        //\Cake\Log\Log::error(__FUNCTION__,'devlog');
        if (!$this->request->is('post')) {//判断POST来的数组是否为空
            //\Cake\Log\Log::error('支付宝回调请求方式错误','devlog');
            runLog('支付宝支付', '支付宝回调请求方式错误');
            return false;
        } else {
            //生成签名结果
            $data = $this->request->data();
            //\Cake\Log\Log::debug($data,'devlog');
            $isSign = $this->rsaVerify($data);
            if ($isSign && !empty($data['notify_id'])) {
                //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
                //\Cake\Log\Log::error('验证通过，开始验证ANT结果','devlog');
                $responseTxt = 'false';
                $notify_id = $data['notify_id'];
                $httpClient = new \Cake\Network\Http\Client(['ssl_verify_peer' => false]);
                $verifyAlipayUrl = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=' . $this->partner . '&notify_id=' . $notify_id;
                $response = $httpClient->get($verifyAlipayUrl);
                if (!$response->isOk()) {
                    //\Cake\Log\Log::error('请求支付宝验证来源失败', 'devlog');
                    //\Cake\Log\Log::error($response, 'devlog');
                    runLog('支付宝支付', '请求支付宝验证来源失败:' . json_encode($response));
                    return false;
                }
                $responseTxt = $response->body();
                //\Cake\Log\Log::debug($responseTxt,'devlog');
                if (preg_match("/true$/i", $responseTxt) && $isSign) {
                    return true;
                } else {
                    return false;
                }
            } else {
                //\Cake\Log\Log::error('支付宝支付验签失败','devlog');
                runLog('支付宝支付', '支付宝支付验签失败');
                return false;
            }
        }
    }

    /**
     * 回调处理
     */
    public function notify() {
        $data = $this->request->data();
        if (isset($data['trade_status']) && $data['trade_status'] == 'TRADE_SUCCESS') {
            //支付宝端成功
            $order_no = $data['out_trade_no'];
            $OrderTable = \Cake\ORM\TableRegistry::get('Payorder');
            $order = $OrderTable->find()->contain(['User', 'Seller'])
                    ->where(['Payorder.status' => 0, 'order_no' => $order_no])->first();
            $output = 'fail';
            if ($order) {
                $realFee = $data['total_fee'];
                $out_trade_no = $data['trade_no'];
                if(!$order->status) {
                    $res = $this->Business->handOrder($order, $realFee, GlobalCode::PAY_TYPE_ALI, $out_trade_no);
                    if($res){
                        $output = 'success';
                    }
                }
            } else {
                //$this->Util->dblog('order', '支付宝交易回调查询订单失败,订单号:' . $order_no, $data);
                runLog('支付宝支付', '支付宝交易回调查询订单失败:' . json_encode($data), 'order_no:' . $order_no);
            }
            $this->response->body($output);
            $this->response->send();
            $this->response->stop();
        } else {
            //$this->Util->dblog('order','支付宝交易回调状态异常,状态值:' . $data['out_trade_no'], $data);
            runLog('支付宝支付', '支付宝交易回调状态异常:' . json_encode($data), '状态值:' . $data['out_trade_no']);
        }
    }

}
