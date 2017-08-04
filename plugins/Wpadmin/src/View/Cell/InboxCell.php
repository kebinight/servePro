<?php

namespace Wpadmin\View\Cell;

use Cake\View\Cell;

/**
 * Inbox cell
 * @property  \App\Model\Table\AdminmsgTable $Adminmsg 管理员消息表
 */
class InboxCell extends Cell {

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];
    protected $_unread_count;

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
        $this->set('unread_count', $this->_unread_count);
    }

    
    /**
     * 菜单那的消息显示
     */
    public function menu() {
        $this->set('unread_count', $this->_unread_count);
    }

    protected function initData() {
        $this->loadModel('Adminmsg');
        $unread = $this->Adminmsg->find('unread');
        $this->_unread_count = $unread->count();
    }

}
