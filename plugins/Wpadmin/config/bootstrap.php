<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-12-26 22:58:51 by allen <blog.rc5j.cn> , caowenpeng1990@126.com
 */
require dirname(__FILE__) . '/function.php';  //引入全局函数文件

use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Database\Type\TimeType;
use Cake\I18n\Time;
\Cake\Core\Configure::load('wpadmin');  //先加载
$wpconf = \Cake\Core\Configure::read('project');
$prefix_str = '/admin';
if($wpconf){
    if($wpconf['subdomain']){
        $prefix_str = '';
    }elseif(isset($wpconf['prefix'])){
        $prefix_str = $wpconf['prefix'];
    }
}
define('PROJ_PREFIX',$prefix_str);
//TimeType::$dateTimeClass = 'Admin\I18n\DateOnly';
Time::setJsonEncodeFormat('yyyy-MM-dd HH:mm:ss');
\Cake\I18n\Date::setJsonEncodeFormat('yyyy-MM-dd');
if (PHP_SAPI === 'cli') {
    // Attach bake events here.
    EventManager::instance()->on('Bake.beforeRender.Controller.controller', function (Event $event) {
        $view = $event->subject();
//        if(strpos('Admin.', $view->viewVars['plugin'])!==FALSE){
        if (strtolower($view->theme) == 'wpadmin') {
            // add the login and logout actions to the Users controller
            $view->viewVars['actions'] = [
                'index',
                'view',
                'add',
                'edit',
                'delete',
                'getDataList',
                'exportExcel',
                'rowEdit'
            ];
        }
    });
    EventManager::instance()->on('Bake.afterRender.Controller.controller', function(Event $event) {
        $view = $event->subject();
        if (strtolower($view->theme) == 'wpadmin') {
            $menuTable = \Cake\ORM\TableRegistry::get('Wpadmin.Menu');
            $node = PROJ_PREFIX. '/'.strtolower($view->viewVars['name']) . '/index';
            $menu = $menuTable->find()->where("`node` = '$node'")->first();
            if (!$menu) {
                $menu = $menuTable->newEntity();
                $menu->name = strtolower($view->viewVars['name']) . '管理';
                $menu->node = $node;
                $menu->is_menu = 1;
                $menu->pid = 1;
                $menuTable->save($menu);
                debug(\Cake\Cache\Cache::delete('admin_menus'));
            }
        }
    });
}