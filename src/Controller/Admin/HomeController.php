<?php
namespace App\Controller\Admin;


use Cake\ORM\TableRegistry;

class HomeController extends AppController
{
    /**
    * Index method
    * @return void
    */
    public function index()
    {
        $this->Common->dealReturn(true, 'hello world');
    }


    /**
     * 获取菜单列表
     */
    public function getMenu()
    {
        $menuTb = TableRegistry::get('Smenu');
        $menus = $menuTb->find('threaded')->where(['status' => 1])->toArray();
        if(!$menus) {
            $menus = [
                [
                    'icon' => 'el-icon-menu',
                    'title' => '表格',
                    'index' => '1',
                    'subs' => [
                        [
                            'title' => '基础表格',
                            'index' => 'basetable'
                        ],
                        [
                            'title' => 'vue表格组件',
                            'index' => 'vuetable'
                        ]
                    ]
                ],
                [
                    'icon' => 'el-icon-date',
                    'title' => '其他',
                    'index' => '2',
                    'subs' => [
                        [
                            'title' => '基础表格',
                            'index' => 'basetable'
                        ],
                        [
                            'title' => 'vue表格组件',
                            'index' => 'vuetable'
                        ]
                    ]
                ]
            ];
        }
        $this->Common->dealReturn(true, '', ['menu' => $menus]);
    }


}
