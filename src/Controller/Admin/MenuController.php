<?php
namespace App\Controller\Admin;
use Cake\ORM\TableRegistry;


/**
 * Menu Controller
 *
 * @property \App\Model\Table\SMenuTable $Menu
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
        $menus = $this->Smenu->find()->where(['status' => 1])->toArray();
        if(!$menus) {
            $menus = [
                [
                    'id' => 1,
                    'name' => '账户管理',
                    'node' => 'user-set',
                    'rank' => 0,
                    'status' => '启用',
                    'admin' => [
                        'name' => '小白'
                    ],
                    'pid' => 0,
                ],
                [
                    'id' => 2,
                    'name' => '菜单管理',
                    'node' => 'menu-set',
                    'rank' => 0,
                    'status' => '启用',
                    'admin' => [
                        'name' => '小白'
                    ],
                    'pid' => 1
                ],
                [
                    'id' => 3,
                    'name' => '基础设置',
                    'node' => 'base-set',
                    'status' => '启用',
                    'rank' => 0,
                    'admin' => [
                        'name' => '小白'
                    ],
                    'pid' => 0,
                ],
                [
                    'id' => 4,
                    'name' => '菜单管理',
                    'node' => 'menu-set',
                    'status' => '启用',
                    'rank' => 0,
                    'admin' => [
                        'name' => '小白'
                    ],
                    'pid' => 3
                ]
            ];
        }
        $this->Common->dealReturn(true, '', ['menu' => $menus]);
    }


    /**
     * 添加/更新菜单信息
     */
    public function save()
    {
        if($this->request->is(["POST", "OPTIONS"])) {
            $data = $this->request->data;

            if(isset($data['id'])) {
                $menu = $this->Smenu->get($data['id']);
                $newMenu = $this->Smenu->patchEntity($menu, $data);
            } else {
                $newMenu = $this->Smenu->newEntity($data);
                $this->Common->dealReturn(true, json_encode($newMenu));
            }

            if($this->Smenu->save($newMenu)) {
                $this->Common->dealReturn(true, '操作成功');
            } else {
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
