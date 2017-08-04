<?php

namespace App\Controller\Mobile;

use App\Controller\Admin\SettingController;
use App\Controller\Mobile\AppController;
use App\Model\Entity\Msgpush;
use App\Model\Entity\Setting;
use Aura\Intl\Exception;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use CheckStatus;
use GlobalCode;
use SerRight;
use ServiceType;
use UserStatus;
use Wpadmin\Utils\UploadFile;

/**
 * Api Controller  用于app接口
 *
 * @property \App\Model\Table\ApiTable $Api
 * @property \App\Controller\Component\WxComponent $Wx
 * @property \App\Controller\Component\EncryptComponent $Encrypt
 * @property \App\Controller\Component\UtilComponent $Util
 * @property \App\Controller\Component\BusinessComponent $Business
 *
 */
class ApiController extends AppController {

    const TOKEN = '64e3f4e947776b2d6a61ffbf8ad05df4';
    //配置对外开放的api接口
    protected $noAcl = [
        'upload', 'wxtoken', 'ckregister', 'recordphone', 'saveuserbasicpic',
        'saveuserbasicvideo', 'getmapusers', 'gettoken','reportuser','checkread','consumepack', 'consumepackage', 'checkright', 'checktalk',
        'getconvlist', 'getadv', 'thirdlogin'
    ];


    public function initialize() {
        parent::initialize();
        $this->autoRender = false;
    }


    public function beforeFilter(\Cake\Event\Event $event) {
        parent::beforeFilter($event);
        return $this->checkAcl();
    }


    protected function jsonResponse($status, $msg = '', $statusCode = 200) {
        $this->autoRender = false;
        $this->response->type('json');
        if (is_array($status) && !empty($status)) {
            if (!array_key_exists('code', $status)) {
                $status['code'] = 200;
            }
            $json = json_encode($status, JSON_UNESCAPED_UNICODE);
        } else {
            $json = json_encode(array('status' => $status, 'msg' => $msg, 'code' => $statusCode), JSON_UNESCAPED_UNICODE);
        }
        echo $json;
        exit();
    }


    /**
     * 接口认证
     */
    protected function checkAcl() {
        if (!$this->request->isPost()) {
            return $this->jsonResponse(false, '请求受限', 405);
        }
        if (!in_array(strtolower($this->request->param('action')), $this->noAcl)) {
            if (!$this->request->data('timestamp') || !$this->request->data('access_token')) {
                return $this->jsonResponse(false, '参数不正确', 412);
            }
            if (!$this->checkSign($this->request->data())) {
                return $this->jsonResponse(false, '验证不通过', 401);
            }
        } else {
            //请求签名认证
            return $this->baseCheckAcl();
        }
    }


    /**
     * 请求签名认证
     */
    protected function baseCheckAcl() {
        $timestamp = $this->request->data('timestamp');
        $access_token = $this->request->data('access_token');
        if (!$timestamp || !$access_token) {
            return $this->jsonResponse(false, 'api参数不正确', 412);
        }
        $timediff = time() - $timestamp;
        if ($timediff > 60 * 60*12) {
            return $this->jsonResponse(false, 'api时间参数过期', 408);
        }
        $sign = strtoupper(md5($timestamp . self::TOKEN));
        if ($sign != $access_token) {
            return $this->jsonResponse(false, 'api验证不通过', 401);
        }
    }


    /**
     *  验证签名
     *  请求中需要附带access_token
     *  access_token 生成规则：
     *      1)请求参数进行键值升序排序，排序根据ASCII排序
     *      2)将参数生成请求字符串A,不要进行转义
     *      3)在字符串A添加上约定字符串token生成字符串B
     *      4)将B进行MD5加密并转成大写
     *  @param type $params
     *  @return type
     */
    protected function checkSign($params) {
        $access_token = $params['access_token'];
        unset($params['access_token']);
        ksort($params);
        $stringA = urldecode(http_build_query($params)); //不要转义的
        $stringB = $stringA . '&key=' . self::TOKEN;
        $sign = strtoupper(md5($stringB));
        return $access_token == $sign;
    }


