<?php

namespace App\Controller\Mobile;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use GlobalCode;
use PackType;
use PayOrderType;
use PayType;
use Sysetting;

/**
 * Pay Controller
 * 处理支付相关
 * @property \App\Model\Table\UserTable $User
 * @property \App\Controller\Component\WxComponent $Wx
 * @property \App\Controller\Component\WxpayComponent $Wxpay
 * @property \App\Controller\Component\AlipayComponent $Alipay
 * @property \App\Controller\Component\BusinessComponent $Business
 */
class PayController extends AppController {

    protected $payConfig = [];
    public function initialize() {
        $this->payConfig = Configure::read('pay');
        parent::initialize();
    }


    //处理云支付回调
    public function cpNotify()
    {
        $this->loadComponent('Cp');
        $this->Cp->notify();
        exit();
    }


    /**
     * 微信的异步回调通知
     */
    public function wxNotify() {
        tmpLog('进入微信支付回调');
        $this->loadComponent('Wxpay');
        $this->Wxpay->notify();
        exit();
    }


    /**
     * 支付宝异步回调通知
     */
    public function aliNotify() {
        tmpLog('进入支付宝支付回调');
        $this->loadComponent('Alipay');
        if (!$this->Alipay->notifyVerify()) {
            //\Cake\Log\Log::error('验证失败', 'devlog');
            echo 'fail';
        } else {
            $this->Alipay->notify();
        }
        exit();
    }

    /**
     * 支付入口
     * @param int $oid  (id/fee二选一)支付订单id
     * @param int $fee (id/fee二选一)fee
     * @param int $ptype  (必须)支付方式  PayType::
     *
     * @param string $callback  (可选)回调页面url，默认为支付成功页面
     * @param int $otype  (可选)支付单类型，默认为PayOrder::CHONGZHI
     * @param int $rid (可选)关联id
     * @param int $sid (可选)卖家id，默认为平台
     * @param string $ptitle (可选)页面标题，默认为'支付'
     * @param string $descript (可选)支付描述，默认为'充值'
     */
    public function pay() {
        //微信支付过程会丢失前端传过来的数据，所以需要进行数据缓存
        if($this->request->is("weixin")) {
            $this->wxPayPrepare();
            $this->setPayParams2Session();
        }

        $params = $this->getPayParams();
        $PayorderTb = TableRegistry::get('Payorder');
        $payorder = null;
        if($params['oid']) {
            $payorder = $PayorderTb->find()->where(['id' => $params['oid']])->first();
            $params['fee'] = $payorder ? $payorder->price : $params['fee'];
        }

        //开始支付
        if($this->request->is('post', 'json')) {
            if(!$payorder) {
                if(!$params['fee']) {
                    $this->Common->failReturn();
                }
                $orderData = [
                    'fee' => $params['fee'],
                    'ptype' => 0,
                    'uid' => $params['uid'],
                    'sid' => $params['sid'],
                    'otype' => $params['otype'],
                    'rid' => $params['rid'],
                    'descript' => $params['descript']
                ];
                $payorder = $this->createPayOrder($orderData);
                if(!$payorder) {
                    $this->Common->dealReturn(false, '生成支付单失败');
                }
            }

            if(!$params['callback']) {
                $params['callback'] = '/pay/pay-success/' . $payorder->id;
            }
            $payorder->callback = $params['callback'];
            $out_trade_no = $payorder->order_no;

            $ptype = $this->request->data('ptype'); //支付方式PayType
            if(!$ptype) {
                $this->Common->dealReturn(false, '请选择支付方式');
            }
            switch ($ptype) {
                case GlobalCode::PAY_TYPE_WX :
                    $openid = $this->request->is('lemon') ? '' : $params['openid'];
                    if($params['openid'] || $this->request->is('lemon')) {
                        $body = $payorder->title;
                        $this->loadComponent('Wxpay');
                        $payParameters = $this->Wxpay->getPayParameter($body, $openid, $out_trade_no, 0.01, null, $this->request->is('lemon'));
                        //$payParameters = $this->Wxpay->getPayParameter($body, $openid, $out_trade_no, $params['fee'], null, $this->request->is('lemon'));
                        tmpLog('微信支付：' . json_encode($payParameters));
                        $this->Common->dealReturn(true, '微信支付初始化成功', ['payParams' => $payParameters, 'callback' => $params['callback']]);
                    } else {
                        $this->Common->dealReturn(true, '微信支付初始化失败', ['openid' => $params['openid']]);
                    }
                    break;
                case GlobalCode::PAY_TYPE_ALI :
                    $title = $payorder->title;
                    $body = $payorder->remark;
                    $this->loadComponent('Alipay');
                    //$payParameters = $this->Alipay->setPayParameter($out_trade_no, $title, $params['fee'], $body);
                    $payParameters = $this->Alipay->setPayParameter($out_trade_no, $title, 0.01, $body);
                    tmpLog('支付宝支付：' . json_encode($payParameters));
                    $this->Common->dealReturn(true, '支付宝支付初始化成功', ['payParams' => $payParameters, 'callback' => $params['callback']]);
                    break;
                default :
                    $this->Common->dealReturn(false, '非法支付方式');
                    break;
            }
        }

        //获取支付方式
        $settingTb = TableRegistry::get('Setting');
        $paySet = $settingTb->find()->where(['type' => GlobalCode::SETTING_PAY_SETTING])->first();
        $sortPays = [];
        if($paySet && ($curPayIds = json_decode($paySet['content']))) {
            $allPays = GlobalCode::getPayType();
            foreach ($allPays as $key => $pay) {
                foreach ($curPayIds as $curPayId) {
                    //如果是微信客户端打开的不显示支付宝等支付方式
                    if($this->request->is('weixin') && ($curPayId == GlobalCode::PAY_TYPE_ALI)) {
                        continue;
                    }
                    //如果是普通浏览器打开的则不显示微信等支付方式
                    /*else if(!$this->request->is('lemon') && $curPayId == PayType::CP_WX) {
                        continue;
                    }*/
                    if($key == $curPayId) {
                        $sortPays[$key] = GlobalCode::getPayui8Id($key);
                    }
                }
            }
        }

        $this->set([
            'title' => $params['descript'],
            'price' => $params['fee'],
            'oid' => $params['oid'],
            'sid' => $params['sid'],
            'rid' => $params['rid'],
            'pageTitle' => $params['ptitle'],
            'pays' => $sortPays
        ]);
    }


