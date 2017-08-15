<?php
namespace App\Controller\Admin;


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
     *  初始化添加/编辑页面数据
     */
    public function saveIndex()
    {
        if($this->request->is(['POST'])) {
            $id = $this->request->data('id');
            $slimit = '';
            if($id) {
                $slimit = $this->Slimit->find()->where(['id' => $id])->map(function($row) {
                    $row->create_time = $row->create_time->i18nFormat('yyyy-MM-dd HH:mm');
                    $row->update_time = $row->update_time->i18nFormat('yyyy-MM-dd HH:mm');
                    return $row;
                })->first();
            }

            $this->Common->dealReturn(true, '', ['slimit' => $slimit]);
        }
    }


    /**
     * 添加/更新权限信息
     */
    public function save()
    {
        if($this->request->is(["POST"])) {
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


    /**
     * 删除
     * @param int $id
     */
    public function delete()
    {
        if($this->request->is(["POST"])) {
            $data = $this->request->data;
            if(isset($data['id'])) {
                $entity = $this->Slimit->get($data['id']);
                $result = $this->Slimit->delete($entity);
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
