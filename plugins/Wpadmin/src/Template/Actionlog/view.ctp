<head>
    <link rel="stylesheet" type="text/css" href="/wpadmin/lib/zui/css/zui.min.css"/>
    <style>
        th{
            width:120px;
        }
    </style>
</head>
<body>
<div class="actionlog view large-9 medium-8 columns content">
    <table class="vertical-table table table-hover table-bordered">
    
    <tr>
        <th>请求地址</th>
        <td><?= h($actionlog->url) ?></td>
    </tr>
    
    <tr>
        <th>请求类型</th>
        <td><?= h($actionlog->type) ?></td>
    </tr>
    
    <tr>
        <th>浏览器信息</th>
        <td><?= h($actionlog->useragent) ?></td>
    </tr>
    
    <tr>
        <th>客户端IP</th>
        <td><?= h($actionlog->ip) ?></td>
    </tr>
    
    <tr>
        <th>脚本名称</th>
        <td><?= h($actionlog->filename) ?></td>
    </tr>
    
    <tr>
        <th>日志内容</th>
        <td><?= h($actionlog->msg) ?></td>
    </tr>
    
    <tr>
        <th>控制器</th>
        <td><?= h($actionlog->controller) ?></td>
    </tr>
    
    <tr>
        <th>动作</th>
        <td><?= h($actionlog->action) ?></td>
    </tr>
    
    <tr>
        <th>请求参数</th>
        <td><?= h($actionlog->param) ?></td>
    </tr>
    
    <tr>
        <th>操作者</th>
        <td><?= h($actionlog->user) ?></td>
    </tr>
    
    <tr>
        <th>创建时间</th>
        <td><?= h($actionlog->create_time) ?></td>
    </tr>
</div>
</body>