    /**
     * 上传接口
     * @return type
     */
    public function upload() {
        $this->autoRender = false;
        $dir = 'tmp';
        $extra_data = $this->request->data('extra_param');
        $extra_data_json = json_decode($extra_data);
        if (is_object($extra_data_json)) {
            if (isset($extra_data_json->dir)) {
                $dir = $extra_data_json->dir;
            }
        }
        $res = $this->Util->uploadFiles($dir);
        if ($res['status']) {   // 上传成功 获取上传文件信息
            $response['status'] = true;
            $response['msg'] = $res['msg'];
            $response['path'] = $res['info'][0]['path'];
            $response['urlpath'] = transformUrl($res['info'][0]['path'], true, ['w' => '150']);
        } else {    // 上传错误提示错误信息
            $response['status'] = false;
            $response['msg'] = $res['msg'];
        }
        tmpLog(json_encode($response));
        return $this->jsonResponse($response);
    }


    /**
     * 中控的access_token 分发接口
     * @return string
     */
    public function wxtoken() {
        $wxconfig = \Cake\Core\Configure::read('weixin');
        $master_ip = $wxconfig['master_ip'];
        $master_domain = $wxconfig['master_domain'];
        if ($this->request->env('SERVER_ADDR') != $master_ip ) {
            //非中控服务器请求 保证中控服务器才能进行本地的获取 
            \Cake\Log\Log::notice('非中控请求调取接口:SERVER_NAME:'.$this->request->env('SERVER_NAME'), 'devlog');
            \Cake\Log\Log::notice('非中控请求调取接口:SERVER_ADDR:'.$this->request->env('SERVER_ADDR'), 'devlog');
            return 'false';
        }
        $this->loadComponent('Wx');
        $this->loadComponent('Encrypt');
        $token = $this->Wx->getAccessToken();
        \Cake\Log\Log::debug('中控服务器获取token', 'devlog');
        \Cake\Log\Log::debug($token, 'devlog');
        $en_token = $this->Encrypt->encrypt($token);
        \Cake\Log\Log::debug($en_token, 'devlog');
        \Cake\Log\Log::debug($this->Encrypt->decrypt($en_token), 'devlog');
        $this->response->body($en_token);
        $this->response->send();
        $this->response->stop();
    }


    /**
     * 检测通讯录中的手机号是否注册
     */
    public function ckRegister() {
        $user_token = $this->request->data('user_token');
        $phones = $this->request->data('phones');
        $phones_arr = explode('|', $phones);
        //从redis中获取数据 集合
        $redis = new \Redis();
        $redis_conf = \Cake\Core\Configure::read('redis_server');
        $redis->connect($redis_conf['host'], $redis_conf['port']);
        $members = $redis->sGetMembers('phones');
        $register_phones = array_intersect($phones_arr, $members);
        $register_phones = array_values($register_phones);
        if ($register_phones) {
            $status = true;
        } else {
            $status = false;
        }
        $PhonelogTable = \Cake\ORM\TableRegistry::get('Phonelog');
        $phonelog = $PhonelogTable->find()->where(['user_token' => $user_token, 'type' => 1])->first();
        if (!$phonelog) {
            //只第一次存储
            $log = $PhonelogTable->newEntity([
                'user_token' => $user_token,
                'type' => 1,
                'phones' => $phones,
                'create_time' => date('Y-m-d H:i:s')
            ]);
            $PhonelogTable->save($log);
        }
        $this->jsonResponse([
            'status' => $status,
            'results' => [
                'phones' => $register_phones
            ]
        ]);
    }


    /**
     * 记录发送的手机号
     */
    public function recordPhone() {
        $user_token = $this->request->data('user_token');
        $phones = $this->request->data('phones');
        $PhonelogTable = \Cake\ORM\TableRegistry::get('Phonelog');
        $log = $PhonelogTable->newEntity([
            'user_token' => $user_token,
            'type' => 2,
            'phones' => $phones,
            'create_time' => date('Y-m-d H:i:s')
        ]);
        if ($PhonelogTable->save($log)) {
            $this->jsonResponse(true, 'ok');
        } else {
            $this->jsonResponse(false, 'fail');
        }
    }


