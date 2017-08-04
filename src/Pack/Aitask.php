<?php

namespace App\Pack;

use App\Controller\Admin\SettingController;
use App\Controller\Component\BusinessComponent;
use App\Model\Entity\User;
use Cake\Controller\ComponentRegistry;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use GlobalCode;
use MsgpushType;
use PackType;

class Aitask {

    public $redis = null;  //redis实例
    protected $userTb = null; //mysql数据库用户表实例
    protected $execAis = [];  //一次干扰任务中的所有ai;

    //redis上的key
    const AI_TASK_HASH_KEY_PREFIX = 'momoai_ai_task_hash_';  //机器人任务池
    const USER_HASH_KEY_PREFIX = 'momoai_user_pool_hash_';  //用户列表池
    const MAN_ID_POOL_SET = 'momoai_normal_man_id_pool';  //男性id池
    const WOMEN_ID_POOL_SET = 'momoai_normal_women_id_pool';  //女性id池
    const AI_WOMEN_ID_POOL_SET = 'momoai_ai_women_id_pool';  //女性机器人id池

    //干扰操作
    const ACTION_VISIT = 1; //访问主页
    const ACTION_CHAT = 2;  //聊天
    const ACTION_PRAISE = 3;  //点赞

    //干扰类型
    const TYPE_COMMON = 1; //非会员干扰
    const TYPE_BUYPACK = 2; //购买会员干扰

    //以下为机器人规则相关配置
    const NUM_4_MAN_TYPE_COMMON = 5; //男性非会员干扰轮数
    const NUM_4_WOMEN_TYPE_COMMON = 1; //女性非会员干扰轮数

    public function __construct()
    {
        $this->userTb = TableRegistry::get('User');
        $RedisConf = \Cake\Core\Configure::read('Redis.default');
        $this->redis = new \Redis();
        $connRes = $this->redis->connect($RedisConf['host'], $RedisConf['port']);
        if(!$connRes) {
            tmpLog('Redis 连接失败');
            return false;
        }
        //初始化用户id池
        $this->initManIdPool();
        $this->initWomenIdPool();
        $this->initAiWomenIdPool();
    }

    private function initManIdPool()
    {
        if(!$this->redis->exists(self::MAN_ID_POOL_SET) || !$this->redis->sCard(self::MAN_ID_POOL_SET)) {
            $users = $this->userTb->find()->select(['id', 'imaccid', 'gender'])
                ->where(['gender' => 1, 'is_normal' => 1, 'is_del' => 0, 'enabled' => 1])
                ->where(['id IN' => [382, 383, 384]])
                ->toArray();
            tmpLog('初始化男性账号池|' . count($users));
            foreach($users as $user) {
                $str = $user->id . ':' . $user->imaccid . ':' . $user->gender;
                $this->redis->sAdd(self::MAN_ID_POOL_SET, $str);
            }
        }
    }


    private function initWomenIdPool()
    {
        if(!$this->redis->exists(self::WOMEN_ID_POOL_SET) || !$this->redis->sCard(self::WOMEN_ID_POOL_SET)) {
            $users = $this->userTb->find()->select(['id', 'imaccid', 'gender'])
                ->where(['gender' => 2, 'is_normal' => 1, 'status' => \UserStatus::PASS, 'is_del' => 0, 'enabled' => 1])
                ->where(['id IN' => [379, 380, 386]])
                ->toArray();
            tmpLog('初始化女性账号池|' . count($users));
            foreach($users as $user) {
                $str = $user->id . ':' . $user->imaccid . ':' . $user->gender;
                $this->redis->sAdd(self::WOMEN_ID_POOL_SET, $str);
            }
        }
    }


    private function initAiWomenIdPool()
    {
        if(!$this->redis->exists(self::AI_WOMEN_ID_POOL_SET) || !$this->redis->sCard(self::AI_WOMEN_ID_POOL_SET)) {
            $users = $this->userTb->find()->select(['id', 'imaccid', 'gender'])
                ->where(['gender' => 2, 'is_normal' => 0, 'status' => \UserStatus::PASS, 'is_del' => 0, 'enabled' => 1])
                ->toArray();
            tmpLog('初始化女性机器人账号池|' . count($users));
            foreach($users as $user) {
                $str = $user->id . ':' . $user->imaccid . ':' . $user->gender;
                $this->redis->sAdd(self::AI_WOMEN_ID_POOL_SET, $str);
            }
        }
    }


