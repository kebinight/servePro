webpackJsonp([8],{497:function(t,e,a){a(549);var i=a(196)(a(509),a(537),null,null);t.exports=i.exports},509:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default={data:function(){return{dataUrl:"/slimit/index",addActionUrl:"/slimit/save",deleteUrl:"/slimit/delete",limitData:[],dialogFormVisible:!1,curentOpeLimitIndex:null,actionItem:{name:"",parent_id:0,node:"",rank:0,status:1,remark:""},rules:{name:[{required:!0,message:"请输入动作名称",trigger:"blur"}],node:[{required:!0,message:"请输入action",trigger:"blur"}]},pager:{curent_page:1,item_total:0,page_size:1,page_sizes:[1,2,4,8,10]}}},watch:{limitData:function(t){this.pager.item_total=t.length}},created:function(){this.getData()},methods:{getData:function(){var t=this,e=this.dataUrl;this.$fetch.post(e,{}).then(function(e){var a=e.data;if(a.status){var i=a.data;t.limitData=i.limits}}).catch(function(t){})},showAddTag:function(t){this.dialogFormVisible=!0,this.curentOpeLimitIndex=this.limitData.indexOf(t)},submitActionPost:function(t){var e=this,a=this,i=this.actionItem;null!==a.curentOpeLimitIndex&&this.$refs[t].validate(function(t){if(!t)return console.log("error submit!!"),!1;var n=a.addActionUrl;i.parent_id=a.limitData[e.curentOpeLimitIndex].id,e.$fetch.post(n,i).then(function(t){var e=t.data;e.status&&(a.dialogFormVisible=!1,a.limitData[a.curentOpeLimitIndex].children.push(e.data.action),a.actionItem.name="",a.actionItem.parent_id="",a.actionItem.node="",a.actionItem.rank=0,a.actionItem.status=1,a.actionItem.remark="")}).catch(function(t){})})},add:function(){this.$router.push({path:"/limit-add"})},handleEdit:function(t,e){var a=e.id;this.$router.push({path:"/limit-edit",query:{limit_id:a}})},handleDeleteOne:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null,a=this;this.$confirm("确定删除?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){var i=a.deleteUrl,n={id:t.id};a.$fetch.post(i,n).then(function(i){i.data.status&&(0!=t.parent_id?e.children.splice(e.children.indexOf(t),1):a.limitData.splice(a.limitData.indexOf(t),1))}).catch(function(t){})}).catch(function(){})},search:function(){},handleInputConfirm:function(){var t=this.inputValue;t&&this.dynamicTags.push(t),this.inputVisible=!1,this.inputValue=""},formatStatus:function(t,e,a){switch(parseInt(t.status)){case 1:return"启用";case 0:return"禁用"}},handleSizeChange:function(t){console.log("每页 "+t+" 条")},handleCurrentChange:function(t){console.log("当前页: "+t)}}}},521:function(t,e,a){e=t.exports=a(89)(void 0),e.push([t.i,".handle-box{margin-bottom:20px}.handle-select{width:120px}.handle-input{width:300px;display:inline-block}.el-table .info-row{background:#c9e5f5}.el-table .positive-row{background:#e2f0e4}",""])},537:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"table"},[a("div",{staticClass:"crumbs"},[a("el-breadcrumb",{attrs:{separator:"/"}},[a("el-breadcrumb-item",[a("i",{staticClass:"el-icon-menu"}),t._v(" 系统设置")]),t._v(" "),a("el-breadcrumb-item",[t._v("权限管理")])],1)],1),t._v(" "),a("div",{staticClass:"handle-box"},[a("el-input",{staticClass:"handle-input mr10",attrs:{placeholder:"输入关键字进行筛选"}}),t._v(" "),a("el-button",{attrs:{type:"primary",icon:"search"},on:{click:t.search}},[t._v("搜索")]),t._v(" "),a("el-button",{staticClass:"handle-add mr10",attrs:{type:"success",icon:"plus"},on:{click:t.add}},[t._v("添加")])],1),t._v(" "),a("el-table",{staticStyle:{width:"100%"},attrs:{data:t.limitData}},[a("el-table-column",{attrs:{prop:"name",label:"权限名称",width:"180"}}),t._v(" "),a("el-table-column",{attrs:{prop:"node",label:"权限标识",width:"120"}}),t._v(" "),a("el-table-column",{attrs:{prop:"status",label:"状态",width:"80",formatter:t.formatStatus}}),t._v(" "),a("el-table-column",{attrs:{prop:"create_time",label:"创建时间",width:"150"}}),t._v(" "),a("el-table-column",{attrs:{prop:"update_time",label:"更新时间",width:"150"}}),t._v(" "),a("el-table-column",{attrs:{label:"权限动作",width:"300"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._l(e.row.children,function(i){return a("el-tag",{attrs:{type:"primary",closable:!0,"close-transition":!1},on:{close:function(a){t.handleDeleteOne(i,e.row)}}},[t._v("\n                "+t._s(i.name)+"\n                ")])}),t._v(" "),a("el-button",{attrs:{size:"small"},on:{click:function(a){t.showAddTag(e.row)}}},[t._v("+添加动作")])]}}])}),t._v(" "),a("el-table-column",{attrs:{label:"操作"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-button",{attrs:{size:"small"},on:{click:function(a){t.handleEdit(e.$index,e.row)}}},[t._v("编辑")]),t._v(" "),a("el-button",{attrs:{size:"small",type:"danger"},on:{click:function(a){t.handleDeleteOne(e.row)}}},[t._v("删除")])]}}])})],1),t._v(" "),a("div",{staticClass:"pagination"},[a("el-pagination",{attrs:{"current-page":t.pager.curent_page,"page-sizes":t.pager.page_sizes,"page-size":t.pager.page_size,layout:"total, sizes, prev, pager, next, jumper",total:t.pager.item_total},on:{"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}})],1),t._v(" "),a("el-dialog",{attrs:{title:"添加权限动作",visible:t.dialogFormVisible,size:"tiny"},on:{"update:visible":function(e){t.dialogFormVisible=e}}},[a("el-form",{ref:"limitForm",attrs:{model:t.actionItem,rules:t.rules}},[a("el-form-item",{attrs:{label:"动作名称","label-width":"120px",prop:"name"}},[a("el-input",{attrs:{"auto-complete":"off"},model:{value:t.actionItem.name,callback:function(e){t.actionItem.name=e},expression:"actionItem.name"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"对应action名称","label-width":"120px",prop:"node"}},[a("el-input",{attrs:{"auto-complete":"off"},model:{value:t.actionItem.node,callback:function(e){t.actionItem.node=e},expression:"actionItem.node"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"排序权重","label-width":"120px",prop:"rank",rules:[{required:!0,message:"权重不能为空"},{type:"number",message:"排序权重必须为数字",trigger:"blur"}]}},[a("el-input",{attrs:{placeholder:"请输入排序权重（数值越大排序越靠前）"},model:{value:t.actionItem.rank,callback:function(e){t.actionItem.rank=t._n(e)},expression:"actionItem.rank"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"是否启用","label-width":"120px",prop:"status"}},[a("el-switch",{attrs:{"on-text":"启用","off-text":"禁用","on-value":"1","off-value":"0"},model:{value:t.actionItem.status,callback:function(e){t.actionItem.status=e},expression:"actionItem.status"}})],1)],1),t._v(" "),a("div",{staticClass:"dialog-footer",slot:"footer"},[a("el-button",{on:{click:function(e){t.dialogFormVisible=!1}}},[t._v("取 消")]),t._v(" "),a("el-button",{attrs:{type:"primary"},on:{click:function(e){t.submitActionPost("limitForm")}}},[t._v("确 定")])],1)],1)],1)},staticRenderFns:[]}},549:function(t,e,a){var i=a(521);"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);a(197)("4786fee4",i,!0)}});