    /**
     * 保存用户基本图片
     */
    public function saveUserBasicPic() {
        set_time_limit(0);
        $data = $this->request->data();
        $user_id = $this->request->data('user_id');
        $param = $this->request->data('param');
        $UserTable = \Cake\ORM\TableRegistry::get('User');
        $movementTb = TableRegistry::get('Movement');
        $user = $UserTable->get($user_id);
        if (!$user_id || !$user) {
            $this->jsonResponse(false, '身份认证失败');
        }
        $res = $this->Util->uploadFiles('user/images');
        $images = [];

        if ($res['status']) {
            $infos = $res['info'];
            foreach ($infos as $key => $info) {
                $images[] = $info['path'];
            }
            if ($param) {
                $param = json_decode($param);
                if(!$param){
                    $this->jsonResponse(false, '参数不正确');
                }
                if (property_exists($param, 'action')) {
                    if ($param->action == 'add_tracle_pic') {
                        //处理发图片动态
                        $MovementTable = \Cake\ORM\TableRegistry::get('Movement');
                        $movement = $MovementTable->newEntity([
                            'user_id' => $user_id,
                            'images' => serialize($images),
                            'body' => $param->tracle_body,
                            'type' => \MovementType::TRACLE_PIC,
                            'status' => CheckStatus::CHECKED
                        ]);
                        if ($MovementTable->save($movement)) {
                            $this->jsonResponse(true, '发布成功');
                        } else {
                            $this->jsonResponse(true, $movement->errors());
                            dblog('movement', '动态保存失败', $movement->errors());
                        }
                    }
                    if (($param->action=='add_basic_pic') || ($param->action == 'update_basic_pic')) {
                        $basicPic = $movementTb->find()
                            ->where([
                                'user_id' => $user->id,
                                'type' => \MovementType::BASIC_PIC,
                                'OR' => [['status' => CheckStatus::CHECKING], ['status' => CheckStatus::CHECKNO]]
                            ])->orderDesc('id')->first();
                        if(!$basicPic) {
                            $basicPic = $movementTb->newEntity([
                                'user_id' => $user->id,
                                'images' => serialize($images),
                                'body' => '',
                                'status' => CheckStatus::CHECKED,
                                'type' => \MovementType::BASIC_PIC
                            ]);
                        } else {
                            $basicPic->images = serialize($images);
                            $basicPic->status = CheckStatus::CHECKED;
                        }
                        if($movementTb->save($basicPic)) {
                            //发送后台通知
                            $url = '/movement/basic-index';
                            $this->Business->addAdminNotice(GlobalCode::ADMIN_NOTICE_BASIC_PIC, $this->user, $url);

                            //发送短信通知
                            /*$msg = $this->Business->createMsgBody(
                                '形象照片已提交审核',
                                '您的形象照片已提交审核，后台人员正在审核中',
                                '',
                                \MsgpushType::RM_MOVEMENT_CHECK
                            );
                            $this->Business->sendSMsg($user_id, $msg);*/
                            $this->jsonResponse(true, '形象照片已提交审核');
                        }
                    }
                }
            } else {
                $user->images = serialize($images);
                $user->status = 1;
                if ($UserTable->save($user)) {
                    $this->jsonResponse(true, '保存成功');
                } else {
                    dblog('user', '基本图片保存失败', $user->errors());
                    $this->jsonResponse(false, $user->errors());
                }
            }
            $this->jsonResponse(false, '成功调取接口');
        } else {
            $this->jsonResponse($res);
        }
    }


