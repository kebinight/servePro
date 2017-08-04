<?php $this->start('static') ?>   
<link href="/wpadmin/lib/jqupload/uploadfile.css" rel="stylesheet">
<link href="/wpadmin/lib/jqvalidation/css/validationEngine.jquery.css" rel="stylesheet">
<?php $this->end()  ?> 
<div class="work-copy">
    <?= $this->Form->create($actionlog, ['class' => 'form-horizontal']) ?>
        <div class="form-group">
        <label class="col-md-2 control-label">请求地址</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('url', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">请求类型</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('type', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">浏览器信息</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('useragent', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">客户端IP</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('ip', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">脚本名称</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('filename', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">日志内容</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('msg', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">控制器</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('controller', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">动作</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('action', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">请求参数</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('param', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">操作者</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('user', ['label' => false, 'class' => 'form-control']);
            ?>
        </div>
    </div>
        <div class="form-group">
        <label class="col-md-2 control-label">创建时间</label>
        <div class="col-md-8">
                        <?php
            echo $this->Form->input('create_time', ['label' => false, 'class' => 'form-control']);
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
<!--<script src="/wpadmin/lib/ueditor/ueditor.config.js" ></script>
    <script src="/wpadmin/lib/ueditor/ueditor.all.js" ></script>
    <script href="/wpadmin/lib/ueditor/lang/zh-cn/zh-cn.js" ></script>-->
<script>
    $(function () {
        // initJqupload('cover', '/wpadmin/util/doUpload', 'jpg,png,gif,jpeg'); //初始化图片上传
        //var ue = UE.getEditor('content'); //初始化富文本编辑器
        $('form').validationEngine({focusFirstField: true, autoPositionUpdate: true, promptPosition: "bottomRight"});
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
                                window.location.href = '/wpadmin.actionlog/index';
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
