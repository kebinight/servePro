<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use GlobalCode;

/**
 * 后端基础服务组件
 * 主要处理登录服务、登出服务等常规服务
 * Common component
 */
class CommonComponent extends Component
{
    const ADMIN_LOGIN_LOG = 'Admin.login.log';         //登录行为记录
    const ADMIN_LOGIN_SESSION = 'Admin.login.info';         //管理员登录信息

    const ADMIN_LOGIN_ERROR_COUNT = 'Admin.login.error.count';  //登录失败次数统计
    /**
     * Default configuration.
     * @var array
     */
    public $components = [];
    protected $_defaultConfig = [];
    protected $controller = null;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->controller = $registry->getController();
    }


    /**
     * 执行返回
     * @param $returnData
     * @param string $format
     */
    protected function doReturn($returnData, $format = 'json') {
        $this->autoRender = false;

        if($format == 'json') {
            $returnData = json_encode($returnData, JSON_UNESCAPED_UNICODE);
        }

        //跨域访问
        $response = $this->response->cors($this->request)->allowCredentials()
            ->allowHeaders(["Access-Control-Allow-Headers" => "Content-Type,Access-Token"])
            ->allowMethods(['GET', 'POST', 'OPTIONS'])->allowOrigin('*')->build();
        $response->type($format);
        $response->body($returnData);
        $response->charset('utf-8');
        $response->send();
        exit();
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
        $returnData = [
            'code' => GlobalCode::API_SUCCESS,
            'status' => $status,
            'msg' => $msg,
            'data' => $data ? $data : []
        ];

        $this->doReturn($returnData, $format);
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
    public function failReturn($code = GlobalCode::API_OPTIONS, $errmsg = '', $msg = '请求失败', $data = [], $format = 'json') {
        $returnData = [
            'code' => $code,
            'status' => false,
            'msg' => $msg,
            'data' => $data,
            'errmsg' => $errmsg ? $errmsg : GlobalCode::toString($code)
        ];

        $this->doReturn($returnData, $format);
    }


    /**
     * 返回任意字符串数据包
     * @param string $msg
     */
    public function anyReturn($msg, $format = 'json') {
        $this->doReturn($msg, $format);
    }


    /**
     * 检测登陆状态
     * @param boolean $isApp 是否app原生访问
     * @return {{boolean}} result
     */
    public function handleCheckLogin()
    {
        $user = $this->getLoginer();
        if (!$user) {
            $this->failReturn(GlobalCode::API_NO_LOGIN, '', '请先登录');
        }
    }


    /**
     * 获取登录信息
     */
    public function getLoginSession()
    {
        return $this->request->session()->read(self::ADMIN_LOGIN_SESSION);
    }


    /**
     * 获取登录者信息
     * @return mixed user
     */
    public function getLoginer()
    {
        $loginInfo = $this->getLoginSession();
        return $loginInfo['user_info'];
    }


    /**
     * session记录用户登录信息
     * @param $user
     */
    public function setLoginInfo($user)
    {
        $res = false;
        try {
            $loginSession = [
                'user_info' => $user,
                'timestamp' => time()
            ];
            $this->request->session()->write(self::ADMIN_LOGIN_SESSION, $loginSession);
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
    public function updateLoginInfo($newUserinfo = null)
    {
        $uTb = TableRegistry::get('Suser');
        $user = $this->getLoginer();
        if($user) {
            if(!$newUserinfo) {
                $user = $uTb->find()->where(['id' => $user->id])->first();
            } else {
                $user = $newUserinfo;
            }
            return $this->setLoginInfo($user);
        }
        return false;
    }


    /**
     * 登录安全防火墙策略检查
     */
    public function loginFirewallCheck()
    {

        $loginLog = $this->getLoginLog();
        if($loginLog[self::ADMIN_LOGIN_ERROR_COUNT] > 3) {
            $this->failReturn(GlobalCode::API_NOT_SAFE, '', '登录失败次数过多，请稍候再试');
        }
    }


    /**
     * 获取登录行为记录
     * @param array $loginfo
     */
    public function getLoginLog($key = null)
    {
        $loginfo =  $this->request->session()->read(self::ADMIN_LOGIN_LOG);
        if($key) {
            return isset($loginfo[$key]) ? $loginfo[$key] : '';
        } else {
            return $loginfo;
        }
    }


    /**
     * 登录行为记录
     * @param array $loginfo
     */
    public function setloginLog($loginfo = [])
    {
        $loginInfo = $this->getLoginLog() ? $this->getLoginLog() : [];
        $this->request->session()->write(self::ADMIN_LOGIN_LOG, array_merge($loginInfo, $loginfo));
    }


    /**
     * 处理登录业务
     * @param array $data
     * @param int $type  #1普通登录 #2微信登录
     */
    public function loginHandle($data = [], $type = 1, $isNative = false)
    {
        $this->loginFirewallCheck();
        if(!$type) {
            $this->failReturn(GlobalCode::API_OPTIONS, 'parameter of "type" is needed!');
        }
        $type = intval($type);
        switch ($type) {
            case 1 :
                $this->handleLogin($data);
                break;
            case 2 :
                break;
            default :
                $this->failReturn(GlobalCode::API_OPTIONS, 'parameter of "type" is invalid!');
        }
    }


    public function handleLogin($data)
    {
        if(!isset($data['account']) || !isset($data['pwd'])) {
            $this->failReturn(GlobalCode::API_OPTIONS, 'parameter of "account" or "password" is needed!');
        }

        $userTb = TableRegistry::get('Suser');
        $user = $userTb->find()->where(['account' => $data['account']])->first();
        if($user) {

        } else {
            $errorCount = $this->getLoginLog(self::ADMIN_LOGIN_ERROR_COUNT) ? $this->getLoginLog(self::ADMIN_LOGIN_ERROR_COUNT) : 0;
            $this->setloginLog([self::ADMIN_LOGIN_ERROR_COUNT => $errorCount + 1]);
            $this->failReturn(GlobalCode::API_NO_ACCOUNT, '', '账号不存在!');
        }
    }
}
