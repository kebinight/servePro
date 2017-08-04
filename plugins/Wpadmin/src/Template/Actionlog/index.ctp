<?php $this->start('static') ?>   
<link rel="stylesheet" type="text/css" href="/wpadmin/lib/jqgrid/css/ui.jqgrid.css">
<link rel="stylesheet" type="text/css" href="/wpadmin/lib/jqgrid/css/ui.ace.css">
<?php $this->end() ?> 
<div class="col-xs-12">
    <form id="table-bar-form">
        <div class="table-bar form-inline">
            <a href="/wpadmin/actionlog/add" class="btn btn-small btn-warning">
                <i class="icon icon-plus-sign"></i>添加
            </a>
            <div class="form-group">
                <label for="keywords">关键字</label>
                <input type="text" name="keywords" class="form-control" id="keywords" placeholder="输入关键字">
            </div>
            <div class="form-group">
                <label for="keywords">时间</label>
                <input type="text" name="begin_time" class="form-control date_timepicker_start" id="keywords" placeholder="开始时间">
                <label for="keywords">到</label>
                <input type="text" name="end_time" class="form-control date_timepicker_end" id="keywords" placeholder="结束时间">
            </div>
            <a onclick="doSearch();" class="btn btn-info"><i class="icon icon-search"></i>搜索</a>
            <!--<a onclick="doExport();" class="btn btn-info"><i class="icon icon-file-excel"></i>导出</a>-->
        </div>
    </form>
    <table id="list"><tr><td></td></tr></table> 
    <div id="pager"></div> 
</div>
<?php $this->start('script'); ?>
<script src="/wpadmin/lib/jqgrid/js/jquery.jqGrid.min.js"></script>
<script src="/wpadmin/lib/jqgrid/js/i18n/grid.locale-cn.js"></script>
<script>
                $(function () {
                    $('#main-content').bind('resize', function () {
                        $("#list").setGridWidth($('#main-content').width() - 40);
                    });
                    $(document).keypress(function (e) {
                        if (e.which == 13) {
                            doSearch();
                        }
                    });
                    $.zui.store.pageClear(); //刷新页面缓存清除
                    $("#list").jqGrid({
                        url: "/wpadmin/actionlog/getDataList",
                        datatype: "json",
                        mtype: "POST",
                        colNames:
                                ['请求地址', '请求类型', '浏览器信息', '客户端IP', '脚本名称', '日志内容', '控制器', '动作', '操作者', '创建时间', '操作'],
                        colModel: [
                            {name: 'url', editable: true, align: 'center'},
                            {name: 'type', editable: true, align: 'center'},
                            {name: 'useragent', editable: true, align: 'center'},
                            {name: 'ip', editable: true, align: 'center'},
                            {name: 'filename', editable: true, align: 'center'},
                            {name: 'msg', editable: true, align: 'center'},
                            {name: 'controller', editable: true, align: 'center'},
                            {name: 'action', editable: true, align: 'center'},
                            {name: 'user', editable: true, align: 'center'},
                            {name: 'create_time', editable: true, align: 'center'},
                            {name: 'actionBtn', align: 'center', viewable: false, sortable: false, frozen: true, formatter: actionFormatter}],
                        pager: "#pager",
                        rowNum: window._config.showDef,
                        rowList: window._config.pages,
                        sortname: "id",
                        sortorder: "desc",
                        viewrecords: true,
                        gridview: true,
                        autoencode: true,
                        caption: '',
                        autowidth: true,
                        height: 'auto',
                        rownumbers: true,
                        fixed: true,
                        jsonReader: {
                            root: "rows",
                            page: "page",
                            total: "total",
                            records: "records",
                            repeatitems: false,
                            id: "id"
                        },
                    }).navGrid('#pager', {edit: false, add: false, del: false, view: true});
                    $('#list').jqGrid('setFrozenColumns');
                });

                function actionFormatter(cellvalue, options, rowObject) {
                    response = '<a title="删除" onClick="delRecord(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn "><i class="icon icon-trash"></i> </a>';
                    response += '<a title="查看" onClick="doView(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn "><i class="icon icon-eye-open"></i> </a>';
                    response += '<a title="编辑" href="/wpadmin/actionlog/edit/' + rowObject.id + '" class="grid-btn "><i class="icon icon-pencil"></i> </a>';
                    return response;
                }

                function delRecord(id) {
                    layer.confirm('确定删除？', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        $.ajax({
                            type: 'post',
                            data: {id: id},
                            dataType: 'json',
                            url: '/wpadmin/actionlog/delete',
                            success: function (res) {
                                layer.msg(res.msg);
                                if (res.status) {
                                    $('#list').trigger('reloadGrid');
                                }
                            }
                        })
                    }, function () {
                    });
                }

                function doSearch() {
                    //搜索
                    var postData = $('#table-bar-form').serializeArray();
                    var data = {};
                    $.each(postData, function (i, n) {
                        data[n.name] = n.value;
                    });
                    $.zui.store.pageSet('searchData', data); //本地存储查询参数 供导出操作等调用
                    $("#list").jqGrid('setGridParam', {
                        postData: data
                    }).trigger("reloadGrid");
                }

                function doExport() {
                    //导出excel
                    var sortColumnName = $("#list").jqGrid('getGridParam', 'sortname');
                    var sortOrder = $("#list").jqGrid('getGridParam', 'sortorder');
                    var searchData = $.zui.store.pageGet('searchData') ? $.zui.store.pageGet('searchData') : {};
                    searchData['sidx'] = sortColumnName;
                    searchData['sort'] = sortOrder;
                    var searchQueryStr = $.param(searchData);
                    $("body").append("<iframe src='/wpadmin/actionlog/exportExcel?" + searchQueryStr + "' style='display: none;' ></iframe>");
                }

                function doView(id) {
                    //查看明细
                    url = '/wpadmin/actionlog/view/' + id;
                    layer.open({
                        type: 2,
                        title: '查看详情',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['45%', '70%'],
                        content: url//iframe的url
                    });
                }
</script>
<?php
$this->end();