    /**
     * 保存用户基本视频
     */
    public function saveUserBasicVideo() {
        set_time_limit(0);
        $data = $this->request->data();
        $user_id = $this->request->data('user_id');
        $UserTable = \Cake\ORM\TableRegistry::get('User');
        $movementTb = TableRegistry::get('Movement');
        $user = $UserTable->findById($user_id)->first();
        $param = $this->request->data('param');
        if (!$user_id || !$user) {
            $this->jsonResponse(false, '身份认证失败');
        }
        $res = $this->Util->uploadFiles('user/video');
        if ($res['status']) {
            $infos = $res['info'];
            foreach ($infos as $key => $info) {
                if ($info['key'] == 'video') {
                    $data['video'] = $info['path'];
                }
                if ($info['key'] == 'cover') {
                    $data['video_cover'] = $info['path'];
                }
            }
            if ($param) {
                $param = json_decode($param);
                if (property_exists($param, 'action')) {
                    if ($param->action == 'add_tracle_video') {
                        //处理发视频动态
                        $movement = $movementTb->newEntity([
                            'user_id' => $user_id,
                            'video' => $data['video'],
                            'video_cover' => $data['video_cover'],
                            'body' => $param->tracle_body,
                            'type' => \MovementType::TRACLE_VID,
                            'status' => CheckStatus::CHECKED
                        ]);
                        if ($movementTb->save($movement)) {
                            //$this->jsonResponse(true, '动态正排队审核中');
                            $this->jsonResponse(true, '发布成功');
                        } else {
                            $this->jsonResponse(true, $movement->errors());
                            dblog('movement', '动态保存失败', $movement->errors());
                        }
                    }

                    if ($param->action == 'up_auth_video') {
                        //处理上传认证视频
                        $user->auth_video = $data['video'];
                        $user->auth_video_cover = $data['video_cover'];
                        $user->auth_status = UserStatus::CHECKING;   //待审核
                        $user->status = UserStatus::CHECKING; //用户审核状态也改为待审核
                        if ($UserTable->save($user)) {
                            //发送后台通知
                            $url = $this->user->gender == 2 ? '/user/fmview/' . $this->user->id : '/user/mview/' . $this->user->id;
                            $this->Business->addAdminNotice(GlobalCode::ADMIN_NOTICE_AUTH_VID, $this->user, $url);

                            $this->jsonResponse(true, '真人视频已提交审核');
                        } else {
                            $this->jsonResponse(true, $user->errors());
                        }
                    }

                    if (($param->action == 'update_basic_video') || ($param->action == 'add_basic_video')) {
                        $basicVid = $movementTb->find()
                            ->where([
                                'user_id' => $user->id,
                                'type' => \MovementType::BASIC_VID,
                                'or' => [['status' => CheckStatus::CHECKING], ['status' => CheckStatus::CHECKNO]]
                            ])->orderDesc('id')->first();
                        if(!$basicVid) {
                            $basicVid = $movementTb->newEntity([
                                'user_id' => $user->id,
                                'video' => $data['video'],
                                'video_cover' => $data['video_cover'],
                                'body' => '',
                                'status' => CheckStatus::CHECKED,
                                'type' => \MovementType::BASIC_VID
                            ]);
                        } else {
                            $basicVid->video = $data['video'];
                            $basicVid->video_cover = $data['video_cover'];
                            $basicVid->status = CheckStatus::CHECKED;
                        }
                        if($movementTb->save($basicVid)) {
                            $msg = $this->Business->createMsgBody(
                                '形象视频已提交审核',
                                '您的形象视频已提交审核，后台人员正在审核中'.
                                '约会说明涉嫌黄色信息；或表达不完整、不清晰。请返回【我的技能】重新编辑发布。',
                                '',
                                \MsgpushType::RM_MOVEMENT_CHECK
                            );
                            $this->Business->sendSMsg($user_id, $msg);
                            $this->jsonResponse(true, '形象视频已提交审核');
                        }
                    }
                }
            } else {
                $user = $UserTable->patchEntity($user, $data);
                $user->status = 1;
                if ($UserTable->save($user)) {
                    $this->jsonResponse(true, '保存成功');
                } else {
                    $this->jsonResponse(true, $user->errors());
                }
            }
            $this->jsonResponse(false, '成功调取接口');
        } else {
            $this->jsonResponse($res);
        }
    }


    /**
     * 获取地图上的用户,根据距离从近到远排序获取前10条信息
     * @param lng 登录者当前坐标-经度
     * @param lat 登录者当前坐标-纬度
     * @return json
     */
    public function getMapUsers() {
        $lng = $this->request->data('lng');
        $lat = $this->request->data('lat');
        $UserTable = \Cake\ORM\TableRegistry::get('User');
        $users = $UserTable->find()->select(['id', 'avatar', 'login_coord_lng', 'login_coord_lat',
                        'distance' => "getDistance($lng,$lat,login_coord_lng,login_coord_lat)"])
                ->where(['gender' => 2, 'status' => 3])
                ->orderDesc('distance')
                ->limit(10)->formatResults(function($items) {
                    return $items->map(function($item) {
                                $item['avatar'] = transformUrl($item['avatar'], true, ['w' => 184, 'fit' => 'stretch']);
                                $item['link'] = '/index/homepage/' . $item['id'];
                                return $item;
                            });
                })
                ->toArray();
        $this->jsonResponse(['result' => $users]);
    }


