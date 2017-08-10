<?php
namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use GlobalCode;

/**
 * Role Controller
 *
 * @property \App\Model\Table\SroleTable $Srole
 */
class RoleController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Srole');
    }

    public function index()
    {
        $roles = $this->Srole->find()
            ->contain(['Slimit' => function($q) {
                    return $q->where(['status' => GlobalCode::COMMON_STATUS_ON, 'parent_id !=' => 0]);
                }
            ])->map(function($row) {
                $row->create_time = $row->create_time->i18nFormat('yyyy-MM-dd HH:mm');
                $row->update_time = $row->update_time->i18nFormat('yyyy-MM-dd HH:mm');
                return $row;
            })->toArray();
        if(!$roles) {
            $roles = [
                [
                    'id' => 1,
                    'name' => '超级管理员',
                    'rank' => 0,
                    'status' => 1,
                    'remark' => '拥有所有操作权限'
                ],
                [
                    'id' => 2,
                    'name' => '普通管理员',
                    'rank' => 0,
                    'status' => 1,
                    'remark' => '拥有部分操作权限'
                ]
            ];
        }
        $this->Common->dealReturn(true, '', ['roles' => $roles]);
    }


    /**
     *  初始化页面数据
     */
    public function editIndex()
    {
        if($this->request->is(['POST'])) {
            $id = $this->request->data('id');
            $role = '';
            if($id) {
                $role = $this->Srole->get($id, ['contain' => ['Slimit' => function($q) {
                    return $q->where(['status' => GlobalCode::COMMON_STATUS_ON]);
                }]]);
            }
            $limitTb = TableRegistry::get('Slimit');
            $limits = $limitTb->find('threaded')->where(['status' => GlobalCode::COMMON_STATUS_ON])->toArray();

            $this->Common->dealReturn(true, '', ['role' => $role, 'limits' => $limits]);
        }
    }


    public function getRoleById()
    {
        if($this->request->is(['POST'])) {
            $id = $this->request->data('id');
            $role = $this->Srole->get($id);
            $this->Common->dealReturn(true, '', ['role' => $role]);
        }
    }


    /**
     * 添加/更新权限信息
     */
    public function save()
    {
        if($this->request->is(["POST"])) {
            $data = $this->request->data;
            if(isset($data['id'])) {
                $role = $this->Srole->get($data['id']);
                $newRole = $this->Srole->patchEntity($role, $data, [
                    'associated' => ['Slimit']
                ]);
            } else {
                $newRole = $this->Srole->newEntity($data,  [
                    'associated' => ['Slimit']
                ]);
            }

            if($this->Srole->save($newRole)) {
                $this->Common->dealReturn(true, '操作成功');
            } else {
                $this->Common->dealReturn(false, '操作失败');
            }
        }
    }
}