    /**
     * 微信支付预准备工作
     */
    private function wxPayPrepare()
    {
        //微信支付时openid的获取
        $openid = $this->request->session()->read('Pay.openid');
        $code = $this->request->query('code');
        $this->loadComponent('Wx');
        if (!$openid && $this->request->is('weixin') && !$this->request->session()->check('Pay.getopenid')) {
            //跳转获取 openid 只跳一次
            tmpLog('开始获取code');
            //微信支付获取预支付码回调本页面参数丢失，需预先保存
            $this->request->session()->write('Pay.getopenid', true);

            $this->Wx->getUserJump(true, true);
        }
        if ($code && !$openid) {
            tmpLog('获取到的code:' . $code);
            $res = $this->Wx->getUser($code);
            tmpLog('获取到的openid:' . json_encode($res));
            if ($res && $res->openid) {
                $openid = $res->openid;
                $this->request->session()->write('Pay.openid', $openid);
            }
        }
    }


    /**
     * 设置支付页参数到session中
     * 由于微信支付流程的特殊性，在支付前要获取预支付码，获取预支付码前要获取用户openid,获取openid需要微信回访导致参数丢失，所以这里对微信支付进行特殊处理
     */
    private function setPayParams2Session()
    {
        $this->request->session()->write('Pay.oid', $this->request->query('oid'));
        $this->request->session()->write('Pay.fee', $this->request->query('fee'));
        $this->request->session()->write('Pay.callback', $this->request->query('callback'));
        $this->request->session()->write('Pay.otype', $this->request->query('otype'));
        $this->request->session()->write('Pay.ptitle', $this->request->query('ptitle'));
        $this->request->session()->write('Pay.rid', $this->request->query('rid'));
        $this->request->session()->write('Pay.sid', $this->request->query('sid'));
        $this->request->session()->write('Pay.descript', $this->request->query('descript'));
    }