    /**
     *
     */
    public function getMapUsersF() {
        $pos_arr = ['114.127843,22.60722'];
        $lng = $this->request->data('lng');
        $lat = $this->request->data('lat');
        $UserTable = \Cake\ORM\TableRegistry::get('User');
        $users = $UserTable->find()->select(['id', 'avatar', 'login_coord_lng', 'login_coord_lat',
                    'distance' => "getDistance($lng,$lat,login_coord_lng,login_coord_lat)",])
                ->where(['gender' => 2])
                ->orderDesc('distance')
                ->limit(10)->formatResults(function($items)use($lng, $lat) {
                    return $items->map(function($item)use($lng, $lat) {
                                $item['id'] = mt_rand(1, 100);
                                $item['avatar'] = generateImgUrl($item['avatar']) .
                                        '?w=184&h=184&fit=stretch';
                                return $item;
                            });
                })
                ->toArray();
        $res = [];
        foreach($pos_arr as $key=>$pos){
            $c = explode(',', $pos);
            $users[$key]->login_coord_lng = (float) $c[0];
            $users[$key]->login_coord_lat = (float) $c[1];
            $res[] = $users[$key];
        }
        $this->jsonResponse(['result' => $res]);
    }


    /**
     * app登录
     * @param {string} u 手机号码
     * @param {string} p 密码
     * @param {int} lng 经度
     * @param {int} lat 纬度
     *
     * @return mixed
     * 1.登录成功
     * {
     *      status : true,
     *      redirect_url : '',
     *      token_uin : '',
     *      msg : '',
     *      user : {
     *
     *      }
     * }
     */
    public function getToken() {
        $u = $this->request->data('u');
        $p = $this->request->data('p');
        $lng = $this->request->data('lng');
        $lat = $this->request->data('lat');
        if($lng && $lat) {
            $data = [
                'phone' => $u,
                'pwd' => $p,
                'login_coord_lng' => $lng,
                'login_coord_lat' => $lat
            ];
        } else {
            $data = [
                'phone' => $u,
                'pwd' => $p
            ];
        }
        $this->Common->loginHandle($data, 1, true);
    }
    

    /**
     * 举报用户
     */
    public function reportUser(){
        $user_id = $this->request->data('user_id');
        $type = $this->request->data('type');
        if(!$user_id||!$type){
            $this->jsonResponse(false,'参数不正确');
        }
        $ReportTable = \Cake\ORM\TableRegistry::get('Report');
        $report = $ReportTable->newEntity($this->request->data());
        if($ReportTable->save($report)){
            $this->jsonResponse(true,'保存成功');
        }else{
            $this->jsonResponse(false,'保存失败');
        }
    }


    /**
     * 检查是否有未读消息、是否有新访客
     * @param user_id
     * @return json
     *  {
     *      fangke: 新访客数量（未读）,
     *      ptmessage: 新平台消息（未读），
     *      fangkes : [
     *          { avatar : ''},
     *          { avatar : ''},
     *          { avatar : ''}
     *      ]
     *  }
     */
    public function checkRead()
    {
        $uid = $this->request->data("user_id");
        $msgpush = TableRegistry::get('Msgpush');
        $visitorTb = TableRegistry::get('Visitor');
        $userTb = TableRegistry::get("User");
        $num = $msgpush->find()
            ->where(['user_id' => $uid, 'is_read' => 0])
            ->count();
        $visitors = $visitorTb->find()->hydrate(false)
            ->select(['visitor_id'])
            ->where(['visited_id' => $uid, 'is_read' => 0])
            ->toArray();
        $ids = [];
        $fangkes = [];
        $vnum = count($visitors);
        if($vnum) {
            $max = ($vnum >= 3) ? 2 : $vnum - 1;
            for($i = 0; $i <= $max; $i ++) {
                $ids[] = $visitors[$i]['visitor_id'];
            }

            $fangkes = $userTb->find()->select(['avatar'])->where(['id IN' => $ids])->map(function($row) {
                $row->avatar = generateImgUrl($row->avatar, true, ['w' => 50]);
                return $row;
            })->toArray();
        }
        /*$vnum = 1;
        $fangkes = $userTb->find()->select(['avatar'])->where(['id IN' => [228]])->map(function($row) {
            $row->avatar = generateImgUrl($row->avatar, true, ['blur' => 15, 'w' => 50]);
            return $row;
        })->toArray();*/
        $this->jsonResponse(['fangke' => $vnum, 'ptmessage' => $num, 'fangkes' => $fangkes]);
    }


