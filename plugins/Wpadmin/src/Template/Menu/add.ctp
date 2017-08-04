<?php $this->start('static') ?>   
<link href="/wpadmin/lib/jqupload/uploadfile.css" rel="stylesheet">
<link href="/wpadmin/lib/jqvalidation/css/validationEngine.jquery.css" rel="stylesheet">
<?php $this->end() ?> 
<div class="work-copy">
    <?= $this->Form->create($menu, ['class' => 'form-horizontal']) ?>
    <div class="form-group">
        <label class="col-md-2 control-label">节点名称</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('name', ['label' => false, 'class' => 'form-control validate[required]']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">路径</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('node', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">上级id</label>
        <div class="col-md-8">
            <select name="pid" class="form-control">
                <option value="0">根节点</option>
                <?php if ($menus): ?>
                    <?php foreach ($menus as $item): ?>
                        <option value="<?= $item['id'] ?>"><?= $item['html'] . $item['name'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">样式</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('class', ['label' => false, 'class' => 'form-control choiceIcon']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">排序</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('rank', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">是否在菜单显示</label>
        <div class="col-md-1">
            <?php
            echo $this->Form->input('is_menu', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">状态</label>
        <div class="col-md-1">
            <?php
            echo $this->Form->input('status', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">备注</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('remark', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <input type='submit' id='submit' class='btn btn-primary' value='保存' data-loading='稍候...' /> 
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>

<?php $this->start('script'); ?>
<script type="text/javascript" src="/wpadmin/lib/jqform/jquery.form.js"></script>
<script type="text/javascript" src="/wpadmin/lib/jqupload/jquery.uploadfile.js"></script>
<script type="text/javascript" src="/wpadmin/lib/jqvalidation/js/languages/jquery.validationEngine-zh_CN.js"></script>
<script type="text/javascript" src="/wpadmin/lib/jqvalidation/js/jquery.validationEngine.js"></script>
<script>
    $(function () {
        // initJqupload('cover', '/admin/util/doUpload', 'jpg,png,gif,jpeg'); //初始化图片上传
        $('form').validationEngine({focusFirstField: true, autoPositionUpdate: true, promptPosition: "bottomRight"});
        $('form').ajaxForm({
            dataType: 'json',
            beforeSubmit: function (formData, jqForm, options) {
            },
            success: function (data) {
                if (data.status) {
                    layer.confirm(data.msg, {
                        btn: ['确认', '继续添加'] //按钮
                    }, function () {
                        window.location.href = '<?=PROJ_PREFIX?>/menu/index';
                    }, function () {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 5});
                }
            }
        });
    });
</script>
<?php
$this->end();
