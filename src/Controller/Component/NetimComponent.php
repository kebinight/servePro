<?php

namespace App\Controller\Component;

use App\Model\Entity\User;
use App\Shell\NetimShell;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use App\Pack\Netim;
use Cake\Core\Exception\Exception;

/**
 * Netim component  云信消息
 */
class NetimComponent extends Component {

    public $components = ['Util'];

    /**
     * 
     *
     */
    protected $Netim;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    protected $appkey;
    protected $appSecret;

    public function initialize(array $config) {
        parent::initialize($config);
        $this->Netim = new Netim();
    }

    
    /**
     * 支付完预约金
     * @param \App\Model\Entity\Dateorder $order
     * @return type
     */
    public function prepayMsg(\App\Model\Entity\Dateorder $order) {
        $from_prefix = '';
        $from_link = transformUrl('/date-order/order-detail/' . $order->id);
        $from_body = '我已发出约单，快来确认吧！';
        $from_link_text = '查看详情';
        $from_msg = $this->Netim->generateCustomMsgBody($from_body, $from_link, $from_link_text, $from_prefix);

        $to_prefix = '[' . $order->skill_name . ']';
        $to_link = $from_link;
        $lasth = $order->end_time->hour - $order->start_time->hour;
        $to_body = '我希望约您在' . $order->site . '，时间为' . $order->start_time . '~' . $order->end_time .
                '，共' . $lasth . '个小时，已预付诚意金' . $order->pre_pay . '元，期待您赴约。';
        $to_link_text = '查看详情';
        $to_msg = $this->Netim->generateCustomMsgBody($to_body, $to_link, $to_link_text, $to_prefix);
        $msg = $this->Netim->generateCustomMsg(Netim::CUSTOM_5_DATEMSG, $from_msg, $to_msg);
        $res = $this->Netim->sendMsg($order->buyer->imaccid, $order->dater->imaccid, $msg);
        if (!$res) {
            \Cake\Log\Log::error($res,'devlog');
            dblog('prepayMsg', 'server发送im消息失败', $res);
        }
        return $res;
    }


    /**
     * 美女接受订单
     * @param \App\Model\Entity\Dateorder $order
     * @return type
     */
    public function receiveMsg(\App\Model\Entity\Dateorder $order){
        $from_prefix = '';
        $from_link = transformUrl('/date-order/order-detail/' . $order->id);
        $from_body = '我已同意你的邀请，期待与你相约。';
        $from_link_text = '查看详情';
        $from_msg = $this->Netim->generateCustomMsgBody($from_body, $from_link, $from_link_text, $from_prefix);

        $to_prefix = '';
        $to_link = $from_link;
        $to_body = '我已同意你的邀请，期待与你相约。';
        $to_link_text = '去付尾款';
        $to_msg = $this->Netim->generateCustomMsgBody($to_body, $to_link, $to_link_text, $to_prefix);
        $msg = $this->Netim->generateCustomMsg(Netim::CUSTOM_5_DATEMSG, $from_msg, $to_msg);
        $res = $this->Netim->sendMsg($order->dater->imaccid, $order->buyer->imaccid, $msg);
        if (!$res) {
            \Cake\Log\Log::error($res,'devlog');
            dblog('prepayMsg', 'server发送im消息失败', $res);
        }
        return $res;
    }


    /**
     * 支付完尾款
     * @param \App\Model\Entity\Dateorder $order
     * @return type
     */
    public function payallMsg(\App\Model\Entity\Dateorder $order){
        $from_prefix = '';
        $from_link = transformUrl('/date-order/order-detail/' . $order->id);
        $from_body = '我已付完尾款,我们不见不散！';
        $from_link_text = '查看详情';
        $from_msg = $this->Netim->generateCustomMsgBody($from_body, $from_link, $from_link_text, $from_prefix);

        $to_prefix = '';
        $to_link = $from_link;
        $to_body = '我已付完尾款,我们不见不散！';
        $to_link_text = '查看详情';
        $to_msg = $this->Netim->generateCustomMsgBody($to_body, $to_link, $to_link_text, $to_prefix);
        $msg = $this->Netim->generateCustomMsg(Netim::CUSTOM_5_DATEMSG, $from_msg, $to_msg);
        $res = $this->Netim->sendMsg($order->buyer->imaccid, $order->dater->imaccid, $msg);
        if (!$res) {
            \Cake\Log\Log::error($res,'devlog');
            dblog('prepayMsg', 'server发送im消息失败', $res);
        }
        return $res;
    }




    /**
     * 发送礼物消息
     * @param type $from
     * @param type $to
     * @param type $gift
     * @return type
     */
    public function giftMsg(\App\Model\Entity\User $from,  \App\Model\Entity\User $to,  \App\Model\Entity\Gift $gift) {
        $gift_name = $gift->name;
        $from_prefix = '';
        $from_link = transformUrl('/userc/my-purse');
        $from_body = '送你一个'.$gift_name;
        $from_link_text = '查看详情';
        $from_msg = $this->Netim->generateCustomMsgBody($from_body, $from_link, $from_link_text, $from_prefix);

        $to_prefix = '['.$gift_name.']';
        $to_link = $from_link;
        $to_body = '对方送你一个'.$gift_name;
        $to_link_text = '查看详情';
        $to_msg = $this->Netim->generateCustomMsgBody($to_body, $to_link, $to_link_text, $to_prefix);
        $msg = $this->Netim->generateCustomMsg(Netim::CUSTOM_6_GIFTMSG, $from_msg, $to_msg,['gift_type'=>  intval($gift->no)]);
        $res = $this->Netim->sendMsg($from->imaccid, $to->imaccid, $msg, Netim::CUSTOM_MSG, 0);
        if (!$res) {
            dblog('prepayMsg', 'server发送im消息失败', $res);
        }
        return $res;
    }


    /**
     * 将需要更新im信息的用户id加入请求队列
     * @param $id
     */
    public function addUpInfoQueue($id){
        $redis = new \Redis();
        $redis_conf = \Cake\Core\Configure::read('Redis.default');
        try {
            $redis->connect($redis_conf['host'], $redis_conf['port']);
            $redis->rPush(NetimShell::REDIS_WAIT_UPINFO_KEY, $id); //缓冲进redis 队列
            //runLog('netim添加更新名片队列', '...', 'user_id: ' . $id);
        } catch (Exception $exc) {
            //dblog('redis error','netim更新名片进入队列失败', '用户id:'.$id);
            runLog('netim更新名片', '进入队列失败', 'user_id: ' . $id);
        }

    }


    /**
     * 发送平台通知
     * @param $from_accid
     * @param $to_accid
     * @return mixed
     */
    public function sendNotice(User $from, User $to)
    {
        $attach = $this->Netim->generateNoticeAttachStr(1, $from->id);
        $res = $this->Netim->sendNotice($from->imaccid, $to->imaccid, $attach);
        return $res;
    }
}
