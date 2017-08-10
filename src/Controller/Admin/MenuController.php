<?php
namespace App\Controller\Admin;
use Cake\ORM\TableRegistry;
use GlobalCode;


/**
 * Menu Controller
 *
 * @property \App\Model\Table\SMenuTable $Smenu
 */
class MenuController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Smenu');
    }

    /**
     * 获取菜单列表
     */
    public function getMenu()
    {
        $menus = $this->Smenu->find('threaded')->where(['status' => 1])->toArray();
        if(!$menus) {
            $menus = [
                [
                    'name' => '基础设置',
                    'node' => 'base-set',
                    'subs' => [
                        [
                            'name' => '菜单管理',
                            'node' => 'menu-index',
                        ]
                    ]
                ],
                [
                    'name' => '系统设置',
                    'node' => 'user-set',
                    'subs' => [
                        [
                            'name' => '账号管理',
                            'node' => 'user-index'
                        ],
                        [
                            'name' => '角色管理',
                            'node' => 'role-index'
                        ],
                        [
                            'name' => '权限管理',
                            'node' => 'limit-index'
                        ]
                    ]
                ]
            ];
        }
        $this->Common->dealReturn(true, '', ['menu' => $menus]);
    }


    public function index()
    {
        $menus = $this->Smenu->find()->where(['status' => 1])->map(function($row) {
            $row->create_time = $row->create_time->i18nFormat('yyyy-MM-dd HH:mm');
            $row->update_time = $row->update_time->i18nFormat('yyyy-MM-dd HH:mm');
            return $row;
        })->toArray();
        $this->Common->dealReturn(true, '', ['menu' => $menus]);
    }


    /**
     *  初始化添加/编辑页面数据
     */
    public function saveIndex()
    {
        if($this->request->is(['POST'])) {
            $id = $this->request->data('id');
            $menu = '';
            if($id) {
                $menu = $this->Smenu->get($id, ['contain' => ['Srole' => function($q) {
                    return $q->where(['status' => GlobalCode::COMMON_STATUS_ON]);
                }]]);
            }
            $roleTb = TableRegistry::get('Srole');
            $role = $roleTb->find()->where(['status' => GlobalCode::COMMON_STATUS_ON])->toArray();

            $rootMenu = $this->Smenu->find()->where(['status' => GlobalCode::COMMON_STATUS_ON, 'parent_id' => 0])->toArray();

            $this->Common->dealReturn(true, '', ['menu' => $menu, 'roles' => $role, 'rootMenus' => $rootMenu]);
        }
    }

    /**
     * 添加/更新菜单信息
     */
    public function save()
    {
        if($this->request->is(["POST"])) {
            $data = $this->request->data;
            //$this->Common->dealReturn(true, json_encode($data));
            if(isset($data['id'])) {
                $menu = $this->Smenu->get($data['id']);
                $newMenu = $this->Smenu->patchEntity($menu, $data);
            } else {
                $newMenu = $this->Smenu->newEntity($data);
            }

            if($this->Smenu->save($newMenu)) {
                $this->Common->dealReturn(true, '操作成功');
            } else {
                debug($newMenu);
                $this->Common->dealReturn(false, '操作失败');
            }
        }
    }


    /**
     * Delete method
     *
     * @param string|null $id Menu id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $menu = $this->Menu->get($id);
        if ($this->Menu->delete($menu)) {
            $this->Flash->success(__('The menu has been deleted.'));
        } else {
            $this->Flash->error(__('The menu could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