    /**
     * 消耗套餐名额
     * $user_id 消费者id
     * $view_id 作用对象id
     * $type 额度类型SerType::
     */
    public function consumePack()
    {
        $res = false;
        $data = $this->request->data;
        $type = $data['type'];
        $view_id = $data['view_id'];
        $user_id = $data['user_id'];
        if(ServiceType::containType($type)) {
            $this->loadComponent('Business');
            $res = $this->Business->consumeRight($user_id, $view_id, $type);
        }
        if($res) {
            $this->jsonResponse(true, '消费成功');
        } else {
            $this->jsonResponse(false, '消费失败');
        }
    }


    /**
     * 消耗套餐名额
     * $user_id 消费者id
     * $view_id 作用对象id
     * $type 额度类型SerType::
     */
    public function consumePackage()
    {
        $res = false;
        $user_id = $this->request->query('user_id');
        $view_id = $this->request->query('view_id');
        $type = $this->request->query('type');
        if(ServiceType::containType($type)) {
            $this->loadComponent('Business');
            $res = $this->Business->consumeRight($user_id, $view_id, $type);
        }
        if($res) {
            //发送实时通知
            $this->loadComponent('Netim');
            $userTb = TableRegistry::get("User");
            $loginer = $userTb->get($user_id);
            $chatter = $userTb->get($view_id);
            $this->Netim->sendNotice($chatter, $loginer);
            $this->Netim->sendNotice($loginer, $chatter);
            if(ServiceType::CHAT == $type) {
                $this->jsonResponse(true, '终于等到你了，来聊聊天吧~');
            } else {
                $this->jsonResponse(true, '消费成功');
            }
        } else {
            $this->jsonResponse(false, '消费失败');
        }
    }