    /**
     * 获取支付页面参数，兼容了微信支付前设置好到session中的参数
     * @return array
     */
    private function getPayParams()
    {
        $uid = $this->user ? $this->user->id : null;
        $oid = $this->request->query('oid') ? $this->request->query('oid') : $this->request->session()->read('Pay.oid');
        $fee = $this->request->query('fee') ? $this->request->query('fee') : $this->request->session()->read('Pay.fee');
        $callback = $this->request->query('callback') ? $this->request->query('callback') : (
            $this->request->session()->read('Pay.callback') ? $this->request->session()->read('Pay.callback') : ''
        );
        $otype = $this->request->query('otype') ? $this->request->query('otype') : (
            $this->request->session()->read('Pay.otype') ? $this->request->session()->read('Pay.otype') : GlobalCode::PAYORDER_TYPE_CZ
        );
        $ptitle = $this->request->query('ptitle') ? $this->request->query('ptitle') : (
            $this->request->session()->read('Pay.ptitle') ? $this->request->session()->read('Pay.ptitle') : '支付'
        );
        $rid = $this->request->query('rid') ? $this->request->query('rid') : (
            $this->request->session()->read('Pay.rid') ? $this->request->session()->read('Pay.rid') : -1
        );
        $sid = $this->request->query('sid') ? $this->request->query('sid') : (
            $this->request->session()->read('Pay.sid') ? $this->request->session()->read('Pay.sid') : 0
        );
        $descript = $this->request->query('descript') ? $this->request->query('descript') : (
            $this->request->session()->read('Pay.descript') ? $this->request->session()->read('Pay.descript') : '充值'
        );
        $openid = $this->request->session()->read('Pay.openid');

        if($this->Business->checkIsCheck($this->user)) {
            $descript = '订单金额';
        }

        $params = [
            'uid' => $uid,
            'oid' => $oid,
            'fee' => $fee,
            'callback' => $callback,
            'otype' => $otype,
            'ptitle' => $ptitle,
            'rid' => $rid,
            'sid' => $sid,
            'descript' => $descript,
            'openid' => $openid
        ];

        tmpLog('获取到的所有参数：' . json_encode($params));

        return $params;
    }


    //生成支付单
    public function createPayOrder($data = [])
    {
        if(!isset($data['fee'])){
            return false;
        }
        $PayorderTable = TableRegistry::get('Payorder');
        $payorder = $PayorderTable->newEntity([
            'user_id' => isset($data['uid']) ? $data['uid'] : -1 ,   //买家可以是游客:-1
            'seller_id' => $data['sid'],
            'title' => isset($data['otype']) ? GlobalCode::getPayOrderType($data['otype']) : '未知',
            'order_no' => $this->Business->createPayorderNo($data),
            'relate_id' => isset($data['rid']) ? $data['rid'] : '-1',
            'type' => $data['otype'],
            'paytype' => $data['ptype'],
            'price' => $data['fee'],
            'fee' => 0,
            'remark' => isset($data['descript']) ? $data['descript'] : '充值',
        ]);
        if($PayorderTable->save($payorder)){
            return $payorder;
        } else {
            return false;
        }
    }


    /**
     * 支付成功
     */
    public function paySuccess($id = null) {
        $st = [];
        if ($id) {
            $OrderTable = TableRegistry::get('Payorder');
            $order = $OrderTable->get($id);
            if ($order) {
                $st = [
                    'pageTitle' => '充值成功',
                    'msg1' => '充值成功！',
                    'msg2' => null,
                    'rebtname' => '查看我的钱包',
                    'reurl' => '/userc/my-purse'
                ];
                switch (intval($order->type)) {
                    case GlobalCode::PAYORDER_TYPE_CZ :
                        $st['msg2'] = '充值金额：' . $order->price . '元';
                        break;
                    case GlobalCode::PAYORDER_TYPE_TC:
                    case GlobalCode::PAYORDER_TYPE_CZTC:
                        $pack = TableRegistry::get('Package')->get($order->relate_id);
                        if ($pack->type == GlobalCode::VIP_VIP) {
                            $st['pageTitle'] = '购买成功';
                            $st['msg1'] = '购买成功!';
                            $st['rebtname'] = '查看会员中心';
                            $st['reurl'] = '/userc/vip-center';
                        } elseif ($pack->type == PackType::RECHARGE) {
                            $st['msg2'] = '充值金额：' . $pack->vir_money . '元';
                        }
                        break;
                }
            }
        }
        $this->set([
            'pageTitle' => isset($st['pageTitle']) ? $st['pageTitle'] : '',
            'st' => $st
        ]);
    }
}
