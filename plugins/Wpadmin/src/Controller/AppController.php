<?php

namespace Wpadmin\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * @property  \Wpadmin\Controller\Component\UtilComponent $Util 开发通用组件
 */
class AppController extends Controller {

    protected $_user;

    /**
     * 无需验证登录的action
     * @var array 
     */
    private $firewall;

    /**
     * 无需权限检测的actin
     * @var type 
     */
    private $noAcl;

    public function initialize() {
        $this->loadComponent('Flash');
        $this->loadComponent('Business');
        $this->loadComponent('Common');
        $this->loadComponent('Wpadmin.Util');
        $this->firewall = array(
            ['admin', 'login'],
            ['util','doupload'],
            ['report','logger']
        );
        $this->noAcl = array(
            ['admin', 'login'],
            ['index', 'index'],
            ['index', ''],
        );
    }

    public function beforeFilter(Event $event) {
       $this->checkLogin();
       $this->autoLog();
    }

    public function beforeRender(\Cake\Event\Event $event) {
        $this->viewBuilder()->layout('Wpadmin.layout');
        $this->viewBuilder()->className('Wpadmin.App');
    }

    /**
     * 自动日志
     */
    protected function autoLog() {
        $action = strtolower($this->request->param('action'));
        if (in_array($action, ['add','edit', 'delete', 'exportexcel'])) {
            \Cake\Log\Log::debug($this->request->method());
            if($this->request->is(['post', 'put'])){
                $msgArray = ['add'=>'添加','edit'=>'修改','delete'=>'删除','exportexcel'=>'导出excel'];
                $msg = $msgArray[$action];
                $user = $this->_user->username;
                $this->Util->actionLog($msg,$user);
            }
        }
    }

    /**
     *  检测登录
     * @return boolean
     */
    protected function checkLogin() {
        $controller = strtolower($this->request->param('controller'));
        $action = strtolower($this->request->param('action'));
        $request_aim = [$controller, $action];
        if (in_array($request_aim, $this->firewall)) {
            return true;
        }
        $admin = $this->request->session()->check('User.admin');
        if (!$admin) {
            $this->failAccess('请重新登录!');
            $this->redirect(['controller' => 'admin', 'action' => 'login']);
            return false;
        }
        // return;
        $this->_user = $this->request->session()->read('User.admin');
        return true;
        if (in_array($request_aim, $this->noAcl) || $this->_user->username == 'admin') {
            //无需检测权限的action 直接pass
            return true;
        }
        $userGroups = $this->_user->g;  //用户所属组
        $request_node = '/admin/' . $controller . '/' . $action;
        if (!$userGroups) {
            $this->failAccess('您没有足够权限!');
            return false;
        }
        $flag = false;  //权限标识
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if (!$group->menu) {
                    $this->failAccess('您没有足够权限!');
                    return;
                } else {
                    foreach ($group->menu as $menu) {
                        if (strtolower($menu->node) == $request_node) {
                            $flag = TRUE;
                            break;
                        }
                    }
                }
            }
        }
        if (!$flag) {
            $this->failAccess('您没有足够权限!');
        }
        return $admin;
    }

    protected function failAccess($mesage) {
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->response->type('json');
            echo json_encode(['status' => false, 'msg' => $mesage]);
            exit();
        }
        // $this->log(__FILE__);
        //echo '无权限';
        $this->set('NO_PERMISSION', TRUE);
        $this->Flash->error($mesage, ['key' => 'acl']);
    }

    /**
 * 返回jqgrid 所需的json 数据格式
 * jqgrid 数据表格jquery  插件 @link http://www.trirand.com/blog/?page_id=15 jqgrid 官网
 * @param type $page 页码
 * @param type $limit 每页显示行数
 * @param type $modelName 表名
 * @param type $sort 排序字段
 * @param type $order 排序形式
 * @param type $where 查询条件
 * @return array total 总页数 page 当前页码 records 总记录数 rows 数据数组
 *
 */
    protected function getJsonForJqrid($page, $limit, $modelName = '', $sort = '', $order = '', $where = [], $contain = [], $select = [], $func = null)
    {
        $Table = TableRegistry::get(!empty($modelName) ? $modelName : $this->modelClass);
        $query = $Table->find();
        $query->hydrate(false);
        if(!empty($select)) {
            $query->select($select);
        }
        if(!empty($contain)) {
            $query->contain($contain);
        }
        if (!empty($where)) {
            $query->where($where);
        }
        $nums = $query->count();
        if (!empty($sort) && !empty($order)) {
            $query->order([$sort => $order]);
        }
        if($func) {
            $query->formatResults(function($row) use($func){
                return $row->map($func);
            });
        }
        $query->limit(intval($limit))
            ->page(intval($page));
        $res = $query->toArray();
        if (empty($res)) {
            $res = array();
        }
        if ($nums > 0) {
            $total_pages = ceil($nums / $limit);
        } else {
            $total_pages = 0;
        }
        $arr = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $nums,
            'rows' => $res
        );
        return $arr;
    }


    /**
     * 返回jqgrid 所需的json 数据格式
     * jqgrid 数据表格jquery  插件 @link http://www.trirand.com/blog/?page_id=15 jqgrid 官网
     * @param type $page 页码
     * @param type $limit 每页显示行数
     * @param type $modelName 表名
     * @param type $order 排序形式
     * @param type $where 查询条件
     * @return array total 总页数 page 当前页码 records 总记录数 rows 数据数组
     *
     */
    protected function getJsonForJqrid2($page,$limit,$modelName = '', $order = [], $where = '',$contain = '')
    {
        $Table = TableRegistry::get(!empty($modelName) ? $modelName : $this->modelClass);
        $query = $Table->find();
        $query->hydrate(false);
        if(!empty($contain)) {
            $query->contain($contain);
        }
        if (!empty($where)) {
            $query->where($where);
        }
        $nums = $query->count();
        $query->order($order);
        $query->limit(intval($limit))
            ->page(intval($page));
        $res = $query->toArray();
        if (empty($res)) {
            $res = array();
        }
        if ($nums > 0) {
            $total_pages = ceil($nums / $limit);
        } else {
            $total_pages = 0;
        }
        $arr = array(
            'page' => $page,
            'total' => $total_pages,
            'records' => $nums,
            'rows' => $res
        );
        return $arr;
    }

}