    /**
     * 记录用户打招呼，用于过滤重复打招呼行为
     * 此方法用于特殊需求：两者已经聊过天的以后不进行打招呼、机器人干扰
     * @param {User} user 被打招呼者
     * @param {User} sayer 打招呼者
     */
    public function setUserImLog($userId, $sayerId)
    {
        if($userId && $sayerId) {
            (!$this->redis->sIsMember($userId, $sayerId)) ? $this->redis->sAdd($userId, $sayerId) : false;
            (!$this->redis->sIsMember($sayerId, $userId)) ? $this->redis->sAdd($sayerId, $userId) : false;
            return true;
        }
        return false;
    }


    /**
     * 检查是否有过对话
     * 此方法用于特殊需求：检查双方是否聊过天
     */
    public function checkUserImLog($userId, $sayerId)
    {
        if($this->redis->sIsMember($userId, $sayerId) || $this->redis->sIsMember($sayerId, $userId)) {
            return true;
        }
        return false;
    }


    /**
     * 添加用户信息到对应池中
     * @param User $user
     */
    public function updateUserIdPool(User $user)
    {
        $str = $user->id . ':' . $user->imaccid . ':' . $user->gender;
        $skey = null;
        if($user->gender == 1) {
            $skey = self::MAN_ID_POOL_SET;
        } else {
            if($user->status == \UserStatus::PASS && $user->is_normal == 1) {
                $skey = self::WOMEN_ID_POOL_SET;
            } else if($user->status == \UserStatus::PASS && $user->is_normal == 0) {
                $skey = self::AI_WOMEN_ID_POOL_SET;
            }
        }
        if($skey) {
            $this->redis->sAdd($skey, $str);
        }
    }


    /**
     * 更新reids中对应用户信息
     * @param array $user
     */
    public function updateUser(User $user, $newInfo = [])
    {
        if(!$newInfo['packDeadline']) {
            tmpLog('更新redis用户信息|失败|原因：packDeadline为空');
            return false;
        }
        $redisUser = $this->getRedisUserEntity($user);
        $redisUser['pack_deadline'] = $newInfo['packDeadline'];
        $this->saveRedisUserEntity($redisUser);
    }


    /**
     * 添加骚扰任务
     * 由于应用为静默登录，所以现在ai功能定为在每次请求的时候都去尝试添加任务
     * 此方法在每次请求的时候都会被调用，所以需要进行一些过滤
     * 用于添加非会员ai干扰任务。
     * @param $user
     * @param int $type
     * @return bool
     */
    public function addTask(User $user)
    {
        if(!$user) {
            return false;
        }
        $userinfo = $this->getRedisUserEntity($user);
        if(time() > $userinfo['pack_deadline']) {
            //非会员用户添加干扰任务
            $this->addCommonTask($user);
        } else {
            //会员用户添加干扰任务
            $this->addBuyPackTask($user);
        }
    }


    //获取用户会员卡失效时间
    private function getPackDeadline($userid)
    {
        $type = [PackType::VIP, PackType::LINSHI, PackType::RECHARGE];
        $upackTb = TableRegistry::get('UserPackage');
        $validpack = $upackTb->find()->where([
            'user_id' => $userid,
            'type IN' => $type,
            'deadline >' => new Time()
        ])->first();
        if($validpack) {
            return $validpack->deadline->timestamp;
        } else {
            return 0;
        }
    }


