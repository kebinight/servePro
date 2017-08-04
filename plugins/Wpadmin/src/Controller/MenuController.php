<?php

namespace Wpadmin\Controller;

use Wpadmin\Controller\AppController;

/**
 * Menu Controller
 *
 * @property \Admin\Model\Table\MenuTable $Menu
 */
class MenuController extends AppController {

    /**
     * Index method
     *
     * @return void
     */
    public function index() {
        $this->set([
            'menu' =>$this->Menu,
            'pageTitle' => '菜单管理',
            'bread' => [
                'first' => ['name' => '系统管理'],
                'second' => ['name' => '菜单管理'],
            ],
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id Menu id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null) {
        $this->viewBuilder()->autoLayout(false);
        $menu = $this->Menu->find('threaded', [
            'keyField' => $this->Menu->primaryKey(),
            'parentField' => 'pid'
        ]);
        $menus = $menu->toArray();
//        var_dump($menus);exit();
        echo count($menus[0]->children);
        var_dump($menus[0]->children[0]->name);
        exit();
        $this->set('menu', $menu);
        $this->set('_serialize', ['menu']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $menu = $this->Menu->newEntity();
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');
            $menu = $this->Menu->patchEntity($menu, $this->request->data);
            if ($this->Menu->save($menu)) {
                \Cake\Cache\Cache::delete('admin_menus'); //更新菜单缓存文件
                echo json_encode(array('status' => true, 'msg' => '添加成功'));
            } else {
                $errors = $menu->errors();
                echo json_encode(array('status' => false, 'msg' => getMessage($errors), 'errors' => $errors));
            }
            return;
        }
        $menus = $this->Menu->find()->hydrate(false)->all()->toArray();
        $menus = \Wpadmin\Utils\Util::tree($menus, 0, 'id', 'pid');
        $this->set(array(
            'menu' => $menu,
            'menus' => $menus,
            'pageTitle' => '菜单管理',
            'bread' => [
                'first' => ['name' => '系统管理'],
                'second' => ['name' => '菜单管理'],
            ],
        ));
    }

    /**
     * Edit method
     *
     * @param string|null $id Menu id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $menu = $this->Menu->get($id);
        if ($this->request->is(['post', 'put'])) {
            $this->autoRender = false;
            $this->response->type('json');
            $menu = $this->Menu->patchEntity($menu, $this->request->data);
            if ($this->Menu->save($menu)) {
                \Cake\Cache\Cache::delete('admin_menus');
                echo json_encode(array('status' => true, 'msg' => '修改成功'));
            } else {
                $errors = $menu->errors();
                echo json_encode(array('status' => false, 'msg' => getMessage($errors)));
            }
            return;
        }
        $menus = $this->Menu->find()->hydrate(false)->all()->toArray();
        $menus = \Wpadmin\Utils\Util::tree($menus, 0, 'id', 'pid');
        $this->set(array(
            'menu' => $menu,
            'menus' => $menus
        ));
    }

    /**
     * Delete method
     *
     * @param string|null $id Menu id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod('post');
        $id = $this->request->data('id');
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');
            $menu = $this->Menu->get($id);
            if ($this->Menu->delete($menu)) {
                \Cake\Cache\Cache::delete('admin_menus');
                echo json_encode(array('status' => true, 'msg' => '删除成功'));
            } else {
                $errors = $menu->errors();
                debug($errors);
                echo json_encode(array('status' => false, 'msg' => getMessage($errors)));
            }
        }
        exit();
    }

    /**
     * get jqgrid data 
     *
     * @return json
     */
    public function getDataList() {
        $this->request->allowMethod('ajax');
        $page = $this->request->data('page');
        $rows = $this->request->data('rows');
        $sort = $this->request->data('sidx');
        $order = $this->request->data('sord');
        $keywords = $this->request->data('keywords');
        $begin_time = $this->request->data('begin_time');
        $end_time = $this->request->data('end_time');
        $where = [];
        if (!empty($keywords)) {
            $where[' name like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`ctime`) >' => $begin_time], ['date(`ctime`) <' => $end_time]];
        }
        $res = $this->getJsonForJqrid($page, $rows, '', $sort, $order, $where);
        $this->autoRender = false;
        $this->response->type('json');
        $this->response->body(json_encode($res));
        $this->response->send();
        $this->response->stop();
    }

    /**
     * export csv
     *
     * @return csv 
     */
    public function exportExcel() {
        $sort = $this->request->data('sidx');
        $order = $this->request->data('sord');
        $keywords = $this->request->data('keywords');
        $begin_time = $this->request->data('begin_time');
        $end_time = $this->request->data('end_time');
        $where = [];
        if (!empty($keywords)) {
            $where[' name like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`ctime`) >' => $begin_time], ['date(`ctime`) <' => $end_time]];
        }
        $Table = $this->Menu;
        $column = ['节点名称', '路径', '父ID', '样式', '排序', '是否在菜单显示', '状态', '备注'];
        $query = $Table->find();
        $query->hydrate(false);
        $query->select(['name', 'node', 'pid', 'class', 'rank', 'is_menu', 'status', 'remark']);
        if (!empty($where)) {
            $query->where($where);
        }
        if (!empty($sort) && !empty($order)) {
            $query->order([$sort => $order]);
        }
        $res = $query->toArray();
        $this->autoRender = false;
        $filename = 'Menu_' . date('Y-m-d') . '.csv';
        \Wpadmin\Utils\Export::exportCsv($column, $res, $filename);
    }

}
