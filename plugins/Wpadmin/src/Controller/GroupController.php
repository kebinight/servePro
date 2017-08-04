<?php

namespace Wpadmin\Controller;

use Wpadmin\Controller\AppController;

/**
 * Group Controller
 *
 * @property \Admin\Model\Table\GroupTable $Group
 */
class GroupController extends AppController {

    /**
     * Index method
     *
     * @return void
     */
    public function index() {
        $this->set('group', $this->Group);
        $this->set([
            'pageTitle' => '群组管理',
            'bread' => [
                'first' => ['name' => '系统管理'],
                'second' => ['name' => '群组管理'],
            ],
        ]);
    }

    /**
     * View method
     *
     * @param string|null $id Group id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null) {
        $this->viewBuilder()->autoLayout(false);
        $group = $this->Group->get($id, [
            'contain' => ['menu']
        ]);
        $this->set('group', $group);
        $this->set('_serialize', ['group']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $group = $this->Group->newEntity();
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');
            $group = $this->Group->patchEntity($group, $this->request->data);
            if ($this->Group->save($group)) {
                echo json_encode(array('status' => true, 'msg' => '添加成功'));
            } else {
                $errors = $group->errors();
                echo json_encode(array('status' => false, 'msg' => getMessage($errors), 'errors' => $errors));
            }
            return;
        }
        $menu = $this->Group->menu->find('list', ['limit' => 200]);
        $this->set(compact('group', 'menu'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Group id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $group = $this->Group->get($id, [
            'contain' => ['menu']
        ]);
        if ($this->request->is(['post', 'put'])) {
            $this->autoRender = false;
            $this->response->type('json');
            $group = $this->Group->patchEntity($group, $this->request->data);
            if ($this->Group->save($group)) {
                echo json_encode(array('status' => true, 'msg' => '修改成功'));
            } else {
                $errors = $group->errors();
                echo json_encode(array('status' => false, 'msg' => getMessage($errors)));
            }
            return;
        }
        $menu = $this->Group->menu->find('list', ['limit' => 200]);
        $this->set(compact('group', 'menu'));
        $this->set([
            'pageTitle' => '群组编辑',
            'bread' => [
                'first' => ['name' => '群组管理'],
                'second' => ['name' => '群组编辑'],
            ],
        ]);
    }

    /**
     * Delete method
     *
     * @param string|null $id Group id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod('post');
        $id = $this->request->data('id');
        if ($this->request->is('post')) {
            $this->autoRender = false;
            $this->response->type('json');
            $group = $this->Group->get($id);
            if ($this->Group->delete($group)) {
                echo json_encode(array('status' => true, 'msg' => '删除成功'));
            } else {
                $errors = $group->errors();
                echo json_encode(array('status' => false, 'msg' => getMessage($errors)));
            }
        }
        return;
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
            $where[' username like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`ctime`) >' => $begin_time], ['date(`ctime`) <' => $end_time]];
        }
        $query = $this->Group->find();
        $query->hydrate(false);
        if (!empty($where)) {
            $query->where($where);
        }
        $nums = $query->count();
        $query->contain(['menu']);
        if (!empty($sort) && !empty($order)) {
            $query->order([$sort => $order]);
        }

        $query->limit(intval($rows))
                ->page(intval($page));
        $res = $query->toArray();
        if (empty($res)) {
            $res = array();
        }
        if ($nums > 0) {
            $total_pages = ceil($nums / $rows);
        } else {
            $total_pages = 0;
        }
        $data = array('page' => $page, 'total' => $total_pages, 'records' => $nums, 'rows' => $res);
        $this->autoRender = false;
        $this->response->type('json');
        echo json_encode($data);
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
            $where[' username like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`ctime`) >' => $begin_time], ['date(`ctime`) <' => $end_time]];
        }
        $Table = $this->Group;
        $column = ['群组名称', '备注', '创建时间', '修改时间'];
        $query = $Table->find();
        $query->hydrate(false);
        $query->select(['name', 'remark', 'ctime', 'utime']);
        if (!empty($where)) {
            $query->where($where);
        }
        if (!empty($sort) && !empty($order)) {
            $query->order([$sort => $order]);
        }
        $res = $query->toArray();
        $this->autoRender = false;
        $filename = 'Group_' . date('Y-m-d') . '.csv';
        \Admin\Utils\Export::exportCsv($column, $res, $filename);
    }

}
