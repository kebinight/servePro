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
use Cake\Utility\Inflector;

$fields = collection($fields)
->filter(function($field) use ($schema) {
return $schema->columnType($field) !== 'binary';
});

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
$fields = $fields->reject(function ($field) {
return $field === 'lft' || $field === 'rght';
});
}
%>
<div class="work-copy">
    <?= $this->Form->create($<%= $singularVar %>, ['class' => 'form-horizontal']) ?>
    <%
    foreach ($fields as $field) {
           if (in_array($field, $primaryKey)) {
                    continue;
            }
           if (isset($keyFields[$field])) {
                $fieldData = $schema->column($field);
    %>
         <div class="form-group">
            <label class="col-md-2 control-label"><%=$fieldData['comment']?$fieldData['comment']:$field%></label>
                <div class="col-md-8">
         <%
            if (!empty($fieldData['null'])) {
         %>
        <?php echo $this->Form->input('<%= $field %>', ['label' => false,'options' => $<%= $keyFields[$field] %>, 
                'empty' => true,'class'=>'form-control']); ?>
        <%
            } else {
        %>
       <?php echo $this->Form->input('<%= $field %>', ['label' => false,'options' => $<%= $keyFields[$field] %>,'class'=>'form-control']);?>
          <%
          }
        %>
            </div>
         </div>
        <%
        continue;
        }
    if (!in_array($field, ['created', 'modified', 'updated'])) {
            $fieldData = $schema->column($field);
    %>
    <div class="form-group">
        <label class="col-md-2 control-label"><%=$fieldData['comment']?$fieldData['comment']:$field%></label>
        <div class="col-md-8">
            <%
            if (($fieldData['type'] === 'date') && (!empty($fieldData['null']))) {
            %>
            <?php
            echo $this->Form->input('<%= $field %>', ['empty' => true, 'class' => 'form-control']);
            ?>
            <%
            } else {
            %>
            <?php
            echo $this->Form->input('<%= $field %>', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <%
    }
    }
    }
    if (!empty($associations['BelongsToMany'])) {
        foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
    %>
    <div class="form-group">
        <label class="col-md-2 control-label"><%=$assocName%></label>
        <div class="col-md-8">
    <?php
        echo $this->Form->input('<%= $assocData['property'] %>._ids', ['options' => $<%= $assocData['variable'] %>,'label'=>false,
            'class'=>'form-control']);
        ?>
        </div>
    </div>
    <%
        }
        }
    %>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <input type='submit' id='submit' class='btn btn-primary' value='保存' data-loading='稍候...' /> 
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

