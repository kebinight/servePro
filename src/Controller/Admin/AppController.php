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

use App\Pack\Aitask;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\Date;
use Cake\ORM\TableRegistry;

/**
 * Application Controller
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
     * @var type
     */
    protected $coord;

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
        //无需登录的模块，格式为[controller名, action名]
        //不指定表示要检测
        //*代表全部不检测
        //可指定特定key,表示指定不检测
        //-key表示指定要检测
        $this->firewall = [
            'Userc' => ['login'],
            /*'Home' => ['*'],
            'Menu' => ['*'],
            'Slimit' => ['*'],
            'Role' => ['*'],*/
            'User' => ['generateUser'],
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
        $rq_controller = $this->request->param('controller') ? $this->request->param('controller') : '';
        $rq_action = $this->request->param('action');
        $this->user = $this->Common->getLoginer();

        //无需登录的模块直接放行
        if(isset($this->firewall[$rq_controller])) {
            $all_Actions = $this->firewall[$rq_controller];
            if(is_array($all_Actions)) {
                $filterActionTmp = isset($rq_action) ? '-' . $rq_action : '';
                if(!in_array($filterActionTmp, $all_Actions)  || in_array($rq_action, $all_Actions)) {
                    return true;
                }
            } else if($all_Actions == '*') {
                return true;
            }
        }

        return $this->handleCheckLogin();
    }


    /**
     * 检查权限
     */
    protected function checkLimit()
    {
        //$this->common->dealReturn();
    }

    /**
     * 处理检测登陆
     */
    protected function handleCheckLogin()
    {
        $this->Common->handleCheckLogin();
    }
}