    /**
     * 添加非会员干扰任务
     *
     * 添加非会员登录后干扰任务
     * 存在三种情况：
     * 1) 已经存在同类型干扰任务，本次添加无效
     * 2) 已经存在不同类型干扰任务，本次添加无效
     * 3) 不存在干扰任务，依据情况添加机器人干扰任务
     * 添加机器人持续干扰任务,规则如下：
     * 1)随机选择以下干扰时间：1.2分钟内干扰1次；2.2~6分钟内干扰1次；3.6~20分钟内干扰2次
     * 2)随机选择干扰操作：1.访问 2.聊天 3.访问+聊天
     * 3)每次干扰机器人数为1
     * 4)总共骚扰轮数：5轮，24小时内仅进行1轮干扰。
     */
    public function addCommonTask($user)
    {
        $aitask = $this->getAiTaskEntity($user->id);
        if($aitask) {
            tmpLog('机器人干扰任务添加|非会员干扰|失败|已存在任务');
            return false;
        }

        //根据用户受干扰历史
        $redisUser = $this->getRedisUserEntity($user);
        if($redisUser) {
            if($redisUser['gender'] == 1 && self::NUM_4_MAN_TYPE_COMMON <= $redisUser['visit_num']) {
                tmpLog('机器人干扰任务添加|非会员干扰|失败|已进行过'.$redisUser['visit_num'].'轮干扰');
                return false;
            } elseif($redisUser['gender'] == 2 && self::NUM_4_WOMEN_TYPE_COMMON <= $redisUser['visit_num']) {
                tmpLog('机器人干扰任务添加|非会员干扰|失败|已进行过'.$redisUser['visit_num'].'轮干扰');
                return false;
            } elseif((time() - $redisUser['last_visit']) < 86400) {
                tmpLog('机器人干扰任务添加|非会员干扰|失败|24小时内已进行过干扰');
                return false;
            }
        }

        //$visTimeRandNum = rand(1, 3);
        $visitNum = 4;
        /*switch ($visTimeRandNum) {
            case 1 :
                //添加任务后不是马上执行，所以给定时任务预留20秒时间
                $visitTimes = rand(20, 140);
                break;
            case 2 :
                $visitTimes = rand(140, 380);
                break;
            case 3 :
                //由于在20分钟内（即1200秒）要执行两次，所以给第二次执行预留一半时间
                $visitTimes = rand(400, 600);
                break;
        }*/
        //两分钟内干扰一次
        $visitTimes = rand(20, 120);
        //测试数据
        //$visitTimes = 10;
        tmpLog('机器人干扰任务添加|非会员干扰|成功|干扰时间设定于' . $visitTimes . '秒后');
        $this->setAiTaskEntity($user->id, self::TYPE_COMMON, time(), $visitTimes, $visitNum);
    }


    /**
     * 添加购买会员后干扰任务
     *
     * 存在三种情况：
     * 1) 已经存在同类型干扰任务，本次添加无效
     * 2) 已经存在不同类型干扰任务，更换干扰任务
     * 3) 不存在干扰任务，添加干扰任务
     * 添加机器人持续干扰任务,规则如下：
     * 1)随机选择以下干扰时间：1.2分钟内干扰1次；2.2~8分钟内干扰1次；
     * 2)随机选择干扰操作：1.访问+点赞 2.聊天
     * 3)每次干扰机器人数为1~2
     * 4)总共骚扰轮数：1轮
     */
    public function addBuyPackTask($user)
    {
        //美女暂时没有该干扰类型
        if($user->gender == 2) {
            tmpLog('机器人干扰任务添加|会员干扰|失败|女性用户不存在此干扰【' . $user->id . '】');
            return false;
        }
        $aiTask = $this->getAiTaskEntity($user->id);
        if($aiTask) {
            if(self::TYPE_BUYPACK == $aiTask['type']) {
                tmpLog('机器人干扰任务添加|会员干扰|失败|已存在同类型干扰任务');
                return false;
            }
        }
        $visTimeRandNum = rand(1, 2);
        $visitNum = 2;
        $visitTimes = 10;
        /*switch ($visTimeRandNum) {
            case 1 :
                //添加任务后不是马上执行，所以给定时任务预留20秒时间
                $visitTimes = rand(20, 140);
                break;
            case 2 :
                $visitTimes = rand(140, 500);
                break;
        }*/
        $visitTimes = rand(20, 120);
        tmpLog('机器人干扰任务添加|会员干扰|成功|干扰时间设定于' . $visitTimes . '秒后');
        $this->setAiTaskEntity($user->id, self::TYPE_BUYPACK, time(), $visitTimes, $visitNum);
    }


