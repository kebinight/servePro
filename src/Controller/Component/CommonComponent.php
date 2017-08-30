<?php
namespace App\Controller\Component;

use Cake\Auth\DefaultPasswordHasher;
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
    const ADMIN_LOGIN_SESSION = 'ADMIN_LOGIN_SESSION';         //管理员登录信息

    const ADMIN_LOGIN_LOG = 'ADMIN_LOGIN_LOG';              //登录行为统计
    const ADMIN_LOGIN_ERROR_COUNT = 'ADMIN_LOGIN_COUNT';  //登录失败次数统计
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
        $response = $this->response->cors($this->request)
            ->allowHeaders(["Access-Control-Allow-Headers" => "Origin, X-Requested-With, Content-Type, Accept, user-agent, Token, woami"])
            ->allowMethods(['GET', 'POST', 'OPTIONS'])
            ->allowCredentials()
            ->allowOrigin('http://pro-admin.cn:8080')
            //->allowOrigin('*')
            ->build();
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
    public function getLoginSession($key = null)
    {
        $loginSession = $this->request->session()->read(self::ADMIN_LOGIN_SESSION);
        if($key !== null) {
            return isset($loginSession[$key]) ? $loginSession[$key] : null;
        }
        return $loginSession;
    }


    /**
     * 获取登录者信息
     * @return mixed user
     */
    public function getLoginer()
    {
        $loginInfo = $this->getLoginSession('user_info');
        return $loginInfo;
    }


    /**
     * session记录用户登录信息
     * 登录信息包括：
     * 1) 用户基本信息
     * 2) 用户菜单权限
     * 3) 用户权限
     * 4) 记录时间
     * @param $user
     */
    public function setLoginInfo($userId)
    {
        $res = null;
        try {
            $roles = [];
            $menus = [];
            $limits = [];

            $userTb = TableRegistry::get('Suser');
            $menuTb = TableRegistry::get('Smenu');
            $limitTb = TableRegistry::get('Slimit');
            $user  = $userTb->find()->contain(['Srole'])->where(['id' => $userId])->first();

            foreach ($user->srole as $item) {
                $roles[] = $item->id;
            }
            if($roles) {
                $menus = $menuTb->find('threaded')->innerJoinWith('Srole', function($q) use ($roles){
                    return $q->where(['Srole.id IN' => $roles]);
                })->distinct('Smenu.id')->where(['Smenu.status' => GlobalCode::COMMON_STATUS_ON])->orderDesc('Smenu.rank')->toArray();
                $limits = $limitTb->find('threaded')->innerJoinWith('Srole', function($q) use ($roles) {
                    return $q->where(['Srole.id IN' => $roles]);
                })->distinct('Slimit.id')->where(['Slimit.status' => GlobalCode::COMMON_STATUS_ON])->toArray();
            }

            $limits_tmp = [];
            foreach ($limits as $limit) {
                $child_tmp = [];
                foreach ($limit->children as $item) {
                    $child_tmp[$item->node] = $item;
                }
                $limit->children = $child_tmp;
                $limits_tmp[$limit->node] = $limit;
            }

            unset($user->srole);
            $loginSession = [
                'user_info' => $user,
                'user_menus' => $menus,
                'user_limits' => $limits_tmp,
                'timestamp' => time()
            ];
            $this->request->session()->write(self::ADMIN_LOGIN_SESSION, $loginSession);
            $res = $user;
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
    public function updateLoginInfo($userId)
    {
        return $this->setLoginInfo($userId);
    }


    /**
     * 登录安全防火墙策略检查
     */
    public function loginFirewallCheck()
    {
        if($this->getLoginLog(self::ADMIN_LOGIN_ERROR_COUNT) > 3) {
            $this->failReturn(GlobalCode::API_NOT_SAFE, '', '登录失败次数过多，请稍候再试');
        }
    }


    /**
     * 获取登录行为记录
     * @param array $loginfo
     */
    public function getLoginLog($key)
    {
        return $this->request->session()->check($key) ? $this->request->session()->read($key) : null;
    }


    /**
     * 登录行为记录
     * 这里封装起来是因为以后可能会使用到其他存储方式
     * @param array $loginfo
     */
    public function setloginLog($key, $value)
    {
        $this->request->session()->write($key, $value);
    }


    /**
     * 处理登录业务
     * @param array $data
     * @param int $type  #1普通登录 #2微信登录
     */
    public function loginHandle($data = [], $type = 1, $isNative = false)
    {
        //$this->loginFirewallCheck();
        if(!$type) {
            $this->failReturn(GlobalCode::API_OPTIONS, 'parameter of "type" is needed!');
        }
        $type = intval($type);
        switch ($type) {
            case 1 :
                $this->doCommonLogin($data);
                break;
            case 2 :
                break;
            default :
                $this->failReturn(GlobalCode::API_OPTIONS, 'parameter of "type" is invalid!');
        }
    }


    /**
     * 处理普通登录（账号密码方式）
     * @param $data
     */
    public function doCommonLogin($data)
    {
        if(!isset($data['account']) || !isset($data['pwd'])) {
            $this->failReturn(GlobalCode::API_OPTIONS, 'parameter of "account" or "password" is needed!');
        }

        $account = $data['account'];
        $pwd = $data['pwd'];
        $userTb = TableRegistry::get('Suser');
        $user = $userTb->find()->select(['id', 'password'])->where([
            'account' => $account,
            'status' => GlobalCode::COMMON_STATUS_ON,
            'is_del' => GlobalCode::COMMON_STATUS_OFF
        ])->first();
        if($user) {
            if((new DefaultPasswordHasher)->check($pwd, $user->password)) {
                $this->login($user->id);
            } else {
                $this->dealReturn(false, '账号或密码不正确!');
            }
        } else {
            $errorCount = ($this->getLoginLog(self::ADMIN_LOGIN_ERROR_COUNT) !== null) ? $this->getLoginLog(self::ADMIN_LOGIN_ERROR_COUNT) : 0;
            $this->setloginLog(self::ADMIN_LOGIN_ERROR_COUNT, $errorCount + 1);
            $this->failReturn(GlobalCode::API_NO_ACCOUNT, '', '账号不存在!');
        }
    }


    /**
     * 退出登录
     */
    public function doLogout()
    {
        $this->request->session()->write(self::ADMIN_LOGIN_SESSION, '');
        $this->dealReturn(true, '成功退出');
    }


    /**
     * 登录验证成功后需要做的后续操作
     * @param $userId
     */
    protected function login($userId)
    {
        $user = $this->setLoginInfo($userId);
        if (!$user) $this->failReturn(GlobalCode::API_ERROR, '', '登录失败');

        $this->setloginLog(self::ADMIN_LOGIN_ERROR_COUNT, 0);
        $this->response->cookie([
            'name' => 'isLogin',
            'value' => true,
            'expire' => time() + 86400
        ]);
        $this->dealReturn(true, '登录成功', [
            'data' => $user,
            'cb' => '/menu-index',
            'userinfo' => [
                'nick' => $user->nick,
                'avatar' => generateImgUrl('/mobile/images/m_avatar.png')
            ]
        ]);
    }
}
