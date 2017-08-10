<?php
namespace App\Controller\Admin;

use Cake\Log\Log;

/**
 * Slimit Controller
 *
 * @property \App\Model\Table\SlimitTable $Slimit
 */
class SlimitController extends AppController
{

    public function index()
    {
        $limits = $this->Slimit->find('threaded')
            ->where(['status' => 1])->map(function($row) {
                $row->create_time = $row->create_time->i18nFormat('yyyy-MM-dd HH:mm');
                $row->update_time = $row->update_time->i18nFormat('yyyy-MM-dd HH:mm');
                return $row;
            })->toArray();
        $this->Common->dealReturn(true, '', ['limits' => $limits]);
    }


    /**
     * 添加/更新权限信息
     */
    public function save()
    {
        if($this->request->is(["POST", "OPTIONS"])) {
            $data = $this->request->data;

            if(isset($data['id'])) {
                $limit = $this->Slimit->get($data['id']);
                $newLimit = $this->Slimit->patchEntity($limit, $data);
            } else {
                $newLimit = $this->Slimit->newEntity($data);
            }

            if($this->Slimit->save($newLimit)) {
                $this->Common->dealReturn(true, '操作成功', ['action' => $newLimit]);
            } else {
                $this->Common->dealReturn(false, '操作失败');
            }
        }
    }
}