    /**
     * 设置机器人干扰任务条目
     * @param { int } $id  用户id
     * @param { int } $type 干扰类型
     * @param { int } $timestamp 任务创建时间戳
     * @param { int } $exectime 从创建时间xx秒后执行
     * @param { int } $restnum 剩余干扰次数
     */
    public function setAiTaskEntity($id, $type, $timestamp, $exectime, $restnum)
    {
        $aiTaskKey = self::AI_TASK_HASH_KEY_PREFIX . $id;
        $this->redis->hSet($aiTaskKey, 'id', $id);
        $this->redis->hSet($aiTaskKey, 'type', $type);
        $this->redis->hSet($aiTaskKey, 'timestamp', $timestamp);
        $this->redis->hSet($aiTaskKey, 'exectime', $exectime);
        $this->redis->hSet($aiTaskKey, 'restnum', $restnum);
    }


    /**
     * 设置机器人干扰任务条目
     */
    public function getAiTaskEntity($id)
    {
        $aiTaskKey = self::AI_TASK_HASH_KEY_PREFIX . $id;
        return $this->redis->hGetAll($aiTaskKey);
    }


    /**
     * 添加或更新干扰任务信息
     * @param array $user
     * @return bool
     */
    public function saveTaskEntity($task = [])
    {
        if(!isset($task['id'])) {
            return false;
        }
        $taskHashKey = self::AI_TASK_HASH_KEY_PREFIX . $task['id'];
        if($this->redis->hExists($taskHashKey, 'id')) {
            if(isset($task['type'])) {
                $this->redis->hSet($taskHashKey, 'type', $task['type']);
            }
            if(isset($task['timestamp'])) {
                $this->redis->hSet($taskHashKey, 'timestamp', $task['timestamp']);
            }
            if(isset($task['exectime'])) {
                $this->redis->hSet($taskHashKey, 'exectime', $task['exectime']);
            }
            if(isset($task['restnum'])) {
                $this->redis->hSet($taskHashKey, 'restnum', $task['restnum']);
            }
            return true;
        } else if(isset($task['type']) && isset($task['timestamp']) && isset($task['exectime']) && isset($task['restnum'])){
            $this->setAiTaskEntity($task['id'], $task['type'], $task['timestamp'], $task['exectime'], $task['restnum']);
            return true;
        } else {
            return false;
        }
    }


    /**
     * 设置Redis中用户信息
     * @param { int } $id 用户id
     * @param { int } $visnum 已干扰轮数
     * @param { int } $packdline 会员到期时间戳
     * @param { int } $lastvis 最后一次干扰时间戳
     */
    public function setRedisUserEntity($id, $gender, $imaccid, $visnum, $lastvis, $pack_deadline)
    {
        $userHashKey = self::USER_HASH_KEY_PREFIX . $id;
        $this->redis->hSet($userHashKey, 'id', $id);
        $this->redis->hSet($userHashKey, 'gender', $gender);
        $this->redis->hSet($userHashKey, 'imaccid', $imaccid);
        $this->redis->hSet($userHashKey, 'visit_num', $visnum);  //已干扰轮数
        $this->redis->hSet($userHashKey, 'last_visit', $lastvis);  //最后一次干扰时间
        $this->redis->hSet($userHashKey, 'pack_deadline', $pack_deadline);  //会员卡过期时间
    }


    /**
     * 添加或更新redis用户数据
     * @param array $user
     * @return bool
     */
    public function saveRedisUserEntity($user = [])
    {
        if(!isset($user['id'])) {
            return false;
        }
        $userHashKey = self::USER_HASH_KEY_PREFIX . $user['id'];
        if($this->redis->hExists($userHashKey, 'id')) {
            if(isset($user['gender'])) {
                $this->redis->hSet($userHashKey, 'gender', $user['gender']);  //性别
            }
            if(isset($user['imaccid'])) {
                $this->redis->hSet($userHashKey, 'imaccid', $user['imaccid']);  //imaccid
            }
            if(isset($user['visit_num'])) {
                $this->redis->hSet($userHashKey, 'visit_num', $user['visit_num']);  //已干扰轮数
            }
            if(isset($user['pack_deadline'])) {
                $this->redis->hSet($userHashKey, 'pack_deadline', $user['pack_deadline']);  //会员卡过期时间戳
            }
            if(isset($user['last_visit'])) {
                $this->redis->hSet($userHashKey, 'last_visit', $user['last_visit']);  //最后一次干扰时间
            }
            return true;
        } else if(isset($user['gender']) && isset($user['imaccid']) && isset($user['visit_num']) && isset($user['last_visit'])){
            $this->setRedisUserEntity($user['id'], $user['gender'], $user['imaccid'], $user['visit_num'], $user['last_visit'], isset($user['pack_deadline'])?$user['pack_deadline']:0);
            return true;
        } else {
            return false;
        }
    }


