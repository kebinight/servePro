<?php
namespace App\Controller\Component;

use App\Model\Entity\Payorder;
use App\Model\Entity\User;
use App\Pack\Aitask;
use App\Pack\Netim;
use Cake\Controller\Component;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use CheckStatus;
use GlobalCode;
use MovementType;
use MsgpushType;
use PackType;
use PayOrderType;
use SerRight;
use ServiceType;
use UserStatus;

/**
 * 项目业务组件
 * Business component  
 */
class BusinessComponent extends Component
{
    
    const REDIS_HASH_KEY = 'momoai_im_pool_hash';
    const REDIS_SET_KEY = 'momoai_im_pool';
    /**
     * Default configuration.
     *
     * @var array
     */
    public $components = ['Push', 'Netim', 'Sms'];
    protected $_defaultConfig = [];


    /**
     * 获取1级技能标签 从缓存或数据库当中
     */
    public function getTopSkill(){
        $skills = \Cake\Cache\Cache::read('topskill');
        if(!$skills){
            $SkillTable = \Cake\ORM\TableRegistry::get('Skill');
            $skills = $SkillTable->find()->hydrate(true)->where(['parent_id IS' => null])->toArray();
            if($skills){
                \Cake\Cache\Cache::write('topskill',$skills);
            } 
        }
        return $skills;
    }


    /**
     * 获取我的排名对象
     * @param string $type
     * @return mixed|null
     */
    public function getMyTop($type = 'week', $userid) {

        $mytop = null;
        //获取我的排名
        $mwhere = ['user_id' => $userid, 'income' => 1];
        if('week' == $type) {
            $mwhere['Flow.create_time >='] = new Time('last sunday');
        } else if('month' == $type) {
            $da = new Time();
            $mwhere['Flow.create_time >='] =
                new Time(new Time($da->year . '-' . $da->month . '-' . '01 00:00:00'));
        }
        $FlowTable = TableRegistry::get('Flow');
        $query = $FlowTable->find();
        $query->contain([
            'User' => function($q) use($userid) {
                return $q->select(['id','avatar','nick','phone','gender','birthday'])
                        ->where(['User.id' => $userid]);
            },
            'User.Upacks',
            'User.Supporteds',
        ])
            ->select(['total' => 'sum(amount)'])
            ->where($mwhere)
            ->map(function($row) {
                $row['user']['age'] = getAge($row['user']['birthday']);
                $row['total'] = intval($row['total']);
                $row['ishead'] = false;
                $row['isHongRen'] = false;
                if((count($row['user']['supporteds']) >= 100) && (count($row['user']['upacks'] >= 100))) {
                    $row['isHongRen'] = true;
                }
                return $row;
            });
        $mytop = $query->first();
        if($mytop) {
            $mytop->user->age = isset($mytop->user->birthday)?getAge($mytop->user->birthday):'xx';
            $mytop->ishead = true;
            if(!$mytop->total) {
                //如果魅力值为0则不参与排名
                return null;
            }

            //获取我的排名对象
            $where = ['income' => 1];
            if('week' == $type) {
                $where['Flow.create_time >='] = new Time('last sunday');
            } else if('month' == $type) {
                $da = new Time();
                $where['Flow.create_time >='] =
                    new Time(new Time($da->year . '-' . $da->month . '-' . '01 00:00:00'));
            }
            $where['User.gender'] = 2;
            $where['User.id !='] = $mytop->user->id;
            $iquery = $FlowTable
                ->find('all')
                ->contain([
                    'User'
                ])
                //->select(['total' => 'sum(amount)'])
                ->where($where)
                ->group('Flow.user_id')
                ->having(['sum(amount) >= ' => $mytop->total]);

            //计算排名
            $mytop->index = $iquery->count() + 1;
            $mytop->total = 10000 + $mytop->total;
        }
        return $mytop;
    }


    /**
     * 获取用户vip等级
     */
    public function getVIP(User $user)
    {
        //累计充值3万元=钻石
        if($user->recharge >= \VIPlevel::ZUANSHI_VIP_CONSUME) {
            return \VIPlevel::ZUANSHI_VIP;
        }
        //累计充值1万元=白金
        else if($user->recharge >= \VIPlevel::BAIJIN_VIP_CONSUME) {
            return \VIPlevel::BAIJIN_VIP;
        }
        //累计充值3999=黄金
        else if($user->recharge >= \VIPlevel::HUANGJIN_VIP_CONSUME) {
            return \VIPlevel::HUANGJIN_VIP;
        }

        $type = [PackType::VIP, PackType::LINSHI];
        $upackTb = TableRegistry::get('UserPackage');
        $validpack = $upackTb->find()->where([
            'user_id' => $user->id,
            'type IN' => $type,
            'deadline >' => new Time()
        ])->first();
        if($validpack) {
            return \VIPlevel::COMMON_VIP;
        }
        return \VIPlevel::NOT_VIP;
    }


    /**
     * 检测是否约见吧红人
     * 规则：100个被查看动态&&收到100个礼物
     * @return boolean
     */
    public function isMYHongRen(User $user)
    {
        if($user->gender == 1) {
            return false;
        }
        $usedPackTb = TableRegistry::get('UsedPackage');
        $supportTb = TableRegistry::get('Support');
        $usednum = $usedPackTb->find()->where(['used_id' => $user->id])->count();
        $supportnum = $supportTb->find()->where(['supported_id' => $user->id])->count();
        if(($usednum >= 100) && ($supportnum >= 100)) {
            return true;
        }
        return false;
    }


    /**
     * 检测是否显示土豪徽章
     * 规则：土豪排名前100
     * @return boolean
     */
    public function isTuHao(User $user)
    {
        if($user->gender == 2) {
            return false;
        }
        $userTb = TableRegistry::get('User');
        $mypaiming = $userTb->find()
                ->select(['recharge'])
                ->where(['recharge >' => $user->recharge, 'gender' => 1])
                ->count() + 1;
        if($mypaiming <= 100) {
            return true;
        }
        return false;
    }


    /**
     * 检测是否显示活跃徽章
     * 规则：连续7天登录
     * @return boolean
     */
    public function isActive(User $user)
    {
        return false;
    }


    /**
     * 获取用于显示的徽章数组
     *
     */
    public function getShown(User $user = null)
    {
        $shown = [
            'isHongRen' => false,
            'isTuHao' => false,
            'isActive' => false,
            'vipLevel' => \VIPlevel::NOT_VIP
        ];
        if(!$user) {
            return $shown;
        }
        if($user->gender == 1) {
            $shown['isHongRen'] = $this->isMYHongRen($user);
            $shown['vipLevel'] = $this->getVIP($user);
        } else {
            $shown['isTuHao'] = $this->isTuHao($user);
        }
        $shown['isActive'] = $this->isActive($user);
        return $shown;
    }


