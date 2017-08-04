<?php $this->start('static') ?>   
<link rel="stylesheet" type="text/css" href="/wpadmin/lib/jqgrid/css/ui.jqgrid.css">
<link rel="stylesheet" type="text/css" href="/wpadmin/lib/jqgrid/css/ui.ace.css">
<?php $this->end() ?> 
<div class="col-xs-12">
    <form id="table-bar-form">
        <div class="table-bar form-inline">
            <a href="/admin/add" class="btn btn-small btn-warning">
                <i class="icon icon-plus-sign"></i>添加
            </a>
            <div class="form-group">
                <label for="keywords">关键字</label>
                <input type="text" name="keywords" class="form-control" id="keywords" placeholder="输入关键字">
            </div>
            <div class="form-group">
                <label for="keywords">时间</label>
                <input type="text" name="begin_time" class="form-control date_timepicker_start" id="keywords" placeholder="输入关键字">
                <label for="keywords">到</label>
                <input type="text" name="end_time" class="form-control date_timepicker_end" id="keywords" placeholder="输入关键字">

            </div>
            <a onclick="doSearch();" class="btn btn-info"><i class="icon icon-search"></i>搜索</a>
            <a onclick="doExport();" class="btn btn-info"><i class="icon icon-file-excel"></i>导出</a>
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
                    $.zui.store.pageClear(); //刷新页面缓存清除
                    $("#list").jqGrid({
                        url: "/wpadmin/admin/getDataList",
                        datatype: "json",
                        mtype: "POST",
                        colNames:
                                ['用户名', '群组', '1启用0禁用', '创建时间', '修改时间', '登录时间', '登录ip', '操作'],
                        colModel: [
                            {name: 'username', editable: true, align: 'center'},
                            {name: 'g.0.name', editable: true, align: 'center'},
                            {name: 'enabled', editable: true, align: 'center'},
                            {name: 'ctime', editable: true, align: 'center'},
                            {name: 'utime', editable: true, align: 'center'},
                            {name: 'login_time', editable: true, align: 'center'},
                            {name: 'login_ip', editable: true, align: 'center'},
                            {name: 'actionBtn', viewable: false, sortable: false, formatter: actionFormatter}],
                        pager: "#pager",
                        rowNum: 10,
                        rowList: [10, 20, 30],
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
                            id: "0"
                        },
                    }).navGrid('#pager', {edit: true, add: true, del: true, view: true}, searchFn, editFn, addFn, delFn, viewFn);
                });
                var searchFn = {
                };
                var editFn = {
                    jqModal: true,
                    reloadAfterSubmit: true,
                    closeOnEscape: true,
                    savekey: [true, 13],
                    closeAfterEdit: true,
                    url: '',
                    beforeShowForm: function (form) {
                        $('#tr_password', form).hide();
                    },
                    afterSubmit: function (response) {
                        var responseText = $.parseJSON(response.responseText);
                        var success = responseText.success;
                        var message = responseText.message;
                        return [success, message];
                    }
                };
                function edit() {

                }
                ;
                function addFn() {
                    console.log('add');
                }
                function delFn() {
                    console.log('add');
                }
                function viewFn() {
                    console.log('view');
                }

                function actionFormatter(cellvalue, options, rowObject) {
                    var response = '';
                    response = '<button onClick="delRecord(' + rowObject.id + ');" data-id="' + rowObject.id + '" class="grid-btn btn btn-primary btn-mini del-record"><i class="icon icon-trash"></i> 删除</button>';
                    response += '<a href="<?=PROJ_PREFIX?>/admin/edit/' + rowObject.id + '" class="grid-btn btn btn-primary btn-mini"><i class="icon icon-pencil"></i> 修改</a>';
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
                            url: '/admin/delete',
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
                    var searchData = $.zui.store.pageGet('searchData') ? $.zui.stroe.pageGet('searchData') : {};
                    searchData['sidx'] = sortColumnName;
                    searchData['sort'] = sortOrder;
                    var searchQueryStr = $.param(searchData);
                    $("body").append("<iframe src='/admin/admin/exportExcel?" + searchQueryStr + "' style='display: none;' ></iframe>");
                }
</script>
<?php
$this->end();
