webpackJsonp([6],{499:function(t,e,a){a(555);var n=a(196)(a(513),a(543),"data-v-ed739870",null);t.exports=n.exports},513:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default={data:function(){return{dataUrl:"/role/index",deleteUrl:"/role/delete",tableData:[],cur_page:1,multipleSelection:[],select_cate:"",select_word:"",del_list:[],is_search:!1}},created:function(){this.getData()},computed:{data:function(){return this.tableData}},methods:{handleCurrentChange:function(t){},getData:function(){var t=this,e=this.dataUrl;this.$fetch.post(e,{page:"1"}).then(function(e){var a=e.data;if(a.status){var n=a.data;t.tableData=n.roles}}).catch(function(t){})},add:function(){this.$router.push({path:"/role-add"})},search:function(){},formatter:function(t,e){return t.address},filterTag:function(t,e){return e.tag===t},handleEdit:function(t,e){var a=e.id;this.$router.push({path:"/role-edit",query:{role_id:a}})},handleDeleteOne:function(t){var e=this;this.$confirm("确定删除?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){var a=e.deleteUrl,n={id:t.id};e.$fetch.post(a,n).then(function(a){a.data.status&&e.tableData.splice(e.tableData.indexOf(t),1)}).catch(function(t){})}).catch(function(){})},formatStatus:function(t,e,a){switch(parseInt(t.status)){case 1:return"启用";case 0:return"禁用"}},handleSelectionChange:function(t){}}}},527:function(t,e,a){e=t.exports=a(89)(void 0),e.push([t.i,".handle-box[data-v-ed739870]{margin-bottom:20px}.handle-select[data-v-ed739870]{width:120px}.handle-input[data-v-ed739870]{width:300px;display:inline-block}",""])},543:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"table"},[a("div",{staticClass:"crumbs"},[a("el-breadcrumb",{attrs:{separator:"/"}},[a("el-breadcrumb-item",[a("i",{staticClass:"el-icon-menu"}),t._v(" 系统设置")]),t._v(" "),a("el-breadcrumb-item",[t._v("角色管理")])],1)],1),t._v(" "),a("div",{staticClass:"handle-box"},[a("el-input",{staticClass:"handle-input mr10",attrs:{placeholder:"筛选关键词"},model:{value:t.select_word,callback:function(e){t.select_word=e},expression:"select_word"}}),t._v(" "),a("el-button",{attrs:{type:"primary",icon:"search"},on:{click:t.search}},[t._v("搜索")]),t._v(" "),a("el-button",{staticClass:"handle-add mr10",attrs:{type:"success",icon:"plus"},on:{click:t.add}},[t._v("添加")])],1),t._v(" "),a("el-table",{ref:"menuTable",staticStyle:{width:"100%"},attrs:{data:t.data,border:""},on:{"selection-change":t.handleSelectionChange}},[a("el-table-column",{attrs:{type:"selection",width:"55"}}),t._v(" "),a("el-table-column",{attrs:{prop:"name",label:"角色名称",width:"150"}}),t._v(" "),a("el-table-column",{attrs:{prop:"rank",label:"排序权重",width:"100"}}),t._v(" "),a("el-table-column",{attrs:{prop:"status",label:"状态",width:"80",formatter:t.formatStatus}}),t._v(" "),a("el-table-column",{attrs:{prop:"admin.name",label:"操作员",width:"120"}}),t._v(" "),a("el-table-column",{attrs:{label:"权限",width:"220"},scopedSlots:t._u([{key:"default",fn:function(e){return t._l(e.row.slimit,function(e){return a("el-tag",{attrs:{type:"primary"}},[t._v("\n                "+t._s(e.name)+"\n                ")])})}}])}),t._v(" "),a("el-table-column",{attrs:{prop:"create_time",label:"创建时间",width:"150"}}),t._v(" "),a("el-table-column",{attrs:{prop:"update_time",label:"更新时间",width:"150"}}),t._v(" "),a("el-table-column",{attrs:{label:"操作"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("el-button",{attrs:{size:"small"},on:{click:function(a){t.handleEdit(e.$index,e.row)}}},[t._v("编辑")]),t._v(" "),a("el-button",{attrs:{size:"small",type:"danger"},on:{click:function(a){t.handleDeleteOne(e.row)}}},[t._v("删除")])]}}])})],1),t._v(" "),a("div",{staticClass:"pagination"},[a("el-pagination",{attrs:{layout:"prev, pager, next",total:1e3},on:{"current-change":t.handleCurrentChange}})],1)],1)},staticRenderFns:[]}},555:function(t,e,a){var n=a(527);"string"==typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);a(197)("0f8f1c1a",n,!0)}});