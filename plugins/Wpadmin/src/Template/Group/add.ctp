<?php $this->start('static') ?>   
<link href="/admin/lib/jqupload/uploadfile.css" rel="stylesheet">
<link href="/admin/lib/jqvalidation/css/validationEngine.jquery.css" rel="stylesheet">
<link href="/wpadmin/lib/select2/css/select2.min.css" rel="stylesheet">
<?php $this->end() ?> 
<div class="work-copy">
    <?= $this->Form->create($group, ['class' => 'form-horizontal']) ?>
    <div class="form-group">
        <label class="col-md-2 control-label">群组名称</label>
        <div class="col-md-8">
            <?php
            echo $this->Form->input('name', ['label' => false, 'class' => 'form-control']);
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
<script type="text/javascript" src="/admin/lib/jqform/jquery.form.js"></script>
<script type="text/javascript" src="/wpadmin/lib/jqupload/jquery.uploadfile.js"></script>
<script type="text/javascript" src="/wpadmin/lib/jqvalidation/js/languages/jquery.validationEngine-zh_CN.js"></script>
<script type="text/javascript" src="/wpadmin/lib/jqvalidation/js/jquery.validationEngine.js"></script>
<script src="/wpadmin/lib/select2/js/select2.full.min.js" ></script>
<script>
    $(function () {
        // initJqupload('cover', '/admin/util/doUpload', 'jpg,png,gif,jpeg'); //初始化图片上传
        $('form').validationEngine({focusFirstField: true, autoPositionUpdate: true, promptPosition: "bottomRight"});
        $('#select-industry').select2({
            language: "zh-CN",
            placeholder: '选择一个标签'
        });
        $('form').submit(function () {
            var form = $(this);
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
                                window.location.href = '<?=PROJ_PREFIX?>/group/index';
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
    });
</script>
<?php
$this->end();
