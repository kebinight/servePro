webpackJsonp([1],{201:function(e,t,r){r(547);var n=r(196)(r(514),r(535),"data-v-46148f12",null);e.exports=n.exports},501:function(e,t){"function"==typeof Object.create?e.exports=function(e,t){e.super_=t,e.prototype=Object.create(t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}})}:e.exports=function(e,t){e.super_=t;var r=function(){};r.prototype=t.prototype,e.prototype=new r,e.prototype.constructor=e}},502:function(e,t){e.exports=function(e){return e&&"object"==typeof e&&"function"==typeof e.copy&&"function"==typeof e.fill&&"function"==typeof e.readUInt8}},503:function(e,t,r){(function(e,n){function o(e,r){var n={seen:[],stylize:a};return arguments.length>=3&&(n.depth=arguments[2]),arguments.length>=4&&(n.colors=arguments[3]),g(r)?n.showHidden=r:r&&t._extend(n,r),_(n.showHidden)&&(n.showHidden=!1),_(n.depth)&&(n.depth=2),_(n.colors)&&(n.colors=!1),_(n.customInspect)&&(n.customInspect=!0),n.colors&&(n.stylize=i),l(n,e,n.depth)}function i(e,t){var r=o.styles[t];return r?"["+o.colors[r][0]+"m"+e+"["+o.colors[r][1]+"m":e}function a(e,t){return e}function s(e){var t={};return e.forEach(function(e,r){t[e]=!0}),t}function l(e,r,n){if(e.customInspect&&r&&w(r.inspect)&&r.inspect!==t.inspect&&(!r.constructor||r.constructor.prototype!==r)){var o=r.inspect(n,e);return v(o)||(o=l(e,o,n)),o}var i=u(e,r);if(i)return i;var a=Object.keys(r),g=s(a);if(e.showHidden&&(a=Object.getOwnPropertyNames(r)),j(r)&&(a.indexOf("message")>=0||a.indexOf("description")>=0))return c(r);if(0===a.length){if(w(r)){var y=r.name?": "+r.name:"";return e.stylize("[Function"+y+"]","special")}if(D(r))return e.stylize(RegExp.prototype.toString.call(r),"regexp");if(O(r))return e.stylize(Date.prototype.toString.call(r),"date");if(j(r))return c(r)}var b="",h=!1,x=["{","}"];if(d(r)&&(h=!0,x=["[","]"]),w(r)){b=" [Function"+(r.name?": "+r.name:"")+"]"}if(D(r)&&(b=" "+RegExp.prototype.toString.call(r)),O(r)&&(b=" "+Date.prototype.toUTCString.call(r)),j(r)&&(b=" "+c(r)),0===a.length&&(!h||0==r.length))return x[0]+b+x[1];if(n<0)return D(r)?e.stylize(RegExp.prototype.toString.call(r),"regexp"):e.stylize("[Object]","special");e.seen.push(r);var _;return _=h?f(e,r,n,g,a):a.map(function(t){return p(e,r,n,g,t,h)}),e.seen.pop(),m(_,b,x)}function u(e,t){if(_(t))return e.stylize("undefined","undefined");if(v(t)){var r="'"+JSON.stringify(t).replace(/^"|"$/g,"").replace(/'/g,"\\'").replace(/\\"/g,'"')+"'";return e.stylize(r,"string")}return h(t)?e.stylize(""+t,"number"):g(t)?e.stylize(""+t,"boolean"):y(t)?e.stylize("null","null"):void 0}function c(e){return"["+Error.prototype.toString.call(e)+"]"}function f(e,t,r,n,o){for(var i=[],a=0,s=t.length;a<s;++a)N(t,String(a))?i.push(p(e,t,r,n,String(a),!0)):i.push("");return o.forEach(function(o){o.match(/^\d+$/)||i.push(p(e,t,r,n,o,!0))}),i}function p(e,t,r,n,o,i){var a,s,u;if(u=Object.getOwnPropertyDescriptor(t,o)||{value:t[o]},u.get?s=u.set?e.stylize("[Getter/Setter]","special"):e.stylize("[Getter]","special"):u.set&&(s=e.stylize("[Setter]","special")),N(n,o)||(a="["+o+"]"),s||(e.seen.indexOf(u.value)<0?(s=y(r)?l(e,u.value,null):l(e,u.value,r-1),s.indexOf("\n")>-1&&(s=i?s.split("\n").map(function(e){return"  "+e}).join("\n").substr(2):"\n"+s.split("\n").map(function(e){return"   "+e}).join("\n"))):s=e.stylize("[Circular]","special")),_(a)){if(i&&o.match(/^\d+$/))return s;a=JSON.stringify(""+o),a.match(/^"([a-zA-Z_][a-zA-Z_0-9]*)"$/)?(a=a.substr(1,a.length-2),a=e.stylize(a,"name")):(a=a.replace(/'/g,"\\'").replace(/\\"/g,'"').replace(/(^"|"$)/g,"'"),a=e.stylize(a,"string"))}return a+": "+s}function m(e,t,r){var n=0;return e.reduce(function(e,t){return n++,t.indexOf("\n")>=0&&n++,e+t.replace(/\u001b\[\d\d?m/g,"").length+1},0)>60?r[0]+(""===t?"":t+"\n ")+" "+e.join(",\n  ")+" "+r[1]:r[0]+t+" "+e.join(", ")+" "+r[1]}function d(e){return Array.isArray(e)}function g(e){return"boolean"==typeof e}function y(e){return null===e}function b(e){return null==e}function h(e){return"number"==typeof e}function v(e){return"string"==typeof e}function x(e){return"symbol"==typeof e}function _(e){return void 0===e}function D(e){return k(e)&&"[object RegExp]"===E(e)}function k(e){return"object"==typeof e&&null!==e}function O(e){return k(e)&&"[object Date]"===E(e)}function j(e){return k(e)&&("[object Error]"===E(e)||e instanceof Error)}function w(e){return"function"==typeof e}function S(e){return null===e||"boolean"==typeof e||"number"==typeof e||"string"==typeof e||"symbol"==typeof e||void 0===e}function E(e){return Object.prototype.toString.call(e)}function z(e){return e<10?"0"+e.toString(10):e.toString(10)}function $(){var e=new Date,t=[z(e.getHours()),z(e.getMinutes()),z(e.getSeconds())].join(":");return[e.getDate(),C[e.getMonth()],t].join(" ")}function N(e,t){return Object.prototype.hasOwnProperty.call(e,t)}var F=/%[sdj%]/g;t.format=function(e){if(!v(e)){for(var t=[],r=0;r<arguments.length;r++)t.push(o(arguments[r]));return t.join(" ")}for(var r=1,n=arguments,i=n.length,a=String(e).replace(F,function(e){if("%%"===e)return"%";if(r>=i)return e;switch(e){case"%s":return String(n[r++]);case"%d":return Number(n[r++]);case"%j":try{return JSON.stringify(n[r++])}catch(e){return"[Circular]"}default:return e}}),s=n[r];r<i;s=n[++r])y(s)||!k(s)?a+=" "+s:a+=" "+o(s);return a},t.deprecate=function(r,o){function i(){if(!a){if(n.throwDeprecation)throw new Error(o);n.traceDeprecation?console.trace(o):console.error(o),a=!0}return r.apply(this,arguments)}if(_(e.process))return function(){return t.deprecate(r,o).apply(this,arguments)};if(!0===n.noDeprecation)return r;var a=!1;return i};var U,A={};t.debuglog=function(e){if(_(U)&&(U=r.i({NODE_ENV:"production"}).NODE_DEBUG||""),e=e.toUpperCase(),!A[e])if(new RegExp("\\b"+e+"\\b","i").test(U)){var o=n.pid;A[e]=function(){var r=t.format.apply(t,arguments);console.error("%s %d: %s",e,o,r)}}else A[e]=function(){};return A[e]},t.inspect=o,o.colors={bold:[1,22],italic:[3,23],underline:[4,24],inverse:[7,27],white:[37,39],grey:[90,39],black:[30,39],blue:[34,39],cyan:[36,39],green:[32,39],magenta:[35,39],red:[31,39],yellow:[33,39]},o.styles={special:"cyan",number:"yellow",boolean:"yellow",undefined:"grey",null:"bold",string:"green",date:"magenta",regexp:"red"},t.isArray=d,t.isBoolean=g,t.isNull=y,t.isNullOrUndefined=b,t.isNumber=h,t.isString=v,t.isSymbol=x,t.isUndefined=_,t.isRegExp=D,t.isObject=k,t.isDate=O,t.isError=j,t.isFunction=w,t.isPrimitive=S,t.isBuffer=r(502);var C=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];t.log=function(){console.log("%s - %s",$(),t.format.apply(t,arguments))},t.inherits=r(501),t._extend=function(e,t){if(!t||!k(t))return e;for(var r=Object.keys(t),n=r.length;n--;)e[r[n]]=t[r[n]];return e}}).call(t,r(90),r(198))},514:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});r(503);t.default={created:function(){var e=this.$route.path,t=this;"/role-add"==e?this.pageTitle="添加角色":"/role-edit"==e&&(this.pageTitle="编辑角色");var r=this.$route.query.role_id,n=r?{id:r}:{},o=this.initUrl;this.$fetch.post(o,n).then(function(e){var r=e.data;if(r.status){var n=r.data,o=n.role,i=n.limits,a=[],s=[];i.forEach(function(e){var t=e.children,r=e.name;delete e.children,e.name=e.name+" - 菜单显示",s.push(e),t.length&&t.forEach(function(e){e.name=r+" - "+e.name,s.push(e)})}),o&&(o.status=o.status.toString(),o.slimit.forEach(function(e){a.push(e.id)}),o.slimit={_ids:a},t.formData=o),t.picker.allData=s}}).catch(function(e){})},data:function(){return{pageTitle:"",postUrl:"/role/save",initUrl:"/role/editIndex",formData:{name:"",rank:0,status:"1",slimit:{_ids:[]},remark:""},picker:{title:["待选择的权限","已拥有的权限"],allData:[]},rules:{name:[{required:!0,message:"请输入菜单名称",trigger:"blur"},{min:1,max:8,message:"长度在 1 到 8 个字符",trigger:"blur"}],node:[{required:!0,message:"请输入路由标识",trigger:"blur"}]}}},methods:{submit:function(e){var t=this,r=this.postUrl,n=this,o=this.formData;delete o.create_time,delete o.update_time,this.$refs[e].validate(function(e){if(!e)return console.log("error submit!!"),!1;t.$fetch.post(r,o).then(function(e){e.data.status&&n.$router.push({path:"/role-index"})}).catch(function(e){})})},resetForm:function(e){this.$refs[e].resetFields()}}}},519:function(e,t,r){t=e.exports=r(89)(void 0),t.push([e.i,"",""])},535:function(e,t){e.exports={render:function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",[r("div",{staticClass:"crumbs"},[r("el-breadcrumb",{attrs:{separator:"/"}},[r("el-breadcrumb-item",[r("i",{staticClass:"el-icon-menu"}),e._v(" 系统设置")]),e._v(" "),r("el-breadcrumb-item",{attrs:{to:"/role-index"}},[r("i",{staticClass:"el-icon-menu"}),e._v(" 角色管理")]),e._v(" "),r("el-breadcrumb-item",[e._v(e._s(e.pageTitle))])],1)],1),e._v(" "),r("el-row",[r("br"),r("br"),e._v(" "),r("el-col",{attrs:{span:12,offset:6}},[r("div",[r("el-form",{ref:"roleForm",attrs:{model:e.formData,rules:e.rules,"label-width":"100px"}},[r("el-form-item",{attrs:{label:"角色名称",prop:"name"}},[r("el-input",{model:{value:e.formData.name,callback:function(t){e.formData.name=t},expression:"formData.name"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"排序权重",prop:"rank",rules:[{required:!0,message:"权重不能为空"},{type:"number",message:"排序权重必须为数字",trigger:"blur"}]}},[r("el-input",{attrs:{placeholder:"请输入排序权重（数值越大排序越靠前）"},model:{value:e.formData.rank,callback:function(t){e.formData.rank=e._n(t)},expression:"formData.rank"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"是否启用",prop:"status"}},[r("el-switch",{attrs:{"on-text":"启用","off-text":"禁用","on-value":"1","off-value":"0"},model:{value:e.formData.status,callback:function(t){e.formData.status=t},expression:"formData.status"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"角色权限"}},[r("el-transfer",{attrs:{props:{key:"id",label:"name"},data:e.picker.allData,titles:e.picker.title},model:{value:e.formData.slimit._ids,callback:function(t){e.formData.slimit._ids=t},expression:"formData.slimit._ids"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"备注说明",prop:"remark"}},[r("el-input",{attrs:{type:"textarea"},model:{value:e.formData.remark,callback:function(t){e.formData.remark=t},expression:"formData.remark"}})],1),e._v(" "),r("el-form-item",[r("el-button",{attrs:{type:"primary"},on:{click:function(t){e.submit("roleForm")}}},[e._v("提交")]),e._v(" "),r("el-button",{on:{click:function(t){e.resetForm("roleForm")}}},[e._v("重置")])],1)],1)],1)])],1)],1)},staticRenderFns:[]}},547:function(e,t,r){var n=r(519);"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);r(197)("0648ebea",n,!0)}});