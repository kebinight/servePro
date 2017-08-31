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

namespace App\Controller\Admin;

use Cake\Controller\Controller;
use Cake\Event\Event;
use GlobalCode;

/**
 * Application Controller
 * @property \App\Controller\Component\WxComponent $Wx
 * @property \App\Controller\Component\CommonComponent $Common
 */
class AppController extends Controller {

    /**
     * 无需验证登录的action
     * @var array
     */
    private $loginCheckList;   //登录检查列表，默认全部需要登录后才能访问
    private $firewall;         //权限检查列表，默认全部需要检查权限
    protected $user;

    /**
     * 登录坐标
     */
    protected $coord;

    /**
     * 请求的controller和action
     */
    protected $rq_controller;
    protected $rq_action;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize() {
        parent::initialize();
        $this->viewBuilder()->layout('layout');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Business');
        $this->loadComponent('Common');
        $this->loadComponent('Flash');
        $this->loadComponent('Push');

        $this->rq_controller = $this->request->param('controller') ? $this->request->param('controller') : '';
        $this->rq_action = $this->request->param('action');

        //无需登录的模块，格式为[controller名, action名]
        //不指定表示要检测
        //可指定特定key,表示指定不检测
        //存在 "-" 则表示反过来，即有填写的要检测，没有填写的就不检测（默认是有填写的不检测，没有填写的就检测）
        $this->loginCheckList = [
            'Userc' => ['login'],
            /*'Home' => ['*'],
            'Menu' => ['*'],
            'Slimit' => ['*'],
            'Role' => ['*'],*/
            'User' => ['generateUser'],
        ];

        //无需权限检查的模块
        //配置方法同loginCheckList
        $this->firewall = [
            'Menu' => ['getMenu'],
            'Userc' => ['login', 'logout']
        ];
    }


    /**
     * 进入action前进行相关操作
     * 1.检查登录态及登录态处理
     * 2.
     * @param Event $event
     */
    public function beforeFilter(Event $event) {
        if($this->request->is('OPTIONS')) {
            return $this->Common->dealReturn();
        }
        $this->checkLogin();  //自动登录并检测登陆
        $this->checkLimit();  //检查用户权限

        //更新用户登录信息
        /*if($this->user){
            $curtimestamp = time();
            $lastLtime = $this->user->login_time;
            $lastLtimestamp = (new Date($lastLtime))->timestamp;
            $lastSessionTimestamp = $this->Common->getLoginUpdateTimestamp();

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
        }*/
    }


    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event) {
        //do something
    }


    /**
     * 检查用户登录
     * @return {{}} type
     */
    private function checkLogin() {

        //无需登录的模块直接放行
        if(isset($this->loginCheckList[$this->rq_controller])) {
            $all_Actions = $this->loginCheckList[$this->rq_controller];
            if(is_array($all_Actions)) {
                //如果存在-则检测规则反过来
                if(in_array('-', $all_Actions)) {
                    if(!in_array($this->rq_action, $all_Actions)) {
                        return true;
                    }
                } else {
                    if(in_array($this->rq_action, $all_Actions)) {
                        return true;
                    }
                }
            } else if('*' == $all_Actions){
                return true;
            }
        }
        $this->handleCheckLogin();
        $this->user = $this->Common->getLoginer();

    }


    /**
     * 检查权限
     */
    protected function checkLimit()
    {
        $user_limits = $this->Common->getLoginSession('user_limits');
        tmpLog('权限检查开始：' . $this->rq_controller . '|' . $this->rq_action);

        //对首页index均不进行检查
        if(isset($this->firewall[$this->rq_controller])) {
            //所有具有controller权限的用户均可以访问其index页面
            tmpLog(stripos($this->rq_action, 'index'));
            if(stripos($this->rq_action, 'index') !== false) {
                return true;
            }

            //对防火墙列表进行检查
            $all_Actions = $this->firewall[$this->rq_controller];
            if(is_array($all_Actions)) {
                //如果存在-则检测规则反过来
                if(in_array('-', $all_Actions)) {
                    if(!in_array($this->rq_action, $all_Actions)) {
                        return true;
                    }
                } else {
                    if(in_array($this->rq_action, $all_Actions)) {
                        return true;
                    }
                }
            } else if('*' == $all_Actions){
                return true;
            }
        }

        if(isset($user_limits[$this->rq_controller])) {
            $action_limits = $user_limits[$this->rq_controller]['children'];
            //每个action的首页index都免权限
            foreach ($action_limits as $key => $action_limit) {
                if(stripos($this->rq_action, $key) !== false) {
                    return true;
                }
            }
            if(!isset($action_limits[$this->rq_action])) {
                $this->Common->failReturn(GlobalCode::API_NO_LIMIT, '', '非法访问');
            }
        }
        $this->Common->failReturn(GlobalCode::API_NO_LIMIT, '', '非法访问');
    }


    /**
     * 处理检测登陆
     */
    protected function handleCheckLogin()
    {
        $this->Common->handleCheckLogin();
    }
}
