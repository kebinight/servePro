<?php

namespace App\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Dateorder shell command.
 */
class DateorderShell extends Shell {

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser() {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main() {
        $this->out($this->OptionParser->help());
    }
    
    
      
    /**
     * 订单的自动检测
     */
    public function check(){
        //\Cake\Log\Log::info('进入自动检测订单任务','cron');                        
        set_time_limit(0);
        $Daterorder = \Cake\ORM\TableRegistry::get('Dateorder');
        $orders = $Daterorder->find()
                            ->contain([
                                'Buyer' => function($q) {
                                    return $q->select(['phone', 'id', 'money']);
                                }, 
                                'Dater' => function($q) {
                                    return $q->select(['id', 'nick', 'money']);
                                }, 
                                'UserSkill.Skill'
                            ])
                            ->where(['Dateorder.status in'=>['1','3','7','10']])
                            ->toArray();
        $counts = count($orders);
        //\Cake\Log\Log::info($counts.'条订单进入时间处理','cron');                        
        foreach ($orders as $key => $order) {
            switch ($order->status) {
                case 1:
                    //男方未支付预约金
                    $this->handStatus1($order);
                    break;
                case 3:
                    //男方支付完预约金
                    $this->handStatus3($order);
                    break;
                case 7:
                    $this->handStatus7($order);
                    break;
                case 10:
                    $this->handStatus10($order);
                    break;
                default:
                    break;
            }
        }
    }
    
   protected function handStatus1(\App\Model\Entity\Dateorder $order){
        $DateorderTable = \Cake\ORM\TableRegistry::get('Dateorder');
        if ((time()-strtotime($order->create_time)) >= 30 * 60 ) {
                    //超过30分钟未支付  订单自动关闭
            \Cake\Log\Log::info('约单:'.$order->id.'超过30分钟未支付预约金,正在被自动处理','cron');
            $order->status = 2;
            $order->close_time = date('Y-m-d H:i:s');
            $res = $DateorderTable->save($order);
            if($res){
                dblog('dateorder','1条约单被执行状态1操作','dateorder_id:'.$order->id);
            }else{
                dblog('dateorder','1条订单执行状态1操作失败','dateorder_id:'.$order->id);
            }
        }
   }

        /**
     * 处理状态3  预约金退回给用户账户中
     */
    protected function handStatus3(\App\Model\Entity\Dateorder $order){
        $DateorderTable = \Cake\ORM\TableRegistry::get('Dateorder');
        $FlowTable = \Cake\ORM\TableRegistry::get('Flow');
        if ((time()-strtotime($order->prepay_time)) >= 30 * 60 && 
                (time()-strtotime($order->prepay_time)) < 60 * 60) {
                    //超过30分钟  人工干预
            //\Cake\Log\Log::info('约单:'.$order->id.'超过30分钟无响应,正在被自动处理','cron');
        }
        
        if ((time()-strtotime($order->prepay_time)) > 60 * 60) {
            \Cake\Log\Log::info('约单:'.$order->id.'超过60分钟无响应,正在被自动处理','cron');
            //超过60分钟  自动退单 
            //1.订单状态改变
            //2.退回预约金
            $order->status = 6;
            $order->close_time = date('Y-m-d H:i:s');
            $m_pre_money = $order->buyer->money;
            $m_amount = $order->pre_pay;
            $order->buyer->money = $order->buyer->money+$order->pre_pay;
            $m_after_amount = $order->buyer->money;
            $m_income = 1;
            
            //生成流水
            $m_flow = $FlowTable->newEntity([
               'user_id'=> $order->buyer->id,
               'buyer_id'=>  0,
               'relate_id'=>$order->id,
               'oid'=>$order->oid,
               'type'=>10,
               'type_msg'=>'自动退单退款',
               'income'=>$m_income,
               'amount'=>$m_amount,
               'price'=>$m_amount,
               'pre_amount'=>$m_pre_money,
               'after_amount'=>$m_after_amount,
               'paytype'=>2, 
               'remark'=> '支付完预约金后60分钟无响应,自动退单'
            ]);
            
            $order->dirty('buyer',true);
            $transRes = $DateorderTable->connection()
                    ->transactional(function()use($FlowTable,&$m_flow,&$order,$DateorderTable){
                return $FlowTable->save($m_flow)&&$DateorderTable->save($order);      
            });
            if($transRes){
                dblog('dateorder','1条约单被执行状态3操作','dateorder_id:'.$order->id);
            }else{
                dblog('dateorder','1条订单执行状态3操作失败','dateorder_id:'.$order->id);
            }
        }
    }
    /**
     * 处理状态7  预约金打到美女账户中
     */
    protected function handStatus7(\App\Model\Entity\Dateorder $order){
        $DateorderTable = \Cake\ORM\TableRegistry::get('Dateorder');
        $FlowTable = \Cake\ORM\TableRegistry::get('Flow');
        
        if ((time()-strtotime($order->receive_time)) >= 6*60 * 60) {
            //超过6小时 未支付尾款  预约金自动打到美女账户中 
            //1.订单状态改变
            //2.女方获得预约金
            $order->status = 9;   //消费者超时未付尾款
            $order->close_time = date('Y-m-d H:i:s');
            $w_pre_money = $order->dater->money;
            $w_amount = $order->pre_pay;
            $order->dater->money = $order->dater->money+$w_amount;
            $w_after_amount = $order->dater->money;
            $w_income = 1;
            
            //生成流水
            $w_flow = $FlowTable->newEntity([
               'user_id'=> $order->dater->id,
               'buyer_id'=>  0,
               'relate_id'=>$order->id,
               'oid'=>$order->oid,
               'type'=>11,
               'type_msg'=>'违约预约金',
               'income'=>$w_income,
               'amount'=>$w_amount,
               'price'=>$w_amount,
               'pre_amount'=>$w_pre_money,
               'after_amount'=>$w_after_amount,
               'paytype'=>2, 
               'remark'=> '接受约单后6小时未支付尾款'
            ]);
            
            $order->dirty('dater',true);
            $transRes = $DateorderTable->connection()
                    ->transactional(function()use($FlowTable,&$w_flow,&$order,$DateorderTable){
                return $FlowTable->save($w_flow)&&$DateorderTable->save($order);      
            });
            if($transRes){
                $this->shareIncome($w_amount, $order->dater,$order->id);
                dblog('dateorder','1条约单被执行状态7操作','dateorder_id:'.$order->id);
            }else{
                dblog('dateorder','1条订单执行状态7操作失败','dateorder_id:'.$order->id);
            }
        }
    }
    /**
     * 处理状态10  约单总金给美女  订单完成
     */
    protected function handStatus10(\App\Model\Entity\Dateorder $order){
        $DateorderTable = \Cake\ORM\TableRegistry::get('Dateorder');
        $FlowTable = \Cake\ORM\TableRegistry::get('Flow');
        
        if ((time()-strtotime($order->end_time)) >= 24*60 * 60) {
            //超过24小时 双方无操作 约单金额自动打到美女账户中 
            //1.订单状态改变
            //2.女方获得约单金
            $order->status = 15;   //订单完成 
            $order->close_time = date('Y-m-d H:i:s');
            $w_pre_money = $order->dater->money;
            $w_amount = $order->amount;
            $order->dater->money = $order->dater->money+$w_amount;
            $w_after_amount = $order->dater->money;
            $w_income = 1;
            
            //生成流水  女性 收入 约单金额
            $w_flow = $FlowTable->newEntity([
               'user_id'=> $order->dater->id,
               'buyer_id'=>  0,
               'relate_id'=>$order->id,
               'oid'=>$order->oid,
               'type'=>12,
               'type_msg'=>'约技能收款',
               'income'=>$w_income,
               'amount'=>$w_amount,
               'price'=>$w_amount,
               'pre_amount'=>$w_pre_money,
               'after_amount'=>$w_after_amount,
               'paytype'=>2, 
               'remark'=> '24小时无操作订单自动完成'
            ]);
            
            $order->dirty('dater',true);
            $transRes = $DateorderTable->connection()
                    ->transactional(function()use($FlowTable,&$w_flow,&$order,$DateorderTable){
                return $FlowTable->save($w_flow)&&$DateorderTable->save($order);      
            });
            if($transRes){
                $this->shareIncome($w_amount, $order->dater,$order->id);
                dblog('dateorder','1条约单被执行状态10操作','dateorder_id:'.$order->id);
            }else{
                dblog('dateorder','1条订单执行状态10操作失败','dateorder_id:'.$order->id);
            }
        }
    }
    
    
    /**
     * 创建分成收入
     * @param $amount 收入/充值
     * @param $invited 被邀请者
     */
    protected function shareIncome($amount, $invited,$relate_id = 0)
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
                $type = 19;  //好友充值
            } else {
                $admoney = $amount * $sr_percent;
                $type = 20;  //好友获得收入
            }
            $preAmount = $invitor->money;
            $invitor->money += $admoney;
            $afterAmount = $invitor->money;
            //生成流水
            $FlowTable = TableRegistry::get('Flow');
            $flow = $FlowTable->newEntity([
                'user_id'=> $invitor->id,
                'buyer_id'=> 0,
                'type'=> $type,
                'type_msg'=> getFlowType($type),
                'relate_id'=>$relate_id,
                'income'=> 1,
                'amount'=> $admoney,
                'price'=> $admoney,
                'pre_amount'=> $preAmount,
                'after_amount'=> $afterAmount,
                'paytype'=>1,   //余额支付
                'remark'=> getFlowType($type)
            ]);

            $userTb = TableRegistry::get('User');
            $transRes = $FlowTable->connection()->transactional(
                function() use ($FlowTable, &$flow, $userTb, &$invitor){
                    $flores = $FlowTable->save($flow);
                    $ures = $userTb->save($invitor);
                    return $flores&&$ures;
                }
            );
            return $transRes;
        }
        return false;
    }
}
