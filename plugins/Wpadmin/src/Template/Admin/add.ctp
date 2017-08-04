<?php $this->start('static') ?>   
<link href="/wpadmin/lib/jqupload/uploadfile.css" rel="stylesheet">
<link href="/wpadmin/lib/jqvalidation/css/validationEngine.jquery.css" rel="stylesheet">
<?php $this->end() ?> 
<div class="work-copy">
    <?= $this->Form->create($admin, ['class' => 'form-horizontal']) ?>
    <div class="form-group">
        <label class="col-md-2 control-label">用户名</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('username', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">密码</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('password', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">群组</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('g._ids', ['id' => 'admin-group-id', 'options' => (isset($g) && $g) ? $g : [], 'label' => false,
                'class' => 'form-control']);
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
    $('form').validationEngine({focusFirstField: true, autoPositionUpdate: true, promptPosition: "bottomRight"});
    $('form').submit(function () {
        var form = $(this);
        var groupIds = $('#admin-group-id').val();
        if(!groupIds) {
            layer.alert('请选择群组！');
            return false;
        }
        $.ajax({
            type: $(form).attr('method'),
            url: $(form).attr('action'),
            data: $(form).serialize(),
            dataType: 'json',
            success: function (res) {
                if (typeof res === 'object') {
                    if (res.status) {
                        layer.confirm(res.msg, {
                            btn: ['确认', '继续添加'] //按钮
                        }, function () {
                            window.location.href = '/admin/index';
                        }, function () {
                            window.location.reload();
                        });
                    } else {
                        layer.alert(res.msg, {icon: 5});
                    }
                }
            }
        });
        return false;
    });
</script>
<?php
$this->end();
