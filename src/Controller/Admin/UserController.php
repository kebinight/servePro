<?php
namespace App\Controller\Admin;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Database\Exception;
use Cake\ORM\TableRegistry;
use GlobalCode;

/**
 * User Controller
 *
 * @property \App\Model\Table\SuserTable $Suser
 */
class UserController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Suser');
    }


    public function generateUser()
    {
        $users = $this->Suser->find()->toArray();
        if($users) {
            $this->Common->dealReturn(false, '不符合初始化条件');
        }
        $initUser = $this->Suser->newEntity([
            'avatar' => '/admin/imgs/admin_avatar.jpg',
            'account' => 'admin',
            'password' => '123456',
            'nick' => '超级管理员',
            'truename' => '系统默认',
            'gender' => 1,
            'status' => GlobalCode::COMMON_STATUS_ON,
            'is_del' => GlobalCode::COMMON_STATUS_OFF
        ]);

        if($this->Suser->save($initUser)) {
            $this->Common->dealReturn(true, '账号初始化成功');
        }
        $this->Common->failReturn(GlobalCode::API_ERROR, '', '账号生成失败');
    }

    public function index()
    {
        if($this->request->is('POST')) {
            $where = [];
            //只有系统内置超级管理员才能修改系统内置超级管理员信息
            if($this->user->is_super != GlobalCode::COMMON_STATUS_ON) {
                $where['is_super'] = GlobalCode::COMMON_STATUS_OFF;
            }

            $users = $this->Suser->find()->contain(['Srole' => function($q) {
                return $q->where(['status' => GlobalCode::COMMON_STATUS_ON]);
            }])->where($where)
                ->map(function($row) {
                $row->create_time = $row->create_time->i18nFormat('yyyy-MM-dd HH:mm');
                $row->update_time = $row->update_time->i18nFormat('yyyy-MM-dd HH:mm');
                return $row;
            })->toArray();
            $this->Common->dealReturn(true, '', ['users' => $users]);
        }
    }


    public function save()
    {
        if($this->request->is('POST')) {
            $data = $this->request->data;
            if(isset($data['id'])) {
                $user = $this->Suser->get($data['id']);
                $newUser = $this->Suser->patchEntity($user, $data);
            } else {
                $newUser = $this->Suser->newEntity($data);
            }

            //只有系统内置超级管理员才能修改系统内置超级管理员信息
            if(($this->user->is_super != GlobalCode::COMMON_STATUS_ON) && ($newUser->is_super == GlobalCode::COMMON_STATUS_ON)) {
                $this->Common->failReturn(GlobalCode::API_NO_LIMIT, '', '缺乏权限');
            }

            if($this->Suser->save($newUser)) {
                if($this->user->id == $newUser->id) {
                    $this->Common->setLoginInfo($this->user->id);
                }
                $this->Common->dealReturn(true, '操作成功', [ 'reload' => true ]);
            } else {
                $this->Common->dealReturn(false, '操作失败');
            }

        }
    }


    /**
     *  初始化添加/编辑页面数据
     */
    public function saveIndex()
    {
        if($this->request->is(['POST'])) {
            $id = $this->request->data('id');
            $user = '';
            if($id) {
                $user = $this->Suser->get($id, ['contain' => ['Srole' => function($q) {
                    return $q->where(['status' => GlobalCode::COMMON_STATUS_ON]);
                }]]);
            }
            $roleTb = TableRegistry::get('Srole');
            $role = $roleTb->find()->where(['status' => GlobalCode::COMMON_STATUS_ON])->toArray();

            $this->Common->dealReturn(true, '', ['user' => $user, 'roles' => $role]);
        }
    }


    /**
     * 删除
     * @param int $id
     */
    public function delete()
    {
        if($this->request->is(["POST"])) {
            $data = $this->request->data;
            if(isset($data['id'])) {
                $entity = $this->Suser->get($data['id']);
                $result = $this->Suser->delete($entity);
                if($result) {
                    $this->Common->dealReturn(true, '操作成功');
                } else {
                    $this->Common->dealReturn(true, '操作失败');
                }
            } else {
                $this->Common->failReturn();
            }
        }
    }
}
