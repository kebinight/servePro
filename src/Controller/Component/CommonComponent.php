<?php
namespace App\Controller\Component;

use App\Controller\Mobile\AppController;
use App\Model\Entity\User;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use GlobalCode;
use MsgpushType;
use UserStatus;

/**
 * 基础服务组件
 * 主要处理登录服务、登出服务等常规服务
 * Common component
 */
class CommonComponent extends Component
{
    const LOGIN_SESSION = 'User.login.info';   //登录后session保存用户信息
    const LOGIN_SESSION_TIMESTAMP = 'User.login.timestamp';   //最后更新时间
    const APP_LOGIN_TOEKN = 'Login.login_token';  //app登录后session保存user_token信息
    const WX_LOGIN_OPENID = 'Login.openid';  //

    /**
     * Default configuration.
     * @var array
     */
    public $components = ['Netim', 'Business', 'Wx'];
    protected $_defaultConfig = [];
    protected $controller = null;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->controller = $registry->getController();
    }


    /**
     * 返回业务成功处理结果数据包-不管业务结果是true还是false，都是业务成功处理的范畴
     * 此接口只返回code为200的情况，其他情况不在此范畴
     * @param bool $status
     * @param string $msg
     * @param array $data
     * @param string $format  返回的数据格式
     */
    public function dealReturn($status = true, $msg = '', $data = [], $format = 'json') {
        $this->autoRender = false;
        $this->response->type($format);
        $returnData = [
            'code' => GlobalCode::SUCCESS,
            'status' => $status,
            'msg' => $msg,
            'data' => $data ? $data : []
        ];
        if($format == 'json') {
            $returnData = json_encode($returnData, JSON_UNESCAPED_UNICODE);
        }

        //跨域访问
        $response = $this->response->cors($this->request)->allowCredentials()
            ->allowMethods(['GET', 'POST'])->allowOrigin('*')->build();

        $response->body($returnData);
        $response->charset('utf-8');
        $response->send();
        exit();
    }


    /**
     * 返回业务处理失败数据包-比如在处理业务之前的接口验证不通过，查询数据库发生错误与业务正常处理无关的错误。
     * 此接口多用于前端的ajax或请求函数有封装了预处理
     * @param string $errmsg
     * @param int $code
     * @param string $msg
     * @param array $data
     * @param string $format  返回的数据格式
     */
    public function failReturn($errmsg = '接口参数不正确', $code = GlobalCode::OPTIONS_NOTRIGHT, $msg = '', $data = [], $format = 'json') {
        $this->autoRender = false;
        $this->response->type($format);
        $returnData = [
            'code' => $code,
            'status' => false,
            'msg' => $msg,
            'data' => $data,
            'errmsg' => $errmsg
        ];
        if($format == 'json') {
            $returnData = json_encode($returnData, JSON_UNESCAPED_UNICODE);
        }
        echo $returnData;
        exit();
    }


    /**
     * 返回任意字符串数据包
     * @param string $msg
     */
    public function anyReturn($msg, $format = 'json') {
        $this->autoRender = false;
        if($format == 'json') {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        echo $msg;
        exit();
    }


    /**
     * 处理登录业务
     * @param array $data
     * @param int $type  #1普通登录 #2微信原生APP登录 #3微信h5端登录
     */
    public function loginHandle($data = [], $type = 1, $isAppNative = false)
    {
        if(!$type) {
            $this->failReturn('type is needed');
        }
        $type = intval($type);
        switch ($type) {
            case 1 :
                $this->cologinHandler($data, $isAppNative);
                break;
            case 2 :
                $this->wxLoginHandler($data);
                break;
            default :
                $this->failReturn('wrong type');
                break;
        }
    }


    /**
     * 通过普通方式登录
     */
    public function cologinHandler($data = [], $isAppNative = false)
    {
        //检验参数合法性
        if(!is_array($data)) {
            $this->failReturn();
        }
        if(!isset($data['phone'])) {
            $this->dealReturn(false, '请输入手机号码');
        }
        if(!isset($data['pwd'])) {
            $this->dealReturn(false, '请输入密码');
        }

        $UserTable = TableRegistry::get('User');
        /*$user = $UserTable->find()->select(['nick', 'imaccid', 'user_token', 'gender', 'pwd', 'imtoken', 'avatar',
            'reg_step', 'id', 'status', 'enabled', 'login_time', 'login_coord_lng', 'login_coord_lat'])
            ->where(['phone' => $data['phone'], 'enabled' => 1, 'is_del' => 0])
            ->first();*/
        $user = $UserTable->find()->where(['phone' => $data['phone'], 'enabled' => 1, 'is_del' => 0])->first();

        if (!$user) {
            $this->dealReturn(false, '该手机号未注册或被禁用');
        }

        if(!$isAppNative && !$this->request->is('lemon') && !$this->request->is('weixin')) {
            $this->dealReturn(false, 'H5版本暂不支持上传图像和视频，为了您有更好的体验，请前往下载app',
                ['redirect_url' => '/other/downApp']);
        }

        if (!(new \Cake\Auth\DefaultPasswordHasher)->check($data['pwd'], $user->pwd)) {
            $this->dealReturn(false, '密码不正确');
        } else {
            //用户信息检测
            $checkRes = $this->loginInfoCheck($user, $isAppNative);
            switch ($checkRes) {
                case GlobalCode::REG_NOT_FIN :
                    //尚未注册完成，跳转到注册流程
                    if($user->gender == 1) {
                        $redirectUrl = '/user/m-reg-basic-info-' . $user->reg_step . '/' . $user->id;
                    } else {
                        $redirectUrl = '/user/reg-basic-info-' . $user->reg_step . '/' . $user->id;
                    }
                    if($isAppNative) {
                        $this->response->type('json');
                        $resData = ['status' => false,
                            'msg' => '注册未完成,请继续注册步骤',
                            'code' => GlobalCode::REG_NOT_FIN,
                            'redirect_url' => $redirectUrl];
                        $this->anyReturn($resData);
                    } else {
                        $this->failReturn('register not finish', GlobalCode::REG_NOT_FIN, '注册未完成,请继续注册步骤', ['redirect_url' => $redirectUrl]);
                    }
                    break;
                case GlobalCode::FORBIDDEN_ACCOUNT :
                    $this->failReturn('forbidden account', GlobalCode::ACCOUNT_NOT_FIND, '此账号不可用');
                    break;
            }

            $user_token = $user->user_token;
            $data['login_time'] = date('Y-m-d H:i:s');
            $user = $UserTable->patchEntity($user, $data);
            $UserTable->save($user);
            $redirect_url = '/index/find-list';
            if(isset($data['rediret'])) {
                //对redirect_url进行匹配修整
                if (in_array($redirect_url, ['/home/my-install', '/user/login', '/user/index'])) {
                    if($user->gender == 1) {
                        $redirect_url = '/index/find-list';
                    } else {
                        $redirect_url = '/index/find-rich-list';
                    }
                } else {
                    $redirect_url = $data['redirect'];
                }
            } else if ($user->gender == 2) {
                //女性用户首页
                $redirect_url = '/index/find-rich-list';
            }
            unset($user->pwd);
            $user->avatar = generateImgUrl($user->avatar);

            $this->setLogin($user);
            //由于app端是原生的且没有统一好所以返回的数据现在有点不一样
            if($isAppNative) {
                $this->anyReturn([
                    'status' => true,
                    'msg' => '登录成功',
                    'redirect_url' => $redirect_url,
                    'token_uin' => $user_token,
                    'user' => $user
                ]);
            } else {
                $this->dealReturn(true, '登录成功', ['redirect_url' => $redirect_url, 'token_uin' => $user_token, 'msg' => '登入成功', 'user' => $user]);
            }
        }
    }


    /**
     * 原生APP的微信登录接口
     * @param array $data
     * @param bool $isApp
     */
    public function wxLoginHandler($data = [])
    {
        if(!is_array($data) || !$data['code']) {
            $this->failReturn('code is needed');
        }

        tmpLog('wxLoginHander,获取到的code:' . $data['code']);
        $wxinfo = $this->Wx->getUser($data['code'], true, true);
        tmpLog('wxLoginHander,获取到的wxinfo:' . json_encode($wxinfo));
        if($wxinfo) {
            $loginRes = $this->wxLoginHandle($wxinfo);
            $user = $loginRes['user'];
            switch ($loginRes['code']) {
                case GlobalCode::SUCCESS :
                    $returnUser = $this->formateLoginUserInfo($user);
                    $this->dealReturn(true, '登录成功', ['user' => $returnUser]);
                    break;
                case GlobalCode::ACCOUNT_NOT_FIND :
                    //尚未注册账号，跳转到注册流程
                    $toUrl = getHost() . '/user/bind-phone?wxinfo=' . json_encode($wxinfo);
                    $this->dealReturn(false, '', ['toUrl' => $toUrl]);
                    break;
                case GlobalCode::REG_NOT_FIN :
                    //尚未注册完成，跳转到注册流程
                    $toUrl = getHost() . '/user/reg-basic-info-' . $user->reg_step . '/' . $user->id;
                    $this->dealReturn(false, '注册未完成,请继续注册步骤', ['toUrl' => $toUrl]);
                    break;
                case GlobalCode::FORBIDDEN_ACCOUNT :
                    $this->dealReturn(false, '此账号不可用');
                    break;
                case GlobalCode::FAIL :
                    $this->dealReturn(false, '登录失败');
                    break;
            }
        } else {
            $this->failReturn('get user wxinfo faild', GlobalCode::ERROR, '获取微信接口调用失败，请重试');
        }
    }


    /**
     * H5的微信登录接口
     * @param array $data
     * @param bool $isApp
     */
    public function wxH5LoginHandler($wxinfo = [])
    {
        if($wxinfo) {
            $loginRes = $this->wxLoginHandle($wxinfo);
            $user = $loginRes['user'];
            switch ($loginRes['code']) {
                case GlobalCode::SUCCESS :
                    $redirectUrl = ($user->gender == 1) ? '/index/find-list' : '/index/find-rich-list';
                    $this->controller->redirect($redirectUrl);
                    break;
                case GlobalCode::ACCOUNT_NOT_FIND :
                    break;
                case GlobalCode::REG_NOT_FIN :
                    //尚未注册完成，跳转到注册流程
                    $redirectUrl = '/user/reg-basic-info-' . $user->reg_step . '/' . $user->id;
                    $this->controller->redirect($redirectUrl);
                    break;
                case GlobalCode::FORBIDDEN_ACCOUNT :
                    $redirectUrl = '/user/login';
                    $this->controller->redirect($redirectUrl);
                    break;
                case GlobalCode::FAIL :
                    $redirectUrl = '/user/login';
                    $this->controller->redirect($redirectUrl);
                    break;
            }
        }
    }


    /**
     * 尝试微信登录
     * @param mixed $wxinfo
     * @return array
     * {
     *      code
     *      res 登录结果: 200#登录成功 201#账号未完成注册步骤 202#账号被禁用
     * }
     */
    public function wxLoginHandle($wxinfo)
    {
        $user = null;
        $res = [
            'code' => GlobalCode::FAIL,
            'user' => null
        ];
        if($wxinfo) {
            $UserTable = TableRegistry::get('User');
            if (isset($wxinfo->unionid)) {
                $union_id = $wxinfo->unionid;
                $user = $UserTable->find()->select()->where(['union_id' => $union_id])->first();
            }
            if (!isset($wxinfo->unionid) && isset($wxinfo->openid)) {
                $open_id = $wxinfo->openid;
                $user = $UserTable->find()->where(['wx_openid' => $open_id])->first();
            }
            if ($user) {
                //检查用户信息是否符合登录条件
                $cres = $this->loginInfoCheck($user);
                $res['user'] = $user;
                if(GlobalCode::SUCCESS == $cres) {
                    //登录态设置
                    if($this->setLogin($user)) {
                        $res['code'] = GlobalCode::SUCCESS;
                    } else {
                        $res['code'] = GlobalCode::FAIL;
                    }
                } else {
                    $res['code'] = $cres;
                }
            } else {
                $res['code'] = GlobalCode::ACCOUNT_NOT_FIND;
            }
        }
        return $res;
    }


    /**
     * 对用户除账号密码外一些基本信息进行限制登录
     * @param User $user
     * @param bool $isApp
     */
    public function loginInfoCheck(User $user, $isApp = false)
    {
        if($user->enabled != 1 || $user->is_del != 0) {
            //账号被禁用
            return GlobalCode::FORBIDDEN_ACCOUNT;
        }
        if ($user->reg_step != 9) {
            //注册未完成
            return GlobalCode::REG_NOT_FIN;
        }
        return GlobalCode::SUCCESS;
    }


    /**
     * 通过微信方式注册账号
     * @param $wxinfo
     * @param int $ignore
     */
    public function wxRegister($wxinfo, $ignore = 0)
    {
        if($wxinfo) {
            //女性暂时不提供微信登录功能
            /*if(!isset($wxinfo->sex) || intval($wxinfo->sex) != 1) {
                $this->dealReturn(false, '女性不提供微信登录方式');
            }*/

            $phone = $this->request->data('phone');
            $vcode = $this->request->data('vcode');

            $wxuser = null;
            $UserTable = TableRegistry::get('User');
            if (isset($wxinfo->unionid)) {
                $union_id = $wxinfo->unionid;
                $wxuser = $UserTable->find()->where(['union_id' => $union_id])->first();
            }
            if (!isset($wxinfo->unionid) && isset($wxinfo->openid)) {
                $open_id = $wxinfo->openid;
                $wxuser = $UserTable->find()->where(['wx_openid' => $open_id])->first();
            }

            //绑定手机，否则直接使用微信信息注册账号
            if(!$ignore) {
                if($phone) {
                    //验证验证码
                    $this->checkVcode($vcode, $phone);
                    //根据手机号获取用户信息
                    $puser = $UserTable->find()->where(['phone' => $phone])->first();
                    if(!$wxuser && !$puser) {
                        //如果微信信息尚未注册，并且对应手机号也尚未注册，则直接创建该微信用户并绑定手机号
                        $wxinfo->phone = $phone;
                        $this->wxRegistNew($wxinfo);
                    } else if($wxuser && !$puser) {
                        //如果微信信息已经注册，但对应手机号码尚未注册，则绑定手机号码到该微信用户
                        $this->wxBindPhone($wxuser, $phone);
                    } else if(!$wxuser && $puser) {
                        //如果微信信息尚未注册，但对应手机号码已经注册，则绑定微信到该手机号账户
                        if($puser->wx_openid) {
                            //手机已经绑定过微信号，不能再进行绑定
                            $this->dealReturn(false, '手机号码已经注册过');
                        } else {
                            $puser->wx_openid = isset($wxinfo->openid) ? $wxinfo->openid : '';
                            $puser->union_id = isset($wxinfo->unionid) ? $wxinfo->unionid : '';
                            if($UserTable->save($puser)) {
                                $res = $this->setLogin($puser);
                                if($res) {
                                    if($puser->gender == 1) {
                                        $redirect_url = '/index/find-list';
                                    } else {
                                        $redirect_url = '/index/find-rich-list';
                                    }
                                    $resData = $this->generatWxReturnBody($redirect_url, $puser);
                                    $this->dealReturn(true, '绑定成功', $resData);
                                } else {
                                    $this->dealReturn(false, '登录失败');
                                }
                            } else {
                                $this->dealReturn(false, '绑定失败，请重试');
                                runLog('手机绑定微信号失败:' . $puser->errors());
                            }
                        }
                    } else if($wxuser->id == $puser->id){
                        //如果微信注册账户和手机注册账户是同一个账户，则说明已经绑定过了，无需继续绑定
                        $this->dealReturn(true, '已经绑定，无需再绑定');
                    } else {
                        //微信注册账户和手机注册账户不是同一个账户并都已经注册过了，则不能进行互绑操作
                        $this->dealReturn(false, '手机号码已经注册过');
                    }
                } else {
                    $this->dealReturn(false, '请填写手机号码');
                }
            } else {
                if(!$wxuser) {
                    $this->wxRegistNew($wxinfo);
                } else {
                    $this->dealReturn(false, '不能重复操作');
                }
            }
        } else {
            $this->failReturn('get user wxinfo faild', GlobalCode::ERROR, '获取微信接口调用失败，请重试');
        }
    }


    /**
     * 检查验证码合法性
     * @param $vcode
     * @param $phone
     */
    public function checkVcode($vcode, $phone)
    {
        $SmsTable = TableRegistry::get('Smsmsg');
        $sms = $SmsTable->find()->where(['phone' => $phone])->orderDesc('create_time')->first();
        //验证码验证
        if (!$sms) {
            $this->dealReturn(false, '验证码错误');
        } else {
            if ($sms->code != $vcode) {
                $this->dealReturn(false, '验证码错误');
            }
            if ($sms->expire_time < time()) {
                $this->dealReturn(false, '验证码已过期');
            }
        }
    }


    /**
     * 微信绑定手机号
     * @param $wxuser
     * @param $phone
     */
    public function wxBindPhone($wxuser, $phone)
    {
        $UserTable = TableRegistry::get('User');
        if($wxuser->phone) {
            //微信已经绑定过手机号，不能绑定新手机号
            $this->dealReturn(false, '微信已经绑定过手机号');
        } else {
            $phoner = $UserTable->find()->where(['phone' => $phone])->count();
            if(!$phoner) {
                $wxuser->phone = $phone;
                if($UserTable->save($wxuser)) {
                    $this->dealReturn(true, '绑定成功');
                } else {
                    $this->dealReturn(false, '绑定失败，请重试');
                    runLog('微信绑定手机号失败:' . $wxuser->errors());
                }
            } else {
                $this->dealReturn(false, '该手机号码已经被绑定');
            }
        }
    }


    /**
     * 微信注册新账号
     * @param {jsonObject} $wxinfo 微信传回的用户信息
     */
    public function wxRegistNew($wxinfo)
    {
        $regData = [
            'pwd' => 987543
        ];
        if(isset($wxinfo->phone)) {
            $regData['phone'] = $wxinfo->phone;
        }
        if(isset($wxinfo->nickname)) {
            $regData['nick'] = $wxinfo->nickname;
        }
        if(isset($wxinfo->openid)) {
            $regData['wx_openid'] = $wxinfo->openid;
        }
        if(isset($wxinfo->unionid)) {
            $regData['union_id'] = $wxinfo->unionid;
        }
        if(isset($wxinfo->sex)) {
            $regData['gender'] = (intval($wxinfo->sex) == 1) ? 1 : 2;
        }
        if(isset($wxinfo->unionid)) {
            $regData['union_id'] = $wxinfo->unionid;
        }
        if(isset($wxinfo->headimgurl)) {
            $regData['avatar'] = $wxinfo->headimgurl;
        }
        $this->registerNew($regData);
    }


    /**
     * 检测登陆状态
     * @param boolean $isApp 是否app原生访问
     * @return {{boolean}} result
     */
    public function checkLoginHand($isApp = false)
    {
        if($isApp) {

        } else {
            $user = $this->getLoginer();
            $url = '/' . $this->request->url;
            if (!$user) {
                if ($this->request->is('ajax')) {
                    $url = $this->request->referer();
                    $login_url = '/user/login?redirect_url=' . $url;
                    $this->anyReturn(['status' => false, 'msg' => '请先登录', 'code' => 403, 'redirect_url' => $login_url]);
                }
                $url = urlencode($url);
                $this->controller->redirect('/user/login?redirect_url=' . $url);
                $this->response->send();
                $this->response->stop();
                return false;
            }
        }
    }


    /**
     * 检查是否登录
     * @return bool
     */
    public function checkLogin()
    {
        return $this->request->session()->check(self::LOGIN_SESSION);
    }


    /**
     * 获取登录者信息
     * @return mixed user
     */
    public function getLoginer()
    {
        $user = $this->request->session()->read(self::LOGIN_SESSION);
        return $user;
    }


    /**
     * session记录用户登录信息
     * @param $user
     */
    public function setLogin($user)
    {
        $res = false;
        try {
            $this->request->session()->write(self::LOGIN_SESSION, $user);
            $this->request->session()->write(self::LOGIN_SESSION_TIMESTAMP, time());
            /*if ($this->request->is('lemon')) {
                $this->request->session()->write(self::APP_LOGIN_TOEKN, $user->user_token);
            }*/
            $res = true;
        } catch (\Cake\Core\Exception\Exception $e) {
            runLog('session记录用户登录信息失败', json_encode($e->getMessage()), 'user_id:' . $user->id);
        } finally {
            return $res;
        }
    }


    /**
     * 刷新内存中的登录信息
     * @return bool
     */
    public function updateLoginInfo($newUserinfo = null, $isApp = false)
    {
        $uTb = TableRegistry::get('User');
        $user = $this->getLoginer();
        if($user || $isApp) {
            if(!$newUserinfo) {
                $user = $uTb->find()->where(['id' => $user->id])->first();
                //->select(['id', 'truename', 'is_normal', 'avatar', 'imaccid', 'imtoken', 'login_time',
                //'wx_openid', 'union_id', 'phone', 'gender', 'user_token', 'reg_step', 'status', 'is_agent'])
            } else {
                $user = $newUserinfo;
            }
            return $this->setLogin($user);
        }
        return false;
    }


    /**
     * 获取登录信息最后更新时间
     * @return null|string
     */
    public function getLoginUpdateTimestamp()
    {
        return $this->request->session()->read(self::LOGIN_SESSION_TIMESTAMP);
    }


    /**
     * 构造微信登录成功返回信息
     * @param $url
     * @param User $user
     * @return array
     */
    public function generatWxReturnBody($url, User $user)
    {
        if($url && $user) {
            return [
                'url' => $url,
                'user' => [
                    'token_uin' => $user->user_token,
                    'gender' => $user->gender,
                    'imaccid' => $user->imaccid,
                    'imtoken' => $user->imtoken,
                    'avatar' => $user->avatar,
                    'id' => $user->id,
                ],
                'is_login' => true
            ];
        }
    }


    /**
     * 注册新用户
     * @param array $data
     * @return mixed 直接返回信息到客户端
     */
    public function registerNew($data = [])
    {
        tmpLog('进入registerNew---------------------------------------');
        //参数验证
        if((isset($data['phone']) || isset($data['wx_openid']))) {
        } else {
            $this->failReturn();
        }

        $uTb = TableRegistry::get('User');
        $newUser = $uTb->newEntity();
        $nickname = '约见吧' . rand(100000, 999999);
        $newUser->nick = $nickname;
        $newUser->truename = $nickname;
        $newUser->is_normal = 1;
        //默认头像
        $newUser->avatar = '/mobile/images/m_avatar_2.png';
        //定位记录
        /*$coord = $this->getPosition();
        if($coord){
            $newUser->login_coord_lng = $coord[0];
            $newUser->login_coord_lat = $coord[1];
        }*/
        //从im 池中获取im 账号绑定
        $im = $this->Business->getNetim();
        if ($im) {
            $newUser->imaccid = $im['accid'];
            $newUser->imtoken = $im['token'];
        }
        //仅用于测试环境
        if (Configure::read('debug')) {
            if(!$newUser->imaccid) {
                $newUser->imaccid = '';
                $newUser->imtoken = '';
            }
        }
        //登录时间
        $newUser->login_time = date('Y-m-d H:i:s');
        $data['user_token'] = md5(uniqid());
        if ($this->request->is('weixin')) {
            $data['device'] = 'weixin';
        }
        $newUser = $uTb->patchEntity($newUser, $data);
        if(!$newUser->state) {
            $newUser->state = 2;
        }
        if($data['gender'] == 1){
            //男性则直接登录
            $newUser->reg_step = 1;  //注册完毕
            $newUser->status = 1;
            $newUser->auth_status = 1;
            $newUser->is_agent = 1;  //默认是经纪人
        }else{
            $newUser->reg_step = 1;
            $newUser->status = 1;
            $newUser->auth_status = 1;
            $newUser->is_agent = 1; //默认是经纪人
        }
        //审核模式
        $iosCheckConf = \Cake\Core\Configure::read('ios_check_conf');
        if($iosCheckConf['check_mode']) {
            $newUser->bonus_point = $iosCheckConf['init_point'];
        }
        tmpLog('即将注册新用户：' . json_encode($newUser));
        if ($uTb->save($newUser)) {
            //发送平台消息
            $msg = "哇哦！平台又迎来一位颜值爆表的小仙女！现在就来学习如何使用约见吧圈APP吧！" .
                "<br>点击查看☞ <a href='/activity/carousel-page/6' style='color: deepskyblue'>《新手使用指南》</a>" .
                "<br>点击查看☞ <a href='/activity/carousel-page/9' style='color: deepskyblue'>《用户常见问题》</a>";
            if($newUser->gender == 1) {
                $msg = '亲爱的用户，恭喜您成功注册约见吧圈APP！平台主打高端圈层社交活动，约工作、约娱乐、约运动，真人视频验证审核，打造品质社交体验。';
            }
            $message = $this->Business->createMsgBody(
                '用户注册-消息推送',
                $msg,
                '',
                MsgpushType::RM_REGISTER
            );
            $this->Business->sendSMsg($newUser->id, $message, [MsgpushType::METHOD_PTMSG]);

            //进入更新队列
            $this->Netim->addUpInfoQueue($newUser->id);
            /*if(isset($data['incode'])) {
                $this->Business->create2Invit($data['incode'], $newUser->id);
            }*/

            //注册成功后返回
            $jumpUrl = '/user/m-reg-basic-info1/' . $newUser->id;
            if ($newUser->gender == 2) {
                $jumpUrl = '/user/reg-basic-info-1/' . $newUser->id;
            }
            $msg = '注册成功';
            $returnUser = false;
            if($data['gender'] == 1){
                //男性则直接登录
                $is_login = true;
                $this->setLogin($newUser);
                $returnUser = $this->formateLoginUserInfo($newUser);
            }else{
                $is_login = false;
            }

            $this->dealReturn(true, '注册成功', [
                'status' => true,
                'msg' => $msg,
                'url' => $jumpUrl,
                'user' => $returnUser,
                'is_login' => $is_login
            ]);
        } else {
            runLog('用户注册失败' . json_encode($newUser->errors()));
            $this->dealReturn(false, $msg = '注册失败');
        }
    }


    /**
     * 提供给客户端保存登录信息的用户信息体
     */
    public function formateLoginUserInfo(User $user)
    {
        return [
            'token_uin' => $user->user_token,
            'gender' => $user->gender,
            'imaccid' => $user->imaccid,
            'imtoken' => $user->imtoken,
            'avatar' => generateImgUrl($user->avatar),
            'nick' => $user->nick,
            'id' => $user->id
        ];
    }
}