    /**
     * 获取reids中用户信息
     * @param $id
     */
    public function getRedisUserEntity($user)
    {
        $userHashKey = self::USER_HASH_KEY_PREFIX . $user->id;
        //判断redis中是否存在该用户记录
        if(!$this->redis->hExists($userHashKey, 'id')) {
            //添加用户信息
            $this->setRedisUserEntity($user->id, $user->gender, $user->imaccid, 0, 0, $this->getPackDeadline($user->id));
        }
        return $this->redis->hGetAll($userHashKey);
    }


    /**
     * 执行任务
     * 如果执行时间符合要求&&剩余干扰次数>=1则执行干扰
     */
    public function execTask()
    {
        //非会员登录后干扰操作集
        $typeCommonActions = [
            [self::ACTION_VISIT],
            [self::ACTION_CHAT],
            [self::ACTION_VISIT, self::ACTION_CHAT]
        ];
        //购买会员时干扰操作集
        $typeBuyPackActions = [
            [self::ACTION_VISIT, self::ACTION_PRAISE],
            [self::ACTION_CHAT]
        ];
        $aiTaskKeys = self::AI_TASK_HASH_KEY_PREFIX . '*';
        $keys = $this->redis->keys($aiTaskKeys);
        tmpLog('执行干扰操作开始扫描：共有' . count($keys) . '条待执行任务..');
        foreach ($keys as $key) {
            $task = $this->redis->hGetAll($key);
            $redisUserKey = self::USER_HASH_KEY_PREFIX . $task['id'];
            $redisUser  = $this->redis->hGetAll($redisUserKey);
            //初始化干扰机器人数量
            $visnum = 0;
            switch ($task['type']) {
                case self::TYPE_COMMON :
                    $visnum = 1;
                    break;
                case self::TYPE_BUYPACK :
                    $visnum = rand(1, 2);
                    break;
            }
            //剩余干扰次数为0的任务直接删除
            if($task['restnum'] <= 0) {
                $this->redis->del($key);
                tmpLog('执行干扰操作开始扫描|存在干扰次数为0的任务|操作：直接删除');
            } else if(time() - intval($task['timestamp']) >= intval($task['exectime'])) {
                tmpLog('执行干扰操作执行开始---------------------------------');
                $execnum = 0;
                while($visnum > $execnum) {
                    //创建随机操作
                    $actions = [];
                    switch ($task['type']) {
                        case self::TYPE_COMMON :
                            $actionRandNum = rand(0, 2);
                            //$actionRandNum = 1;
                            $actions = $typeCommonActions[$actionRandNum];
                            break;
                        case self::TYPE_BUYPACK :
                            $actionRandNum = rand(0, 1);
                            //$actionRandNum = 1;
                            $actions = $typeBuyPackActions[$actionRandNum];
                            break;
                    }
                    $this->execAction($redisUser, $actions);
                    $execnum ++;
                }

                //更新干扰任务信息
                $task['restnum'] --;
                switch ($task['type']) {
                    case self::TYPE_COMMON :
                        if($task['restnum'] == 3) {
                            $task['exectime'] += rand(120, 240);  //6分钟内
                        } else if($task['restnum'] == 2) {
                            $task['exectime'] += rand(120, 240);  //10分钟内
                        } else if($task['restnum' == 1]) {
                            $task['exectime'] += rand(300, 600);  //20分钟内
                        }
                        break;
                    case self::TYPE_BUYPACK :
                        if($task['restnum' == 1]) {
                            $task['exectime'] += rand(180, 360);  //8分钟内
                        }
                        break;
                }
                //测试数据
                //$task['exectime'] += 10;
                $this->saveTaskEntity($task);

                //剩余干扰次数为0则清除该任务
                if($task['restnum'] <= 0) {
                    //更新用户信息
                    $redisUser['visit_num'] ++;
                    $redisUser['last_visit'] = time();
                    $this->saveRedisUserEntity($redisUser);

                    $this->redis->del($key);
                }
            }
        }
    }