    /**
     * @author: kebin
     * 与美女聊天、查看美女动态
     * 检查是否有权限
     * data必须参数：
     *      int userid 使用者id
     *      int usedid 作用对象id
     *      int type   使用类型，见ServiceType类
     * 返回结果：
 *          case 0:不合法参数
 *          case 1:可以访问（已经消耗过额度）
 *          case 2:可以访问（尚未消耗额度）
 *          case 3:不可以访问（没有额度可以消耗）
     */
    public function checkRight($userid = null, $usedid = null, $type = null) {
        if(
            $userid === null
            &&$usedid === null
            &&$type === null
            &&!ServiceType::containType($type)
        ) {
            return 0;
        }

        //检查是否有权限看
        $usedPackTb = TableRegistry::get('UsedPackage');
        $usedPack = $usedPackTb
            ->find()
            ->select('id')
            ->where(
                [
                    'user_id' => $userid,
                    'used_id' => $usedid,
                    'type' => $type,
                    'deadline >' => new Time()
                ])
            ->first();
        if($usedPack) {
            return SerRight::OK_CONSUMED;
        } else {
            //审核模式
            $iosCheckConf = \Cake\Core\Configure::read('ios_check_conf');
            $user = null;
            try {
                $user = TableRegistry::get("User")->get($userid);
            } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                return SerRight::NO_HAVENOPOINT;
            }

            if($this->checkIsCheck($user)) {
                if(($type == ServiceType::BROWSE) && ($user->bonus_point >= $iosCheckConf['view_dt_point'])) {
                    return SerRight::NO_HAVEPOINT;
                } else if(($type == ServiceType::CHAT) && ($user->bonus_point >= $iosCheckConf['chat_point'])) {
                    return SerRight::NO_HAVEPOINT;
                } else {
                    return SerRight::NO_HAVENOPOINT;
                }
            } else {
                $userPackTb = TableRegistry::get('UserPackage');
                $key = "sum(" . ServiceType::getDBRestr($type) . ")";
                $userPack = $userPackTb
                    ->find()
                    ->select(['rest' => $key])
                    ->where(
                        [
                            'user_id' => $userid,
                            'deadline >' => new Time(),
                        ])
                    ->first();
                if($userPack) {
                    $rest = $userPack->rest;
                    if($rest > 0) {
                        return SerRight::NO_HAVENUM;
                    }
                }
                return SerRight::NO_HAVENONUM;
            }
        }
    }


    /**
     * @author: kebin
     * 名额充足的情况下
     * 直接消耗一个名额
     * data必须参数：
     *      int userid 使用者id
     *      int usedid 作用对象id
     *      int type   使用类型，见ServiceType类
     */
    function consumeRightD($userid, $usedid, $type)
    {
        //审核模式
        $iosCheckConf = \Cake\Core\Configure::read('ios_check_conf');
        $userTb = TableRegistry::get("User");
        $user = $userTb->get($userid);
        if($this->checkIsCheck($user)) {
            //生成消费记录
            $usedPackTb = TableRegistry::get("UsedPackage");
            $usedPack = $usedPackTb
                ->newEntity([
                    'user_id' => $userid,
                    'used_id' => $usedid,
                    'package_id' => 0,  //表示零售
                    'type' => $type,
                    'deadline' => new Time('+1 year'),
                ]);
            if($type == ServiceType::BROWSE) {
                if($user->bonus_point < $iosCheckConf['view_dt_point']) {
                    return false;
                }
                $user->bonus_point -= $iosCheckConf['view_dt_point'];
            } else{
                if($user->bonus_point < $iosCheckConf['chat_point']) {
                    return false;
                }
                $user->bonus_point -= $iosCheckConf['chat_point'];
            }
            $transRes = $usedPackTb
                ->connection()
                ->transactional(
                    function() use ($user, $userTb, $usedPack, $usedPackTb){
                        $useres = $userTb->save($user);
                        $dpackres = $usedPackTb->save($usedPack);
                        return $useres&&$dpackres;
                    });
            if($transRes) {
                return true;
            } else {
                return false;
            }
        } else {
            $userPackTb = TableRegistry::get("UserPackage");
            $key = ServiceType::getDBRestr($type);
            $userPack = $userPackTb
                ->find()
                ->where(
                    [
                        'user_id' => $userid,
                        $key.' >' => 0,
                        'deadline >' => new Time(),
                    ])
                ->orderAsc('create_time')
                ->first();
            if($userPack) {
                $userPack->$key --;
                //生成消费记录
                $usedPackTb = TableRegistry::get("UsedPackage");
                $usedPack = $usedPackTb
                    ->newEntity([
                        'user_id' => $userPack->user_id,
                        'used_id' => $usedid,
                        'package_id' => $userPack->id,
                        'type' => $type,
                        'deadline' => $userPack->deadline,
                    ]);
                $transRes = $usedPackTb
                    ->connection()
                    ->transactional(
                        function() use ($userPack, $userPackTb, $usedPack, $usedPackTb){
                            $upackres = $userPackTb->save($userPack);
                            $dpackres = $usedPackTb->save($usedPack);
                            return $upackres&&$dpackres;
                        });
                if($transRes) {
                    return true;
                }
            }
            return false;
        }
    }


    /**
     * @author: kebin
     * 会检查权限和名额剩余
     * 如果已经消费了则直接返回true而不消费名额
     * 消耗一个名额
     * data必须参数：
     *      int userid 使用者id
     *      int usedid 作用对象id
     *      int type   使用类型，见ServiceType类
     */
    public function consumeRight($userid, $usedid, $type)
    {
        $chres = $this->checkRight($userid, $usedid, $type);
        if($chres == SerRight::OK_CONSUMED) {
            return true;
        } else if(($chres == SerRight::NO_HAVENUM) || ($chres == SerRight::NO_HAVEPOINT)) {
            return $this->consumeRightD($userid, $usedid, $type);
        }
        return false;
    }


     /**
     * 处理订单业务
     * @param \App\Model\Entity\Order $order
     * @param float $realFee 实际支付金额
     * @param int $payType 支付方式 1微信2支付宝
     * @param string $out_trade_no 第三方平台交易号
     */
    public function handOrder(\App\Model\Entity\Payorder $order, $realFee, $payType, $out_trade_no) {
        //安全过滤，如果实际支付的金额与订单需金额不符合则直接过滤不处理
        /*if($order->price != $realFee) {
            runLog('支付回调', '订单金额与直接支付金额不符合', 'order_id:' . $order->id, 0);
            return false;
        }*/

        if ($order->type == GlobalCode::PAYORDER_TYPE_CZ) {
            return $this->handType1Order($order, $realFee, $payType, $out_trade_no);
        } elseif ($order->type == GlobalCode::PAYORDER_TYPE_TC || $order->type == GlobalCode::PAYORDER_TYPE_CZTC) {
            //购买套餐成功
            return $this->handPackPay($order, $realFee, $payType, $out_trade_no);
        } elseif ($order->type == GlobalCode::PAYORDER_TYPE_WX) {
            //支付微信查看金成功
            return $this->handViewWxPay($order, $realFee, $payType, $out_trade_no);
        } elseif ($order->type == GlobalCode::PAYORDER_TYPE_BVD) {
            //购买观看美女形象视频
            return $this->handViewBvPay($order, $realFee, $payType, $out_trade_no);
        } elseif ($order->type == GlobalCode::PAYORDER_TYPE_GIFT) {
            //赠送礼物
            return $this->handSendGiftPay($order, $realFee, $payType, $out_trade_no);
        }
    }

    
    /**
    * 处理type1  直接充值 支付订单状态更改  改变余额  生成流水
    * @param \App\Model\Entity\Order $order
    */
    protected function handType1Order(\App\Model\Entity\Payorder $order, $realFee, $payType, $out_trade_no) {
        $order->fee = $realFee;  //实际支付金额
        $order->paytype = $payType;  //实际支付方式
        $order->out_trade_no = $out_trade_no;  //第三方订单号
        $order->status = 1;
        $pre_amount = $order->user->money;
        $order->user->money += $order->price;    //专家余额+
        $order->user->recharge += $realFee;
        $order->user->is_normal = 1;
        $order->dirty('user', true);  //这里的seller 一定得是关联属性 不是关联模型名称 可以理解为实体
        $OrderTable = \Cake\ORM\TableRegistry::get('Payorder');
        $FlowTable = \Cake\ORM\TableRegistry::get('Flow');
        $flow = $FlowTable->newEntity([
            'user_id' => $order->user_id,
            'type' => \FlowType::CZ_PAY,
            'relate_id' => $order->id,   //关联的订单id
            'type_msg' => '账户充值',
            'income' => 1,
            'amount' => $realFee,
            'price' => $order->price,
            'pre_amount' => $pre_amount,
            'after_amount' => $order->user->money,
            'paytype' => $payType,
            'status' => 1,
            'remark' => '普通充值' . $order->price
        ]);
        $transRes = $OrderTable->connection()->transactional(function()use(&$order, $OrderTable, $FlowTable, &$flow) {
            return $OrderTable->save($order) &&  $FlowTable->save($flow);
        });
        if ($transRes) {
            $this->shareIncome($realFee, $order->user, $order->id);
            return true;
        }else{
            //\Cake\Log\Log::debug($order->errors(),'devlog');
            //\Cake\Log\Log::debug($flow->errors(),'devlog');
            //dblog('recharge','充值回调业务处理失败',$order->id);
            runLog('充值业务', '回调处理失败:order_errors:' . json_encode($order->errors()) . 'flow_errors:' . json_encode($flow->errors()), 'order_id:' . $order->id);
            return false;
        }
    }


    /**
     * 购买套餐支付成功后处理接口
     */
    public function handPackPay(Payorder $order, $realFee, $payType, $out_trade_no)
    {
        $packTb = TableRegistry::get('Package');
        try {
            $pack = $packTb->get($order->relate_id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return false;
        }

        //更新支付单信息
        $order->fee = $realFee;  //实际支付金额
        $order->paytype = $payType;  //实际支付方式
        $order->out_trade_no = $out_trade_no;  //第三方订单号
        $order->status = 1;
        $pre_amount = $order->user->money;
        $order->user->money += $pack->vir_money;
        $packTypeStr = PackType::getPackageType(PackType::RECHARGE);
        $flowType = GlobalCode::VIP_VIP;  //购买vip套餐
        if($pack->type == GlobalCode::VIP_VIP) {
            $packTypeStr = GlobalCode::getPackageType(GlobalCode::VIP_VIP);
            $order->user->consumed += $realFee;
        } else if($pack->type == GlobalCode::VIP_RECHARGE) {
            $flowType = GlobalCode::PAYORDER_TYPE_CZTC;  //购买充值套餐
            $packTypeStr = GlobalCode::getPackageType(GlobalCode::VIP_RECHARGE);
        }
        $order->user->recharge += $realFee;
        $order->dirty('user', true);  //这里的seller 一定得是关联属性 不是关联模型名称 可以理解为实体
        $OrderTable = TableRegistry::get('Payorder');

        //生成流水记录
        $FlowTable = TableRegistry::get('Flow');
        $flow = $FlowTable->newEntity([
            'user_id' => $order->user_id,
            'type' => $flowType,  //购买套餐
            'relate_id' => $order->id,   //关联的订单id
            'type_msg' => '购买' . $packTypeStr,
            'income' => 1,
            'amount' => $realFee,
            'price'=> $order->price,
            'pre_amount' => $pre_amount,
            'after_amount' => $order->user->money,
            'paytype' => $payType,
            'status' => 1,
            'remark' => '购买' . $packTypeStr
        ]);

        //生成套餐购买记录
        //查询当前用户账户下套餐的最长有效期
        $addDays = $pack->vali_time + 1;
        $deadline = new Time("+$addDays day");
        $deadline->hour = 0;
        $deadline->second = 0;
        $deadline->minute = 0;
        $userPackTb = TableRegistry::get('UserPackage');
        $query = $userPackTb
            ->find()
            ->select(['longestdl' => 'max(deadline)'])
            ->where([
                'user_id' => $order->user->id,
                'deadline >' => new Time()
            ]);
        $ownPach = $query->first();
        //计算出最长有效期
        //是否需要更新UserPackage表和UsedPackage表该用户的截止日期标志
        $udFlag = false;
        if($ownPach->longestdl) {
            $longestdl = new Time($ownPach->longestdl);
            if($deadline > $longestdl) {
                //购买的套餐以最长截止日期为准
                $udFlag = true;
            } else {
                $deadline = $longestdl;
            }
        }
        $userPack = $userPackTb->newEntity([
            'title' => $pack->title,
            'user_id' => $order->user->id,
            'package_id' => $pack->id,
            'chat_num' => $pack->chat_num,
            'rest_chat' => $pack->chat_num,
            'browse_num' => $pack->browse_num,
            'rest_browse' => $pack->browse_num,
            'type' => $pack->type,
            'cost' => $pack->price,
            'vir_money' => $pack->vir_money,
            'deadline' => $deadline,
            'honour_name' => $pack->honour_name,
        ]);
        $user = $order->user;
        $transRes = $userPackTb
            ->connection()
            ->transactional(
                function() use ($FlowTable, $flow, $OrderTable, $order, $user, $userPack, $userPackTb, $udFlag, $deadline){
                    $updateUsedres = true;
                    $updateUseres = true;
                    //更新UserPackage表和UsedPackage表该用户的截止日期
                    //如果用户买了新的套餐，该套餐截止日期比现有的长，则更新所有未过期的已购买套餐
                    if($udFlag) {
                        $usedPackTb = TableRegistry::get('UsedPackage');
                        $updateUsedres = $usedPackTb
                            ->query()
                            ->update()
                            ->set(['deadline' => $deadline])
                            ->where(['user_id' => $user->id, 'deadline >=' => new Time()])
                            ->execute();

                        $updateUseres = $userPackTb
                            ->query()
                            ->update()
                            ->set(['deadline' => $deadline])
                            ->where(['user_id' => $user->id, 'deadline >=' => new Time()])
                            ->execute();
                    }
                    $orderes = $OrderTable->save($order);
                    $useres = TableRegistry::get('User')->save($user);
                    $flowres = $FlowTable->save($flow);
                    return
                        $flowres
                        &&$orderes
                        &&$useres
                        &&$userPackTb->save($userPack)
                        &&$updateUsedres
                        &&$updateUseres;
                });

        if ($transRes) {
            //资金流水记录
            if($order->type == GlobalCode::VIP_RECHARGE) {
                $this->shareIncome($realFee, $order->user);
            }

            //添加机器人干扰任务
            $aiTask = new Aitask();
            $aiTask->addBuyPackTask($user);
            $aiTask->updateUser($user, ['packDeadline' => $deadline->timestamp]);
            return true;
        }else{
            \Cake\Log\Log::debug($order->errors(),'devlog');
            \Cake\Log\Log::debug($flow->errors(),'devlog');
            //dblog('recharge','套餐回调业务处理失败',$order->id);
            runLog('购买套餐', '套餐回调业务处理失败', 'orderid:' . $order->id);
            return false;
        }
    }


    /**
     * 支付微信查看金成功处理接口
     */
    public function handViewWxPay(Payorder $order, $realFee, $payType, $out_trade_no)
    {
        //更新订单状态消息及用户信息
        $order->fee = $realFee;  //实际支付金额
        $order->paytype = $payType;  //实际支付方式
        $order->out_trade_no = $out_trade_no;  //第三方订单号
        $order->status = 1;
        $order->user->recharge += $realFee;
        $order->user->consumed += $realFee;
        $order->dirty('user', true);  //这里的seller 一定得是关联属性 不是关联模型名称 可以理解为实体
        $OrderTable = TableRegistry::get('Payorder');

        //修改收款方费用
        $userTb = TableRegistry::get('User');
        $in_user = $userTb->get($order->relate_id);
        if(!$in_user) {
            return $this->Util->ajaxReturn(false, '用户不存在');
        }
        $in_user->money = $in_user->money + $realFee;
        $in_user->charm = $in_user->charm + $realFee;
        //生成流水
        $FlowTable = TableRegistry::get('Flow');
        $flow = $FlowTable->newEntity([
            'user_id' => $order->relate_id,
            'buyer_id' => $order->user->id,
            'type' => \FlowType::WX_VIEW_PAY,
            'type_msg' => \FlowType::getStr(\FlowType::WX_VIEW_PAY),
            'income' => 2,
            'amount' => $realFee,
            'price' => $order->price,
            'pre_amount' => 0,
            'after_amount' => 0,
            'paytype' => $payType,
            'remark' => \FlowType::getStr(\FlowType::WX_VIEW_PAY)
        ]);
        //生成查看记录
        $anhao = '约见吧'.mt_rand(10000, 99999);
        $wxorderTb = TableRegistry::get('Wxorder');
        $wxorder = $wxorderTb->newEntity([
            'user_id' => $order->user->id,
            'wxer_id' => $order->relate_id,
            'anhao' => $anhao
        ]);
        $transRes = $FlowTable->connection()->transactional(function() use ($OrderTable, $order, $flow, $FlowTable, $wxorderTb, $wxorder, $in_user, $userTb){
            return $OrderTable->save($order)&&$FlowTable->save($flow)&&$wxorderTb->save($wxorder)&&$userTb->save($in_user);
        });
        if($transRes) {
            //发送平台消息+友盟推送消息给美女
            $msg2wxer = $this->createMsgBody(
                '查看微信',
                $order->user->nick . '已成功购买了您的微信号，其暗号为【' . $wxorder->anhao . '】',
                '',
                \MsgpushType::RM_VIEW_WX
            );
            $msg2user = $this->createMsgBody(
                '查看微信',
                '你已成功购买了' . $in_user->nick . '的微信号【' . $in_user->wxid . '】，添加其微信时，请注意一定要填写暗号【 ' . $wxorder->anhao . '】',
                '',
                \MsgpushType::RM_REGISTER
            );
            $this->sendSMsg($order->relate_id, $msg2wxer);
            $this->sendSMsg($this->user->id, $msg2user);
            return true;
        } else {
            return false;
        }
    }


    /**
     * 支付形象视频查看金成功处理接口
     */
    public function handViewBvPay(Payorder $order, $realFee, $payType, $out_trade_no)
    {
        //更新订单状态消息及用户信息
        $order->fee = $realFee;  //实际支付金额
        $order->paytype = $payType;  //实际支付方式
        $order->out_trade_no = $out_trade_no;  //第三方订单号
        $order->status = 1;
        $order->user->recharge += $realFee;
        $order->user->consumed += $realFee;
        $order->dirty('user', true);  //这里的seller 一定得是关联属性 不是关联模型名称 可以理解为实体
        $OrderTable = TableRegistry::get('Payorder');

        //修改收款方费用
        $userTb = TableRegistry::get('User');
        $in_user = $userTb->get($order->relate_id);
        if(!$in_user) {
            return $this->Util->ajaxReturn(false, '用户不存在');
        }
        $preamount = $in_user->money;
        $in_user->money = $in_user->money + $realFee;
        $afteramount = $in_user->money;
        $in_user->charm = $in_user->charm + $realFee;
        //生成流水
        $FlowTable = TableRegistry::get('Flow');
        $flow = $FlowTable->newEntity([
            'user_id'=> $order->relate_id,
            'buyer_id'=> $order->user->id,
            'type'=> \FlowType::BV_VIEW_PAY,
            'type_msg'=> \FlowType::getStr(\FlowType::BV_VIEW_PAY),
            'income'=> 1,
            'amount'=> $realFee,
            'price'=> $order->price,
            'pre_amount'=> $preamount,
            'after_amount'=> $afteramount,
            'paytype'=> $payType,
            'remark'=> \FlowType::getStr(\FlowType::BV_VIEW_PAY)
        ]);
        //生成查看记录
        $retailTb = TableRegistry::get('RetailOrder');
        $retail = $retailTb->newEntity([
            'type' => \RetailType::VIEW_BASIC_VID,
            'buyer_id' => $order->user->id,
            'user_id' => $in_user->id,
        ]);
        $transRes = $FlowTable->connection()->transactional(function() use ($OrderTable, $order, $flow, $FlowTable, $retailTb, $retail, $in_user, $userTb){
            return $OrderTable->save($order)&&$FlowTable->save($flow)&&$retailTb->save($retail)&&$userTb->save($in_user);
        });
        if($transRes) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 赠送礼物付款回调处理接口
     * @param \App\Model\Entity\Order $order
     */
    protected function handSendGiftPay(\App\Model\Entity\Payorder $order, $realFee, $payType, $out_trade_no) {
        //进行相关验证
        if(!$order->relate_id) {
            return false;
        }
        $giftTb = TableRegistry::get('Gift');
        $gift = $giftTb->get($order->relate_id);
        if(!$gift) {
            return false;
        }
        //支付金额与礼物价格不对等时返回失败
        if($realFee != $gift->price) {
            return false;
        }

        $order->fee = $realFee;  //实际支付金额
        $order->paytype = $payType;  //实际支付方式
        $order->out_trade_no = $out_trade_no;  //第三方订单号
        $order->status = 1;

        //生成流水
        $FlowTable = TableRegistry::get('Flow');
        $inflow = null;
        $outflow = null;
        //男性总消费值增加
        if($order->user && $order->user->id != 0) {
            $order->user->recharge += $realFee;
            $order->user->consumed += $realFee;
            $order->user->is_normal = 1;
            $order->dirty('user', true);
        }
        //修改支付方费用
        //即使是游客也需要生成一条流水来平衡做账
        $outpre_money = $order->user?$order->user->money:0;
        $outafter_money = $outpre_money;
        $outflow = [
            'user_id' => 0,
            'buyer_id' =>  $order->user?$order->user->id:0,
            'type' => \FlowType::GIFT_COST,
            'type_msg' =>'送礼物（' . $gift->name . ')',
            'income' => 2,
            'amount' => $realFee,
            'price' => $order->price,
            'pre_amount' => $outpre_money,
            'after_amount' => $outafter_money,
            'paytype' => $payType,
            'remark' => '礼物名称[' . $gift->name . ']|礼物价格[' . $gift->price . ']'
        ];

        //女性魅力值增加
        if($order->seller && $order->seller->id != 0) {
            $order->seller->charm += $realFee;
            $order->dirty('seller', true);

            //修改收款方费用
            $inpre_money = $order->seller->money;
            $order->seller->money += $realFee;
            $inafter_money = $inpre_money + $realFee;
            $inflow = [
                'user_id' => $order->seller->id,
                'buyer_id' =>  0,
                'type' => \FlowType::GIFT_COST,
                'type_msg' => '礼物（' . $gift->name . ')',
                'income' => 1,
                'amount' => $realFee,
                'price' => $realFee,
                'pre_amount' => $inpre_money,
                'after_amount' => $inafter_money,
                'paytype' => $payType,
                'remark' => '礼物名称[' . $gift->name . ']|礼物价格[' . $gift->price . ']'
            ];
        }

        //生成支持记录
        $supportTb = TableRegistry::get("Support");
        $support = $supportTb->newEntity(Array(
            'supporter_id' => $order->user_id,
            'supported_id' => $order->seller_id
        ));

        $transRes = $supportTb->connection()->transactional(
            function() use ($supportTb, $support, $FlowTable, $inflow, $outflow, $order){
                $oTb = TableRegistry::get('PayOrder');
                $ordres = $oTb->save($order);
                $inflow = $FlowTable->newEntity($inflow);
                $outflow = $FlowTable->newEntity($outflow);
                $supres = $supportTb->save($support);
                if($supres) {
                    $inflow->relate_id = $supres->id;
                    $outflow->relate_id = $supres->id;
                }
                $inflores = $FlowTable->save($inflow);
                $outflores = $FlowTable->save($outflow);
                return $supres&&$inflores&&$outflores&&$ordres;
            });

        if ($transRes) {
            return true;
        }else{
            //dblog('recharge','送礼物支付回调业务处理失败', $order->id);
            return false;
        }
    }

    
    /**
     * 获取网易im token 和accid
     */
    public function getNetim(){
        $RedisConf = \Cake\Core\Configure::read('Redis.default');
        $redis = new \Redis();
        $redis->connect($RedisConf['host'], $RedisConf['port']);
        $accid = $redis->sPop(self::REDIS_SET_KEY);
        $token = $redis->hGet(self::REDIS_HASH_KEY,$accid);
        if($accid===false){
            return false;
        }
        return [
            'accid'=>$accid,
            'token'=>$token
        ];
    }


     /**
     * 生成  支付订单
     */
    public function createPayorder($user, $param, $type=1){
        $PayorderTable = TableRegistry::get('Payorder');
        if($type==1){
            $payorder = $PayorderTable->newEntity([
                'user_id'=> $user->id,
                'title'=>'充值',
                'order_no'=>time() . $user->id . createRandomCode(4, 1),
                'price'=>  $param['mb'],
                'remark'=>  '充值'.$param['mb'].'元',
            ]);
        }
        $res = $PayorderTable->save($payorder);
        if($res){
            return $res;
        }else{
            return false;
        }
    }


    /**
     * 生成用户邀请码
     * @param $uid
     */
    public function createInviteCode($uid)
    {
        $before = $uid + 111111;
        $after = dechex($before);
        return ''.$after;
    }


    /**
     * 根据邀请码产生邀请关系
     * 邀请码 incode
     * 注册人id  uid
     */
    public function create2Invit($incode, $uid)
    {
        $inviterTb = TableRegistry::get('User');
        $inviter = $inviterTb->find()->select(['id', 'is_agent'])->where(['invit_code' => $incode])->first();
        if($inviter && ($inviter->is_agent == 1)) {
            $invTb = TableRegistry::get('Inviter');
            $inv = $invTb->find()->where(['invited_id' => $uid])->first();
            if(!$inv) {
                $inv = $invTb->newEntity([
                    'inviter_id' => $inviter->id,
                    'invited_id' => $uid,
                    'status' => 1,
                ]);
                $invTb->save($inv);
            }
        }
    }


    /**
     * 创建分成收入
     * @param $amount 收入/充值
     * @param App\Model\Entity\User $invited 被邀请者
     * @param int $relate_id 关联id
     */
    public function shareIncome($amount, \App\Model\Entity\User $invited, $relate_id = 0)
    {
        $cz_percent = 0.15;  //男性充值上家获得分成比例
        $sr_percent = 0.10;  //女性收入上家获得分成比例
        $invtb = TableRegistry::get('Inviter');
        $inv = $invtb->find()->contain(['Invitor'])->where(['invited_id' => $invited->id])->first();
        if($inv) {
            $invitor = $inv->invitor;
            if($invitor->is_agent == 2) {
                return false;
            }
            $admoney = 0;
            if($invited->gender == 1) {
                $admoney = $amount * $cz_percent;
                $type = \FlowType::FRIEND_CZ_COMM;  //好友充值
            } else {
                $admoney = $amount * $sr_percent;
                $type = \FlowType::FRIEND_SR_COMM;  //好友获得收入
            }
            $inv->income += $admoney;
            $preAmount = $invitor->money;
            $invitor->money += $admoney;
            $afterAmount = $invitor->money;
            //生成流水
            $FlowTable = TableRegistry::get('Flow');
            $flow = $FlowTable->newEntity([
                'user_id'=> $invitor->id,
                'buyer_id'=> 0,
                'type'=> $type,
                'type_msg'=> \FlowType::getStr($type),
                'income'=> 1,
                'relate_id'=> $relate_id,
                'amount'=> $admoney,
                'price'=> $admoney,
                'pre_amount'=> $preAmount,
                'after_amount'=> $afterAmount,
                'paytype'=>1,   //余额支付
                'remark'=> \FlowType::getStr($type)
            ]);
            $inv->dirty('invitor', true);
            $transRes = $FlowTable->connection()->transactional(
                function() use ($FlowTable, &$flow, $invtb, &$inv){
                    $flores = $FlowTable->save($flow);
                    $ires = $invtb->save($inv);
                    return $flores&&$ires;
                }
            );
            return $transRes;
        }
        return false;
    }


    /**
     * 发送平台消息-单发
     * @param int $uid 推送对象
     * @param array $message 推送消息体
     *      [
     *          'towho' => 推送说明(直接调用MsgpushType::TO_**类型的，例如，约会过程的通知使用MsgpushType::TO_DATER),
     *          'title' => string 标题,
     *          'body' => string 消息体,
     *          'to_url' => string 跳转链接,
     *      ]
     * @param boolean $umeng 是否推送消息
     * @return array 例如：MsgpushTye::ERROR_NOUSER
     */
    public function sendSMsg($uid, $message = [], $methods = [MsgpushType::METHOD_PTMSG, MsgpushType::METHOD_UMENG])
    {
        return $this->sendMsg([$uid], $message, $methods);
    }


    /**
     * 发送平台消息-群发
     * @param array $uids 推送对象集
     * @param array $message 推送消息体
     *      [
     *          'towho' => 推送说明(直接调用MsgpushType::TO_**类型的，例如，约会过程的通知使用MsgpushType::TO_DATER),
     *          'title' => string 标题,
     *          'body' => string 消息体,
     *          'to_url' => string 跳转链接,
     *      ]
     * @param boolean $umeng 是否推送消息
     * @return array 例如：MsgpushTye::ERROR_NOUSER
     */
    public function sendMsg($uids = [], $message = [], $methods = [MsgpushType::METHOD_PTMSG, MsgpushType::METHOD_UMENG])
    {
        //初始化推送结果集
        $results = [
            'pt_push_res' => false,
            'um_push_res' => false,
            'im_push_res' => false,
            'sms_push_res' => false
        ];
        if(!$message) {
            return $results;
        }
        $message['id'] = '';  //发送消息都会重新生成新的消息记录
        if($uids) {
            //保存推送对象列表记录
            $message['userlist'] = serialize($uids);
        }
        if($methods) {
            $message['methods'] = serialize($methods);
        } else {
            return $results;
        }
        //过滤消息体中前面的空格
        $message['to_url'] = trim($message['to_url']);
        //创建消息体
        $res = $this->addPtMsg($message);
        $msgid = 0;
        if(!$res['status']) {
            //消息创建失败
            return $results;
        }
        $msgid = $res['obj']['id'];

        //初始化发送对象列表
        $target_users = [];
        $usertb = TableRegistry::get('User');
        $select = ['id', 'user_token', 'imaccid', 'phone'];
        switch (intval($message['towho'])) {
            case MsgpushType::TO_ALL:
                $target_users = $usertb->find()->select($select)->where(['id !=' => 0])->toArray();
                break;
            case MsgpushType::TO_MAN:
                $target_users = $usertb->find()->select($select)->where(['id !=' => 0, 'gender' => 1])->toArray();
                break;
            case MsgpushType::TO_WOMAN:
                $target_users = $usertb->find()->select($select)->where(['id !=' => 0, 'gender' => 2])->toArray();
                break;
            case MsgpushType::TO_PASS:
                $target_users = $usertb->find()->select($select)->where(['id !=' => 0, 'OR' => [
                    ['status' => \UserStatus::PASS],
                    ['status' => \UserStatus::SHARE_PASS]
                ]])->toArray();
                break;
            case MsgpushType::TO_PER:
                $target_users = $usertb->find()->select($select)->where(['id IN' => $uids])->toArray();
                break;
        }
        //如果发送对象列表为空则不做任何操作
        if(empty($target_users)) {
            return $results;
        }

        if(in_array(MsgpushType::METHOD_ALL, $methods) || in_array(MsgpushType::METHOD_PTMSG, $methods)) {
            $results['pt_push_res'] = $this->sendPtMsg($target_users, $msgid);
        }
        if(in_array(MsgpushType::METHOD_ALL, $methods) || in_array(MsgpushType::METHOD_UMENG, $methods)) {
            $title = $message['body'];
            $content = ' ';
            $ticker = $message['body'];
            $alias = '';
            foreach($target_users as $target_user) {
                $alias .= $target_user['user_token'] . ',';
            }
            if(count($target_users) >= 50) {
                $results['um_push_res'] = $this->Push->sendFile($title, $content, $ticker, str_replace(',', "\n", $alias), 'MY', false);
            } else if(count($target_users) > 0) {
                $results['um_push_res'] = $this->Push->sendAlias($alias, $title, $content, $ticker, 'MY', false);
            }
        }
        if(in_array(MsgpushType::METHOD_ALL, $methods) || in_array(MsgpushType::METHOD_IM, $methods)) {
            $netim = new Netim();
            $to_body = $message['body'];
            $to_link = $message['to_url'];
            $to_link_text = '查看详情';
            $msgbody = $netim->generateCustomMsgBody($to_body, $to_link, $to_link_text, '');
            $msg = $netim->generatePushMsg($msgbody);

            $res = true;
            foreach ($target_users as $target_user) {
                $res = $res?$netim->sendMsg('mycs_0', $target_user->imaccid, $msg, Netim::CUSTOM_MSG):$res;
            }
            $results['im_push_res'] = $res;
        }
        if(in_array(MsgpushType::METHOD_ALL, $methods) || in_array(MsgpushType::METHOD_SMS, $methods)) {
            $phones = [];
            $content = $message['body'];
            if(isset($message['to_url'])) {
                $content .= getHost() . $message['to_url'];
            }
            foreach ($target_users as $target_user) {
                if($target_user->phone) {
                    $phones[] = $target_user->phone;
                }
            }
            $res = $this->Sms->sendCusNetim2Many($phones, $content);
            $results['sms_push_res'] = $res;
        }
        return $results;
    }


    /**
     * 发送平台消息-单发
     * @param int $uid 推送对象
     * @param int $msgid 推送消息id
     * @return boolean
     */
    public function sendSPtMsg($user, $msgid)
    {
        return $this->sendPtMsg([$user], $msgid);
    }


    /**
     * 发送平台消息-群发
     * @param array $uids 推送对象集
     * @param int $msgid 推送消息id
     * @param array $message 推送消息体
     *      [
     *          'towho' => 推送说明(直接调用MsgpushType::TO_**类型的，例如，约会过程的通知使用MsgpushType::TO_DATER),
     *          'title' => string 标题,
     *          'body' => string 消息体,
     *          'to_url' => string 跳转链接,
     *          'remark' => string 备注信息,
     *          'msg_type' => string 消息类型
     *      ]
     * @return boolean
     */
    public function sendPtMsg($users = [], $msgid)
    {
        if(empty($users)) {
            return false;
        }
        $msgpushTb = TableRegistry::get('Msgpush');
        $msgpushes = [];
        foreach($users as $user) {
            $msgpushes[] = [
                'msg_id' => $msgid,
                'user_id' => $user->id,
                'is_read' => 0,
                'is_del' => 0
            ];
        }
        $msgpushes = $msgpushTb->newEntities($msgpushes);
        $msgres = $msgpushTb->saveMany($msgpushes);
        if($msgres) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 创建平台消息
     */
    public function addPtMsg($message = [])
    {
        if(!$message) {
            return null;
        }
        $ptmsgtb = TableRegistry::get('Ptmsg');
        $ptmsg = $ptmsgtb->newEntity($message);
        return [
            'status' => $ptmsgtb->save($ptmsg),
            'obj' => $ptmsg
        ];
    }


    /*
     * 生成消息体
     */
    public function createMsgBody($title = '', $body, $to_url = '', $remark = MsgpushType::RM_CUSTOM,
                                  $towho = MsgpushType::TO_PER,
                                  $msgtype = MsgpushType::TYPE_COMMON)
    {
        if(!$body || !array_key_exists($remark, MsgpushType::getRemark()) ||
            !array_key_exists($towho, MsgpushType::getTowho()) ||
            !array_key_exists($msgtype, MsgpushType::getType())) {
            return false;
        }
        $msg = [
            'title' => $title,
            'body' => $body,
            'towho' => $towho,
            'to_url' => $to_url,
            'remark' => $remark,
            'msg_type' => $msgtype
        ];
        return $msg;
    }


    /**
     * 检查是否审核模式
     */
    public function checkIsCheck($user)
    {
        $iosCheckConf = \Cake\Core\Configure::read('ios_check_conf');
        //游客的话根据运营需求
        if(!$user) {
            return $iosCheckConf['f2check_mode'];
        }
        //登录账户
        $res = $iosCheckConf['check_mode'] &&
                !$user->is_normal &&
                (in_array($user->id, $iosCheckConf['special_user']) || ($user->id >= $iosCheckConf['new_user_id']));
        return $res;
    }


    /**
     * 检查是否购买查看形象视频零售产品
     */
    public function checkRetailOrder($buyer_id = null, $user_id = null, $type = null)
    {
        if(!$type || !$buyer_id || !$user_id) return false;
        $retailTb = TableRegistry::get('RetailOrder');
        $count = $retailTb->find()->where(['type' => $type, 'buyer_id' => $buyer_id, 'user_id' => $user_id])->first();
        if($count) {
            return true;
        }
        return false;
    }


    /**
     * 获取配置
     * @param $settingType
     * @return mixed
     */
    public function getSettingParam($settingType) {
        $setting = \Cake\Cache\Cache::read(\Sysetting::getStr($settingType));
        if(!$setting) {
            $setTb = TableRegistry::get("Setting");
            $setting = $setTb->find()->where(['type' => $settingType])->first();
            \Cake\Cache\Cache::write(\Sysetting::getStr($settingType), $setting);
        }
        return $setting;
    }


    /**
     * 生成返回对象
     * @param $resnum 剩余聊天次数
     * @param $action 1放行,不弹窗 2发起访问 3跳转页面 4不放行，弹窗，不发起访问，不跳转页面 5吐司提示，不发起访问，不跳转页面
     * @param $url 链接，全地址
     * @param $title
     * @param $body
     * @param string $lbt
     * @param string $rbt
     * @return array
     */
    private function createTalkResMsg($resnum = 0, $action = 4, $title = '非法操作', $body = '非法操作!', $url = '', $rbt = '确定', $lbt = '取消')
    {
        return [
            'resnum' => $resnum,
            'action' => $action,
            'url' => $url,
            'msg' => [
                'title' => $title,
                'body' => $body,
                'lbt' => $lbt,
                'rbt' => $rbt,
            ],
        ];
    }


    /**
     * 检查是否可以聊天
     * @param {int} user1_id 登录者id
     * @param {int} user2_id 聊天者id
     * @return {
     *      resnum: 剩余聊天次数
     *      action: 1放行,不弹窗 2发起访问 3跳转页面 4不放行，弹窗，不发起访问，不跳转页面 5吐司提示，不发起访问，不跳转页面
     *      url: 链接，全地址
     *      msg: {
     *          title: 弹窗标题,
     *          body: 弹窗内容,
     *          lbt: 左键文字
     *          rbt: 右键文字
     *      }
     * }
     * 附加说明：
     * 对于return内容中action=2的情况，
     * 发起访问后如果返回的结果status为true则通过本次验证，false的话弹出msg内容并不进行其他操作
     */
    public function checkTalk($loginerid, $chatterid)
    {
        $setting = $this->getSettingParam(\Sysetting::CHAT_SETTING);
        $chat_people = 10;  //限制只能免费与xx个用户聊天
        $chat_person = 10;  //限制与每个用户只能聊xx句话
        if($setting && @unserialize($setting->content)) {
            $settingContent = @unserialize($setting->content);
            $chat_people = intval(isset($settingContent['people_num'])?$settingContent['people_num']:10);
            $chat_person = intval(isset($settingContent['person_chat'])?$settingContent['person_chat']:10);
        }
        $uTb = TableRegistry::get('User');
        try {
            $chatter = $uTb->get($chatterid);  //聊天者
            $loginer = $uTb->get($loginerid);  //登录者
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->createTalkResMsg(0, 4, '', '对方已被封禁，请勿再联系');
        }

        /*if($this->checkUserNdata($loginer, self::UDATA_NES)) {
            return $this->createTalkResMsg(
                0,
                3,   //跳转页面
                '',
                '约见吧圈希望给大家提供一个真诚可靠的社交平台，请您先完善资料和形象照片，才能和对方聊天哦~',
                transformUrl('/userc/edit-info'),
                '立即完善');
        };*/

        //待审核、审核信息未上传成功、未审核通过的美女，都不能发起聊天，点击‘聊天’按钮，吐司提示
        /*if($loginer->gender == 2) {
            if($loginer->status == \UserStatus::SHARE_PASS) {
                return $this->createTalkResMsg(0, 5, '', '您当前为约见吧经纪人，暂无此权限');
            } else if($loginer->status != \UserStatus::PASS) {
                return $this->createTalkResMsg(0, 5, '', '您暂无此权限，请先通过认证信息的审核');
            }

        }*/

        //审核不通过的用户不能聊天
        if($loginer->status == UserStatus::NOPASS) {
            return $this->createTalkResMsg(0, 5, '', '您暂无此权限');
        }

        $today = new Time();
        $todayStart = new Time($today->year.'-'.$today->month.'-'.$today->day);
        $todayEnd = new Time($today->year.'-'.$today->month.'-'.$today->day.' 23:59:59');
        $convTb = TableRegistry::get('Conversation');

        //限定每个用户只能和同一个用户每天免费聊xx句话
        $resnum = $chat_person;
        $hasChat = $convTb->find()->where([
            'fromAccount' => $loginer->imaccid,
            'toAccount' => $chatter->imaccid,
            'msgTimestamp >=' => $todayStart->timestamp
        ])->andWhere(['msgTimestamp <=' => $todayEnd->timestamp])->count();
        //初始化聊天剩余句数
        if($hasChat >= $resnum) {
            $resnum = 0;
        } else {
            $resnum -= $hasChat;
        }
        //限定每个用户每天只能免费和xx个用户聊天
        $respeople = $chat_people;
        $hasChatnum = $convTb->find()->distinct('toAccount')->where([
            'fromAccount' => $loginer->imaccid,
            'msgTimestamp >=' => $todayStart->timestamp
        ])->andWhere(['msgTimestamp <=' => $todayEnd->timestamp])->count();
        //初始化聊天剩余人数
        if($hasChatnum > $respeople) {
            $respeople = 0;
        } else {
            $respeople -= $hasChatnum;
        }

        if(!$respeople) {
            if($hasChat) {
                //女性只限制聊天人数不限制聊天句数
                /*if($loginer->gender == 2) {
                    $resnum = 99999;
                }*/
            } else {
                //如果没有聊过天则判断今天剩余可聊天人数
                //如果今天剩余可聊天人数用完了，则本次没有可以聊天次数了
                $resnum = 0;
            }
        } else {
            //女性只限制聊天人数不限制聊天句数
            /*if($loginer->gender == 2) {
                $resnum = 99999;
            }*/
        }

        //全面开放女生聊天权限
        if($loginer->gender == 2) {
            $resnum = 99999;
        }

        //查询聊天权限
        $right = 0;
        $toUrl = '';
        $type = ServiceType::CHAT;
        if($loginer->gender == 1) {
            //男的需要查看自己有没有权限
            if(ServiceType::containType($type)) {
                $right = $this->checkRight($loginerid, $chatterid, $type);
            }
        } else {
            //女的需要查看对方有没有权限
            if(ServiceType::containType($type)) {
                $right = $this->checkRight($chatterid, $loginerid, $type);
            }
        }


        //返回内容
        $iosCheckConf = \Cake\Core\Configure::read('ios_check_conf');
        if($loginer->gender == 1) {
            //返回的提示语可根据以下几种情况：
            // 1)与当前聊天对象当日没有聊过天且聊天人数用完的提示语
            // 2)与当前聊天对象当日没有聊过天但聊天人数没有用完的提示语
            // 3)与当前聊天对象当日有聊过天且聊天人数用完的提示语
            // 4)与当前聊天对象当日有聊过天但聊天人数没有用完的提示语
            //分析：分类主要根据最终resnum为0时检查的聊天对象是否有和我聊过天，则可以分为1和2/3/4两种情况
            switch ($right) {
                case SerRight::OK_CONSUMED:
                    return $this->createTalkResMsg($resnum, 1);
                    break;
                case SerRight::NO_HAVENUM:
                    return $this->createTalkResMsg(
                        $resnum,
                        2,   //访问链接
                        '和她聊天',
                        '将会消耗一个聊天名额',
                        getHost().'/api/consumePackage?user_id='.$loginer->id.'&view_id='.$chatter->id.'&type='.ServiceType::CHAT,
                        '继续聊天');
                    break;
                case SerRight::NO_HAVENONUM:
                    if(!$hasChat&&!$respeople) {
                        return $this->createTalkResMsg(
                            $resnum,
                            3,   //跳转页面
                            '充值会员',
                            '才能和人家聊天哦',
                            getHost().'/userc/vip-buy?reurl=/index/homepage/'.$chatter->id,
                            '确定');
                    } else {
                        return $this->createTalkResMsg(
                            $resnum,
                            3,   //跳转页面
                            '充值会员',
                            '才能和人家继续聊天哦',
                            getHost().'/userc/vip-buy?reurl=/index/homepage/'.$chatter->id,
                            '确定');
                    }
                    break;
                case SerRight::NO_HAVEPOINT:
                    return $this->createTalkResMsg(
                        $resnum,
                        4,
                        '和她聊天',
                        '和她聊天将会消耗'.$iosCheckConf['chat_point'].'积分',
                        getHost().'/api/consumePackage?user_id='.$loginer->id.'&view_id='.$chatter->id.'&type='.ServiceType::CHAT,
                        '继续聊天');
                    break;
                case SerRight::NO_HAVENOPOINT:
                    return $this->createTalkResMsg(
                        $resnum,
                        4,
                        '和她聊天',
                        '积分不足~');
                    break;
                default :
                    return $this->createTalkResMsg(0, 4);
                    break;
            }
        } else {
            if($right != SerRight::OK_CONSUMED) {
                return $this->createTalkResMsg($resnum, 5, '', '阿哦，您今天主动聊天机会已用完，明天再来吧~');
            } else {
                return $this->createTalkResMsg($resnum, 1);
            }
        }
    }

    /**
     * 检查用户资料资料是否完善
     */
    const UDATA_ALL = 1;   //所有可填资料
    const UDATA_NES = 2;   //基本信息
    const UDATA_HOBBY = 3; //基本信息 + 兴趣爱好
    public function checkUserNdata($user = null, $type = self::UDATA_ALL)
    {
        $percent = 0;
        if(!$user) {
            return $percent;
        }
        $countBasicPic = false;
        switch ($type) {
            case self::UDATA_NES :
                //女性资料
                $femalelist = Array('nick', 'truename', 'phone', 'birthday', 'profession',
                    'weight', 'height', 'cup', 'zodiac', 'state', 'hometown', 'city', 'avatar');
                //男性资料
                $malelist = Array('phone', 'nick', 'truename', 'profession', 'birthday', 'zodiac',
                    'weight', 'height', 'hometown', 'city', 'avatar', 'state');
                $man_total = count($malelist) + 1;
                $women_total = count($femalelist) + 1;
                $countBasicPic = true;
                break;
            case self::UDATA_HOBBY :
                $femalelist = Array('nick', 'truename', 'phone', 'birthday', 'profession',
                    'weight', 'height', 'cup', 'zodiac', 'state', 'hometown', 'city', 'avatar', 'place', 'food', 'music', 'movie', 'sport', 'sign');
                //男性资料
                $malelist = Array('phone', 'nick', 'truename', 'profession', 'birthday', 'zodiac',
                    'weight', 'height', 'hometown', 'city', 'avatar', 'state', 'place', 'food', 'music', 'movie', 'sport', 'sign');
                $man_total = count($malelist);
                $women_total = count($femalelist);
                break;
            default :
                //女性资料
                $femalelist = Array('phone', 'nick', 'truename', 'profession', 'email',
                    'gender', 'birthday', 'zodiac', 'weight', 'height', 'cup',
                    'hometown', 'city', 'avatar', 'state', 'career', 'place', 'food',
                    'music', 'movie', 'sport', 'sign', 'wxid', 'idpath', 'idfront', 'idback',
                    'idperson', 'images', 'video', 'video_cover', 'auth_video');
                //男性资料
                $malelist = Array('phone', 'nick', 'truename', 'profession', 'email',
                    'gender', 'birthday', 'zodiac', 'weight', 'height',
                    'hometown', 'city', 'avatar', 'state', 'career', 'place', 'food',
                    'music', 'movie', 'sport', 'sign', 'idpath', 'idfront', 'idback',
                    'idperson', 'auth_video');
                $man_total = count($malelist) + 1;
                $women_total = count($femalelist) + 1;
                $countBasicPic = true;
                break;
        }

        //基本图片和视频
        if($countBasicPic) {
            $moveTb = TableRegistry::get("Movement");
            $move = $moveTb->find()->where(['user_id' => $user->id, 'status' => CheckStatus::CHECKED, 'type IN' => [MovementType::BASIC_PIC, MovementType::BASIC_VID]])->first();
            if($move) {
                $percent ++;
            }
        }

        if($user->gender == 1) {
            foreach ($malelist as $item) {
                if ($user->$item) {
                    $percent++;
                }
            }
            $percent = round($percent / $man_total * 100);
        } else {
            foreach ($femalelist as $item) {
                if ($user->$item) {
                    $percent++;
                }
            }
            $percent = round($percent / $women_total * 100);
        }
        return $percent;
    }


    const AlERT_TOAST = 1;  //吐司提示
    const ALERT_CONFIRM = 2; //提示框提示
    /**
     * 用户在进行某些操作的时候需要一些资料或权限，使用该函数检查
     */
    public function checkUserInfo(User $user)
    {
        $rbtn = '';
        $lbtn = '';
        $msg = '';
        $redirect = '';
        $status = false;
        $showType = self::AlERT_TOAST;
        switch ($user->status){
            //$msg = '您暂无此权限，认证信息未上传成功。';
            //检查资料填写情况
            case UserStatus::NONEED:
            case UserStatus::PASS:
            case UserStatus::CHECKING:
                if(100 == $this->checkUserNdata($user, self::UDATA_NES)) {
                    if($user->auth_video && ($user->auth_status == 3 || $user->auth_status == 0)) {
                        $status = true;
                    } else if($user->auth_video && ($user->auth_status == UserStatus::CHECKING)){
                        $msg = '您暂无此权限，认证信息正在审核中';
                    }
                } else {
                    $msg = '请先完善基本信息必填项、进行真人脸部识别、上传形象照片后，再进行此操作';
                    $redirect = '/userc/edit-info';
                    $rbtn = '立即完善';
                    $showType = self::ALERT_CONFIRM;
                }
                break;
            case UserStatus::NOPASS:
                $msg = '您暂无此权限，认证信息审核不通过，请立即修改';
                $redirect = '/userc/edit-info';
                $rbtn = '立即修改';
                $showType = self::ALERT_CONFIRM;
                break;
            case UserStatus::SHARE_PASS:
                $msg = '您暂无此权限';
                break;
        }

        /*if($user->status == UserStatus::PASS){
            $status = true;
        }else{
            switch ($user->status){
                //$msg = '您暂无此权限，认证信息未上传成功。';
                //检查资料填写情况
                case UserStatus::NONEED:
                case UserStatus::CHECKING:
                    if(100 == $this->checkUserNdata($user, self::UDATA_NES)) {
                        if($user->auth_video && ($user->auth_status == 3 || $user->auth_status == 0)) {
                            $status = true;
                        } else if($user->auth_video && ($user->auth_status == UserStatus::CHECKING)){
                            $msg = '您暂无此权限，认证信息正在审核中';
                        }
                    } else {
                        $msg = '请您先完善个人信息中的兴趣爱好，以让对方更了解你，提高约会成功率~';
                        $redirect = '/userc/edit-info';
                        $rbtn = '立即完善';
                        $showType = self::ALERT_CONFIRM;
                    }
                    break;
                case UserStatus::NOPASS:
                    $msg = '您暂无此权限，认证信息审核不通过，请立即修改';
                    $redirect = '/userc/edit-info';
                    $rbtn = '立即修改';
                    $showType = self::ALERT_CONFIRM;
                    break;
                case UserStatus::SHARE_PASS:
                    $msg = '您暂无此权限';
                    break;
            }
        }*/
        return [
            'status' => $status,
            'msg' => $msg,
            'rbtn' => $rbtn,
            'lbtn' => $lbtn,
            'redirect' => $redirect,
            'showType' => $showType
        ];
    }


    public function generateAvatarUrl($url, $glide = true, $conf = [])
    {
        $url = trim($url) ? $url : '/mobile/images/m_avatar_2.png';
        return generateImgUrl($url, $glide, $conf, null, null);
    }


    /**
     * 生成支付单号（不同情况不一样的格式，第三方支付需求）
     * @param array $params
     * @return string
     */
    public function createPayorderNo($params = [])
    {
        $orderNo = '';
        switch (intval(isset($params['ptype']) ? $params['ptype'] : 0)) {
            default :
                if(isset($params['uid'])) {
                    $orderNo = time() . $params['uid'] . createRandomCode(4, 1);
                } else {
                    $orderNo = time() . 0000 . createRandomCode(4, 1);
                }
                break;
        }
        return $orderNo;
    }


    /**
     * 添加后台消息
     * @param $type
     * @param null $user
     * @param string $url
     * @param string $notice
     * @return bool|\Cake\Datasource\EntityInterface|mixed
     */
    public function addAdminNotice($type, $user = null, $url = '', $notice = '')
    {
        $adminNoticeTb = TableRegistry::get("AdminNotice");
        $adminNotice = $adminNoticeTb->newEntity([
            'user_name' => $user ? $user->nick : 0,
            'user_id' => $user ? $user->id : '',
            'notice' => $notice ? $notice : GlobalCode::getAdminNotice($type),
            'url' => $url,
            'status' => 1
        ]);
        return $adminNoticeTb->save($adminNotice);
    }
}