    /**
     * 检查是否可以聊天/查看动态接口
     * @param user1_id 登录者id
     * @param user2_id 聊天者id
     * @param type 2 服务类型：参考ServiceType::CHAT
     * @return {right:
     *              case 0:不合法参数
     *              case 1:可以访问（已经消耗过额度）
     *              case 2:可以访问（尚未消耗额度）
     *              case 3:不可以访问（没有额度可以消耗）
     *          resnum:
     *              剩余聊天次数
     *         }
     */
    public function checkRight()
    {
        $this->loadComponent('Business');
        $setting = $this->Business->getSettingParam(\Sysetting::CHAT_SETTING);
        $chat_people = 10;  //限制只能免费与xx个用户聊天
        if($setting && @unserialize($setting->content)) {
            $settingContent = @unserialize($setting->content);
            $chat_people = intval(isset($settingContent['people_num'])?$settingContent['people_num']:10);
        }

        $loginerid = $this->request->data("user1_id");
        $chatterid = $this->request->data("user2_id");
        $uTb = TableRegistry::get('User');
        $chatter = $uTb->get($chatterid);  //聊天者
        $loginer = $uTb->get($loginerid);  //登录者

        if(!$chatter || !$loginer) {
            $this->jsonResponse(['right' => 0]);
        }
        if($loginer->gender == 2) {
            //tmpLog('私聊权限日志A--：gender='.$loginer->gender);
            $this->jsonResponse(['right' => 1]);
        }
        $today = new Time();
        $todayStart = new Time($today->year.'-'.$today->month.'-'.$today->day);
        $todayEnd = new Time($today->year.'-'.$today->month.'-'.$today->day.' 23:59:59');
        $convTb = TableRegistry::get('Conversation');
        $hasChat = $convTb->find()->where([
            'fromAccount' => $loginer->imaccid,
            'toAccount' => $chatter->imaccid,
            'msgTimestamp >=' => $todayStart->timestamp
        ])->andWhere(['msgTimestamp <=' => $todayEnd->timestamp])->count();
        if($hasChat) {
            //tmpLog('私聊权限日志A：1');
            $this->jsonResponse(['right' => 1]);
        }
        $hasChatnum = $convTb->find()->distinct('toAccount')->where([
            'fromAccount' => $loginer->imaccid,
            'msgTimestamp >=' => $todayStart->timestamp
        ])->andWhere(['msgTimestamp <=' => $todayEnd->timestamp])->count();
        if($hasChatnum < $chat_people) {
            //tmpLog('私聊权限日志B：1'.'|'.$hasChatnum.'|user1id:'.$loginer->imaccid.'|user2id:'.$chatter->imaccid);
            $this->jsonResponse(['right' => 1]);
        }

        $res = 0;
        $toUrl = null;
        $type = $this->request->data("type");
        if($loginer->gender == 1) {
            if(ServiceType::containType($type)) {
                $this->loadComponent('Business');
                $res = $this->Business->checkRight($loginerid, $chatterid, $type);
            }
        } else {
            $res = SerRight::OK_CONSUMED;
        }
        switch ($res) {
            case SerRight::NO_HAVENONUM:
                $toUrl = '/userc/vip-buy?reurl=/index/homepage/'.$chatter->id;
                break;
        }
        //tmpLog('私聊权限日志C：'.$res);
        $this->jsonResponse(['right' => $res, 'to_url' => $toUrl]);
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
    public function checkTalk()
    {
        $this->loadComponent('Business');
        $loginerid = $this->request->data("user1_id");
        $chatterid = $this->request->data("user2_id");
        $this->jsonResponse($this->Business->checkTalk($loginerid, $chatterid));
    }


    /**
     * 获取用户便捷聊天用语列表
     * @param {int} sex 1#男性 2#女性
     * @return
     *  {
     *      "status": true,
     *      -"data": [
     *      -   {
     *              "body": "你好啊帅哥\r\n",
     *          },
     *  -       {
     *              "body": "爽肤水s",
     *          }
     *      ],
     *      "code": 200
     *  }
     */
    public function getConvList()
    {
        $gender = $this->request->data('sex');
        $autoimTb = TableRegistry::get("Autoim");
        $autoims = $autoimTb->find()->select(['body'])->where(['gender' => $gender, 'type' => GlobalCode::SAY_HI_COMMON])->toArray();
        $this->jsonResponse(['status' => true, 'data' => $autoims]);
    }


    /**
     * 获取登录广告页信息
     * @return
     * {
     *      'status' : true, //是否显示广告
     *      'data' :
     *      {
     *          'PicUrl' : '',   //广告图片链接，全链接，支持jpeg, png, gif等图片格式
     *          'id' : 12,     //广告唯一识别码
     *          'toUrl' : ''     //跳转链接，没有的话直接跳过
     *      }
     * }
     */
    public function getAdv()
    {
        $advTb = TableRegistry::get('Advertisement');
        $adv = $advTb->find()->where(['status' => 1])->first();
        if($adv) {
            $adv->pic_url = generateImgUrl($adv->pic_url);
            $adv->to_url = getHost() . $adv->to_url;
        }
        $this->jsonResponse(['status' => $adv ? true : false, 'data' => $adv]);
    }


    /**
     * 微信登录
     * 判断是否可以直接进行登录操作
     * @param {string} code
     * @param {int} type   #2微信登录
     * //已经注册，可以直接登录的情况
     * @return {
     *      code : 200,  //全局状态码, app端可以根据状态码在封装的请求里预先统一做某些处理
     *      status : true,  //业务状态，true处理成功/false处理失败
     *      msg : '',  //相关信息说明
     *      data : {
     *          1.成功的话返回用户信息
     *          user : {
     *              id : 12,
     *              nick : '',
     *              avatar : '',
     *              gender : 1/2,
     *              token_uin : '',
     *              im_accid : '',
     *              im_token : '',
     *          }
     *          2.失败的话返回跳转链接
     *          toUrl : ""
     *      }
     * }
     */
    public function thirdLogin()
    {
        if($this->request->is('POST')) {
            $code = $this->request->data('code');
            $type = $this->request->data('type');
            $this->Common->loginHandle(['code' => $code], $type, true);
        }
    }
}
