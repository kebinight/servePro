<?php

namespace Wpadmin\View\Cell;

use Cake\ORM\TableRegistry;
use Cake\View\Cell;

/**
 * Menu cell
 */
class MenuCell extends Cell {

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];
    protected $_breadFirst;
    protected $_breadSecond;
    protected $_menus;
    protected $_url;
    protected $_active;
    protected $_pageTitle;

    public function __construct(\Cake\Network\Request $request = null, \Cake\Network\Response $response = null, \Cake\Event\EventManager $eventManager = null, array $cellOptions = array()) {
        parent::__construct($request, $response, $eventManager, $cellOptions);
        $this->initData();
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display() {

        $this->set('menus', $this->_menus);
        $this->set('active', $this->_active);
        $this->set('url', $this->_url);
    }

    public function bread() {
        $this->set('breadFirst', $this->_breadFirst);
        $this->set('breadSecond', $this->_breadSecond);
    }

    public function title($header=null) {
        if(isset($header)){
            $this->_pageTitle = $header;
        }
        $this->set('pageTitle', $this->_pageTitle);
    }

    protected function initData() {
        $admin = $this->request->session()->read('User.admin');
        $menus = \Cake\Cache\Cache::read($admin->username . '_menus');
        if ($menus === false) {
            $Menu = TableRegistry::get('Wpadmin.Menu');
            if ($admin->username == 'admin') {
                $menus = $Menu->find('threaded', [
                            'keyField' => 'id',
                            'parentField' => 'pid'
                        ])->hydrate(false)->where('status = 1')->orderDesc('rank')->toArray();
            } else {
                $AdminMenu = TableRegistry::get('GroupMenu');
                $AdminMenu->displayField('menu_id');
                $admin_menus = $AdminMenu->find('list')->where(['admin_id' => $admin->id])->toArray();
                $admin_menus = array_values($admin_menus);
                if ($admin_menus) {
                    $menus = $Menu->find('threaded', [
                                'keyField' => 'id',
                                'parentField' => 'pid'
                            ])->hydrate(false)->where(['status' => 1, 'id in' => $admin_menus])->orderDesc('rank')->toArray();
                }
            }
            \Cake\Cache\Cache::write($admin->username . '_menus', $menus);
        }
        $this->_menus = $menus;
        $controller = $this->request->param('controller');
        $controller = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '-', $controller));
        $action = $this->request->param('action');
        $action = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '-', $action));
        $url = PROJ_PREFIX.'/' . $controller . '/' . $action;
        $this->_url = $url;
        $active = null;
        foreach ($menus as $value) {
            if (!empty($value['children'])) {
                foreach ($value['children'] as $sub_menu) {
                    if ($sub_menu['node'] == $url) {
                        $this->_pageTitle = $sub_menu['name'];
                        $this->_breadFirst = ['name' => $value['name'], 'node' => $value['node']];
                        $this->_breadSecond = ['name' => $sub_menu['name'], 'node' => $sub_menu['node']];
                        break;
                    }
                    if (!empty($sub_menu['children'])) {
                        foreach ($sub_menu['children'] as $v) {
                            if ($v['node'] == $url) {
                                $active = $sub_menu['node'];
                                $this->_active = $active;
                                $this->_pageTitle = $v['name'];
                                $this->_breadFirst = ['name' => $value['name'], 'node' => $value['node']];
                                $this->_breadSecond = ['name' => $sub_menu['name'], 'node' => $sub_menu['node']];
                            }
                        }
                    }
                }
            } else {
                if ($value['node'] == $url) {
                    $active = $value['node'];
                    $this->_active = $active;
                    $this->_pageTitle = $value['name'];
                    $this->_breadFirst = ['name' => $value['name'], 'node' => $value['node']];
                    //$this->_breadSecond = ['name' => $sub_menu['name'], 'node' => $sub_menu['node']];
                }
            }
        }
    }

}
