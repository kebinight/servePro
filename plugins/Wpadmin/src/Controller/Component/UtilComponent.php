<?php

namespace Wpadmin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

/**
 * Util component
 */
class UtilComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * ajax 返回json
     * @param array/boole $status 可为数组也可以为boole
     * @param type $msg
     */
    public function ajaxReturn($status, $msg = '') {
        $this->autoRender = false;
        $this->response->type('json');
        if (is_array($status) && !empty($status)) {
            echo json_encode($status, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array('status' => $status, 'msg' => $msg), JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    /**
     * 
     * @param type $msg
     * @param type $user
     * @param type $ip
     * @param type $url
     * @param type $controller
     * @param type $action
     * @param type $param
     * @param type $useragent Description
     */
    public function actionLog($msg, $user) {
        $actionlogTable = \Cake\ORM\TableRegistry::get('Wpadmin.Actionlog');
        $actionlog = $actionlogTable->newEntity();
        $actionlog->msg = $msg;
        $actionlog->user = $user;
        $actionlog->ip = $this->request->clientIp();
        $actionlog->url = $this->request->url;
        $actionlog->filename = __FILE__;
        $actionlog->controller = strtolower($this->request->param('controller'));
        $param = var_export($this->request->data(), true);
        $actionlog->type = $this->request->method();
        $actionlog->useragent = $this->request->header('User-Agent');
        $actionlog->action = strtolower($this->request->param('action'));
        $actionlog->param = $param;
        if($actionlog->action=='login'){
            $actionlog->param = '';
        }
        $actionlog->create_time = date('Y-m-d H:i:s');
        $ck = $actionlogTable->save($actionlog);
        //\Cake\Log\Log::debug($ck);
    }

}
