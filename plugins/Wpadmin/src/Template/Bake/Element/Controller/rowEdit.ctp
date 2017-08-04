<%
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$belongsTo = $this->Bake->aliasExtractor($modelObj, 'BelongsTo');
$belongsToMany = $this->Bake->aliasExtractor($modelObj, 'BelongsToMany');
$compact = ["'" . $singularName . "'"];
%>

    /**
     * rowEdit method
     *
     * @param string|null $id <%= $singularHumanName %> id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function rowEdit()
    {
    
        $id = $this->request->data('id');
        $oper = $this->request->data('oper');
        $data = $this->request->data();
        if($id&&$oper=='edit'){
            $<%= $singularName %> = $this-><%= $currentModelName %>->get($id,[
            'contain' => [<%= $this->Bake->stringifyList($belongsToMany, ['indent' => false]) %>]
            ]);
            $<%= $singularName %> = $this-><%= $currentModelName %>->patchEntity($<%= $singularName %>, $data);
            if($this-><%= $currentModelName %>->save($<%= $singularName %>)){
                  $this->Util->ajaxReturn(true, '保存成功');
            }else{
                  $this->Util->ajaxReturn(true, '保存失败');
            }
        }
    }
