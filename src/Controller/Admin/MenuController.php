<?php
namespace App\Controller\Admin;
use Cake\ORM\TableRegistry;
use GlobalCode;


/**
 * Menu Controller
 *
 * @property \App\Model\Table\SMenuTable $Smenu
 * @property \App\Controller\Component\CommonComponent $Common
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
        if($this->request->is(['POST'])) {
            $user = $this->user;
            if($user) {

            }
            $menus = $this->Smenu->find('threaded')->where(['status' => 1])->toArray();
            if(!$menus) {
                $initMenu = $this->Smenu->newEntities([
                    [
                        'name' => '系统设置',
                        'node' => 'base-set',
                        'parent_id' => 0,
                        'rank' => 0,
                        'status' => 1,
                        'remark' => '系统自动初始化基础设置菜单',
                        'children' => [
                            [
                                'name' => '权限管理',
                                'node' => 'limit-index',
                                'parent_id' => 0,
                                'rank' => 0,
                                'status' => 1,
                                'remark' => '系统自动初始化权限管理菜单',
                            ],
                            [
                                'name' => '角色管理',
                                'node' => 'role-index',
                                'parent_id' => 0,
                                'rank' => 0,
                                'status' => 1,
                                'remark' => '系统自动初始化角色管理菜单',
                            ],
                            [
                                'name' => '管理员管理',
                                'node' => 'user-index',
                                'parent_id' => 0,
                                'rank' => 0,
                                'status' => 1,
                                'remark' => '系统自动初始化管理员管理菜单',
                            ],
                            [
                                'name' => '菜单管理',
                                'node' => 'menu-index',
                                'parent_id' => 0,
                                'rank' => 0,
                                'status' => 1,
                                'remark' => '系统自动初始化基础设置菜单',
                            ]
                        ]
                    ]
                ]);
                if($res = $this->Smenu->saveMany($initMenu, ['associated' => ['Children']])) {
                    $this->Common->dealReturn(true, '', ['menu' => $initMenu]);
                } else {
                    $this->Common->failReturn(GlobalCode::API_ERROR, '数据库操作有问题，请检查', '菜单初始化失败');
                }
            }
            $this->Common->dealReturn(true, '', ['menu' => $menus]);
        }
    }


    public function index()
    {
        $menus = $this->Smenu->find()->where(['status' => 1])->contain([
            'Srole' => function($q) {
                return $q->where(['status' => GlobalCode::COMMON_STATUS_ON]);
            }
        ])->map(function($row) {
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
     * 删除
     * @param int $id
     */
    public function delete()
    {
        if($this->request->is(["POST"])) {
            $data = $this->request->data;
            if(isset($data['id'])) {
                $entity = $this->Smenu->get($data['id']);
                $result = $this->Smenu->delete($entity);
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
