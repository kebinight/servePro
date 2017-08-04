<?php
namespace Wpadmin\Controller;

use Wpadmin\Controller\AppController;

/**
 * Actionlog Controller
 *
 * @property \Wpadmin\Model\Table\ActionlogTable $Actionlog
 */
class ActionlogController extends AppController
{

/**
* Index method
*
* @return void
*/
public function index()
{
$this->set('actionlog', $this->Actionlog);
}

    /**
     * View method
     *
     * @param string|null $id Actionlog id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->autoLayout(false);
        $actionlog = $this->Actionlog->get($id, [
            'contain' => []
        ]);
        $this->set('actionlog', $actionlog);
        $this->set('_serialize', ['actionlog']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $actionlog = $this->Actionlog->newEntity();
        if ($this->request->is('post')) {
            $actionlog = $this->Actionlog->patchEntity($actionlog, $this->request->data);
            if ($this->Actionlog->save($actionlog)) {
                 $this->Util->ajaxReturn(true,'添加成功');
            } else {
                 $errors = $actionlog->errors();
                 $this->Util->ajaxReturn(['status'=>false, 'msg'=>getMessage($errors),'errors'=>$errors]);
            }
        }
                $this->set(compact('actionlog'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Actionlog id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
         $actionlog = $this->Actionlog->get($id,[
            'contain' => []
        ]);
        if ($this->request->is(['post','put'])) {
            $actionlog = $this->Actionlog->patchEntity($actionlog, $this->request->data);
            if ($this->Actionlog->save($actionlog)) {
                  $this->Util->ajaxReturn(true,'修改成功');
            } else {
                 $errors = $actionlog->errors();
               $this->Util->ajaxReturn(false,getMessage($errors));
            }
        }
                  $this->set(compact('actionlog'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Actionlog id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod('post');
         $id = $this->request->data('id');
                if ($this->request->is('post')) {
                $actionlog = $this->Actionlog->get($id);
                 if ($this->Actionlog->delete($actionlog)) {
                     $this->Util->ajaxReturn(true,'删除成功');
                } else {
                    $errors = $actionlog->errors();
                    $this->Util->ajaxReturn(true,getMessage($errors));
                }
          }
    }

/**
* get jqgrid data 
*
* @return json
*/
public function getDataList()
{
        $this->request->allowMethod('ajax');
        $page = $this->request->data('page');
        $rows = $this->request->data('rows');
        $sort = 'Actionlog.'.$this->request->data('sidx');
        $order = $this->request->data('sord');
        $keywords = $this->request->data('keywords');
        $begin_time = $this->request->data('begin_time');
        $end_time = $this->request->data('end_time');
        $where = [];
        if (!empty($keywords)) {
            $where[' username like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`create_time`) >' => $begin_time], ['date(`create_time`) <' => $end_time]];
        }
                $data = $this->getJsonForJqrid($page, $rows, '', $sort, $order,$where);
                $this->autoRender = false;
        $this->response->type('json');
        $this->response->body(json_encode($data));
        $this->response->send();
        $this->response->stop();
}

/**
* export csv
*
* @return csv 
*/
public function exportExcel()
{
        $sort = $this->request->query('sidx');
        $order = $this->request->query('sort');
        $keywords = $this->request->query('keywords');
        $begin_time = $this->request->query('begin_time');
        $end_time = $this->request->query('end_time');
        $where = [];
        if (!empty($keywords)) {
            $where['username like'] = "%$keywords%";
        }
        if (!empty($begin_time) && !empty($end_time)) {
            $begin_time = date('Y-m-d', strtotime($begin_time));
            $end_time = date('Y-m-d', strtotime($end_time));
            $where['and'] = [['date(`create_time`) >' => $begin_time], ['date(`create_time`) <' => $end_time]];
        }
        $Table =  $this->Actionlog;
        $column = ['请求地址','请求类型','浏览器信息','客户端IP','脚本名称','日志内容','控制器','动作','请求参数','操作者','创建时间'];
        $query = $Table->find();
        $query->hydrate(false);
        $query->select(['url','type','useragent','ip','filename','msg','controller','action','param','user','create_time']);
         if (!empty($where)) {
            $query->where($where);
        }
        if (!empty($sort) && !empty($order)) {
            $query->order([$sort => $order]);
        }
        $res = $query->toArray();
        $this->autoRender = false;
        $filename = 'Actionlog_'.date('Y-m-d').'.csv';
        \Wpadmin\Utils\Export::exportCsv($column,$res,$filename);

}
}