    /**
     * 将redis中保存的字符串格式用户信息转成字典型数组供业务使用
     * @param $str
     * @return array
     */
    public function str2Arr4AiInfo($str)
    {
        tmpLog('str2Arr4AiInfo：' . $str);
        $infoArray = explode(':', $str);
        return [
            'id' => $infoArray[0],
            'imaccid' => $infoArray[1],
            'gender' => $infoArray[2]
        ];
    }


    /**
     * 执行操作
     */
    public function execAction($redisUser, $actions = [])
    {
        //保证每次执行干扰的机器人不是同一个
        /*$ai = null;
        do {
            if($redisUser['gender'] == 1) {
                $aiInfos = $this->redis->sRandMember(self::WOMEN_ID_POOL_SET);
            } else {
                $aiInfos = $this->redis->sRandMember(self::MAN_ID_POOL_SET);
            }
        } while($aiInfos && in_array($aiInfos, $this->execAis));

        if(!$aiInfos) {
            tmpLog('执行干扰操作|失败|redis中没有机器人可用');
            return false;
        }
        $this->execAis[] = $aiInfos;*/
        $ai = null;
        for($i = 0; $i <= 5; $i ++) {
            if($redisUser['gender'] == 1) {
                $aiInfos = $this->redis->sRandMember(self::WOMEN_ID_POOL_SET);
            } else {
                $aiInfos = $this->redis->sRandMember(self::MAN_ID_POOL_SET);
            }
            if(!$aiInfos) {
                tmpLog('执行干扰操作|失败|redis中没有机器人可用');
                return false;
            }

            $aitmp = $this->str2Arr4AiInfo($aiInfos);
            if($aitmp['id'] == $redisUser['id']) {
                continue;
            }
            if(!$this->checkUserImLog($aitmp['id'], $redisUser['id'])) {
                tmpLog('执行干扰操作|选中的机器人信息：' . json_encode($aitmp));
                $this->setUserImLog($aitmp['id'], $redisUser['id']);
                $ai = $aitmp;
                break;
            }
        }
        if(!$ai) {
            tmpLog('执行干扰操作|失败|超过限定次数筛选不出符合条件用户');
            return false;
        }

        if(in_array(self::ACTION_VISIT, $actions)) {
            $this->visit($ai, $redisUser);
        }
        if(in_array(self::ACTION_PRAISE, $actions)) {
            $this->follow($ai['id'], $redisUser['id']);
        }
        if(in_array(self::ACTION_CHAT, $actions)) {
            //不干扰男性
            if($redisUser['gender'] == 1) {
                return false;
            }
            $this->talk($ai, $redisUser);
        }
    }


    public function visit($ai, $redisUser) {
        $visitorTb = TableRegistry::get('Visitor');
        $visitor = $visitorTb->find()->where(['visitor_id' => $ai['id'], 'visited_id' => $redisUser['id']])->count();
        if($visitor) {
            return false;
        }
        $visitor = $visitorTb->newEntity([
            'visitor_id' => $ai['id'],
            'visited_id' => $redisUser['id'],
            'is_read' => 0
        ]);
        if(!$visitorTb->save($visitor)) {
            tmpLog('执行干扰操作|访问|失败|visitor_id:' . $ai['id'] . '|visited_id:' . $redisUser['id']);
        } else {
            tmpLog('执行干扰操作|访问|成功|visitor_id:' . $ai['id'] . '|visited_id:' . $redisUser['id']);
        };
    }


