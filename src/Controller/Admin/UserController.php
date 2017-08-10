<?php
namespace App\Controller\Admin;
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

    public function index()
    {
        if($this->request->is('POST')) {
            $users = $this->Suser->find()->map(function($row) {
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

            if($this->Suser->save($newUser)) {
                $this->Common->dealReturn(true, '操作成功');
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
}
