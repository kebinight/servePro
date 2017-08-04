<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller\Mobile;

use App\Controller\Component\CommonComponent;
use App\Pack\Aitask;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 * @property \App\Controller\Component\UtilComponent $Util
 * @property \App\Controller\Component\WxComponent $Wx
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    
    /**
     * 无需验证登录的action
     * @var array 
     */
    private $firewall;
    protected $user;

    /**
     * 登录坐标
     * @var string type
     */
    protected $coord;

    public function initialize() {
        parent::initialize();
        $this->viewBuilder()->layout('layout');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Business');
        $this->loadComponent('Common');
        $this->loadComponent('Flash');
        $this->loadComponent('Push');
        //无需登录的模块，格式为[controller名, action名]
        //无需登录的模块，格式为[controller名, action名]
        //不指定表示要检测
        //*代表全部不检测
        //可指定特定key,表示指定不检测
        //-key表示指定要检测
        $this->firewall = [
            'Index' => ['*'],
        ];
    }


    /**
     * 进入action前进行相关操作
     * 1.检查登录态及登录态处理
     * 2.
     * @param Event $event
     */
    public function beforeFilter(Event $event) {
        $this->checkLogin();  //自动登录并检测登陆

        $defaultPosition = implode(',', getDefalutPosition());
        $this->coord = $this->request->cookie('coord')?$this->request->cookie('coord') : $defaultPosition;

        //更新用户登录信息
        if($this->user){
            $curtimestamp = time();
            $lastLtime = $this->user->login_time;
            $lastLtimestamp = (new Date($lastLtime))->timestamp;
            $lastSessionTimestamp = $this->Common->getLoginUpdateTimestamp();

            //是否需要跳转到打招呼页面
            if(!$this->request->is('lemon') && !$this->request->cookie('view_say_hi')) {
                $this->loadComponent('Cookie');
                $this->Cookie->config([
                    'expires' => '+1 days',
                    'httpOnly' => true
                ]);
                $this->Cookie->write('view_say_hi', true);
                $this->redirect('/index/sayHi');
            }

            //一分钟更新一次session登录信息
            if(!$lastSessionTimestamp || (($curtimestamp - $lastSessionTimestamp) >= 1 * 60)) {
                $this->Common->updateLoginInfo();
            }

            //24小时更新用户登录信息
            if(($curtimestamp - $lastLtimestamp) > 24 * 60 * 60) {
                $login_time = date('Y-m-d H:i:s');
                $this->user->login_time = $login_time;
                $coord = $this->getPosition();
                if($coord){
                    if(!isDefaultPosition($coord)) {
                        $this->user->login_coord_lng = $coord[0];
                        $this->user->login_coord_lat = $coord[1];
                    } else {
                        //如果获取不到用户当前坐标，则尝试把用户上次登录坐标赋值给coord
                        $this->coord = $this->user->login_coord_lng . ',' . $this->user->login_coord_lat;
                    }
                    $UserTable = TableRegistry::get('User');
                    $UserTable->save($this->user);
                }
            }

            $aiTask = new Aitask();
            $aiTask->addTask($this->user);
        }
    }


    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event) {
        $wxConfig = [];
        if ($this->request->is('weixin')) {
            $this->loadComponent('Wx');
            $wxConfig = $this->Wx->wxconfig(['onMenuShareTimeline', 'onMenuShareAppMessage', 'scanQRCode',
                'chooseImage', 'uploadImage', 'previewImage','getLocation','openLocation'], false);
        }
        $isLogin = 'no';
        if ($this->user) {
            $isLogin = 'yes';
        }
        $this->set(compact('isLogin'));
        $this->set(compact('wxConfig'));
        if (!array_key_exists('_serialize', $this->viewVars) &&
                in_array($this->response->type(), ['application/json', 'application/xml'])) {
            $this->set('_serialize', true);
        }

        if ($this->request->is('lemon')) {
            $this->set('isApp', true);
        }
        if ($this->request->is('weixin')) {
            $this->set('isWx', true);
        }
    }


    /**
     * 检查用户登录
     * @return {{}} type
     */
    private function checkLogin() {
        $controller = $this->request->param('controller') ? $this->request->param('controller') : '*';
        $action = $this->request->param('action') ? $this->request->param('action') : '*';

        //app的静默登录
        $this->user = $this->Common->getLoginer();
        $this->baseLogin();

        //debug(isset($this->firewall[$controller]));die;

        //无需登录的模块直接放行
        if(isset($this->firewall[$controller])) {
            $pActions = $this->firewall[$controller];
            if(is_array($pActions)) {
                $filterActionTmp = $action ? '-' . $action : '';
                if(!in_array($filterActionTmp, $pActions) && (in_array('*', $pActions) || in_array($action, $pActions))) {
                    return true;
                }
            }
        }

        return $this->handCheckLogin();
    }


    /**
     * APP静默登录
     * 原理：app把token_uin注入到webview的cookie中，这里利用这个cookie进行静默登录
     * 缺陷：安全性有待研究与提高
     */
    protected function baseLogin() {
        if (!$this->Common->checkLogin() && $this->request->is('lemon') && $this->request->cookie('token_uin')) {
            $UserTable = TableRegistry::get('User');
            //如果是APP，获取user_token 自动登录
            $user_token = $this->request->cookie('token_uin');
            $user = $UserTable->find()->where(['user_token' => $user_token, 'enabled' => 1, 'is_del' => 0])->first();
            //debug($user_token);tmpLog($user_token);die;
            if ($user) {
                $this->user = $user;
                $this->Common->setLogin($user);
            }
        }
    }


    /**
     * 处理检测登陆
     */
    protected function handCheckLogin() {
        $this->Common->checkLoginHand();
    }


    /**
     * 获取坐标
     */
    protected function getPosition() {
        $position = explode(',', $this->coord);
        return $position;
    }
}