    /**
     * 点赞操作
     * @param $followerid
     * @param $followedid
     * @return bool
     */
    public function follow($followerid, $followedid)
    {
        if ($followerid == $followedid) {
            tmpLog('执行干扰操作|点赞|失败|不能给自己点赞|followerid:' . $followerid . '|followedid:' . $followedid);
            return false;
        }
        $follower = $this->userTb->find()->select(['id', 'gender'])->where(['id' => $followerid])->first();
        $followed = $this->userTb->find()->select(['id', 'gender'])->where(['id' => $followedid])->first();
        if (!$followed) {
            tmpLog('执行干扰操作|点赞|失败|所关注的用户不存在|followerid:' . $followerid . '|followedid:' . $followedid);
            return false;
        }
        if ($follower->gender == $followed->gender) {
            if ($follower->gender == 1) {
                tmpLog('执行干扰操作|点赞|失败|男性只可关注女性|followerid:' . $followerid . '|followedid:' . $followedid);
                return false;
            } else {
                tmpLog('执行干扰操作|点赞|失败|女性只可关注男性|followerid:' . $followerid . '|followedid:' . $followedid);
                return false;
            }
        }
        $FansTable = TableRegistry::get('UserFans');
        //判断是否关注过
        $Myfan = $FansTable->find()->where(["user_id" => $followerid, "following_id" => $followedid])->first();
        if (!$Myfan) {
            //查看是否被该用户关注过
            $hisFan = $FansTable->find()->where(["user_id" => $followedid, "following_id" => $followerid])->first();
            $newfan = $FansTable->newEntity();
            $newfan->user_id = $followerid;
            $newfan->following_id = $followedid;
            if ($hisFan) {
                //有被关注
                $hisFan->type = 2;  //关系标注为互为关注
                $newfan->type = 2;
                $transRes = $FansTable->connection()
                    ->transactional(function()use($FansTable, $hisFan, $newfan) {
                        //开启事务
                        return $FansTable->save($newfan) && $FansTable->save($hisFan);
                    });
                if (!$transRes) {
                    tmpLog('执行干扰操作|点赞|失败|关注失败|followerid:' . $followerid . '|followedid:' . $followedid);
                    return false;
                }
            } else {
                $newfan->type = 1;
                if (!$FansTable->save($newfan)) {
                    tmpLog('执行干扰操作|点赞|失败|关注失败|followerid:' . $followerid . '|followedid:' . $followedid);
                    return false;
                }
            }
            tmpLog('执行干扰操作|点赞|成功|关注成功|followerid:' . $followerid . '|followedid:' . $followedid);
            return true;
        }
        tmpLog('执行干扰操作|点赞|失败|已经关注过了|followerid:' . $followerid . '|followedid:' . $followedid);
        return false;
    }


    /**
     * 聊天
     * @param $userA
     * @param $userB
     * @return bool
     */
    public function talk($userA, $userB)
    {
        tmpLog('开始聊天：$userA:' . json_encode($userA) . '|UserB:' . json_encode($userB) );
        $msgs = [];
        $autoimTb = TableRegistry::get('Autoim');
        $autoims = $autoimTb->find()->hydrate(false)->select(['id', 'body'])->where(['type' => GlobalCode::SAY_HI_MAP, 'gender' => $userA['gender']])->toArray();
        $netim = new Netim();
        if(!$autoims) {
            tmpLog('执行干扰操作|聊天|失败|缺少聊天用语|userA:' . $userA['id'] . '|userB:' . $userB['id']);
            return false;
        }
        foreach ($autoims as $autoim) {
            $msgs[] = $netim->generateTextMsgBody($autoim['body']);
        }
        $sendMsg = $msgs[array_rand($msgs, 1)];
        $res = $netim->sendMsg($userA['imaccid'], $userB['imaccid'], $sendMsg, Netim::TEXT_MSG);
        if($res) {
            if($userA['gender'] == 1) {
                $msgBody = '系统发现1个美女和你匹配度很高吖^^,已自动帮你给她打招呼了~';
            } else {
                //女性骚扰聊天动作暂时下架
                $msgBody = '系统发现1个帅哥和你匹配度很高吖^^,已自动帮你给他打招呼了~';
            }
            $this->sendMsg(
                $userA['id'],
                '机器人干扰通知',
                $msgBody
            );
            tmpLog('执行干扰操作|聊天|成功|聊天内容：' . $sendMsg['msg'] . '|userA:' . $userA['id'] . '|userB:' . $userB['id']);
            return true;
        } else {
            tmpLog('执行干扰操作|聊天|失败|请求失败|userA:' . $userA['id'] . '|userB:' . $userB['id']);
            return false;
        }
    }


    /**
     * 发送消息
     * @return array
     */
    public function sendMsg($uid, $title, $body, $method = [MsgpushType::METHOD_IM])
    {
        $businessC = new BusinessComponent(new ComponentRegistry(null));
        $msg = $businessC->createMsgBody(
            $title,
            $body,
            '',
            \MsgpushType::AI_CHAT
        );
        return $businessC->sendSMsg($uid, $msg, $method);
    }

}