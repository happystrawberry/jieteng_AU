webpackJsonp([8],{"5wkg":function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=s("QiB2"),a=(s("0xDb"),s("M9A7")),i={components:{Pageing:n.a},methods:{getlist:function(t){var e=this;Object(a.a)("setting/gettaxrate").then(function(t){e.value=t.data.value})},complite:function(t){var e=this;""!=this.value?this.value>100||this.value<0?this.$message({showClose:!0,message:"税率设置有误,请重试",type:"warning"}):Object(a.a)("setting/edittaxrate",{val:this.value}).then(function(t){e.$message({showClose:!0,message:"修改税率成功",type:"success"}),e.getlist()}):this.$message({showClose:!0,message:"税率不能设置为空",type:"warning"})}},mounted:function(){this.getlist()},data:function(){return{value:""}}},l={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"zone-content"},[s("h3",[t._v("税率设置")]),t._v(" "),s("div",{staticClass:"content"},[s("span",[t._v("设置税率(0-100百分制)")]),t._v(" "),s("el-input",{staticStyle:{width:"100px","margin-left":"10px","margin-right":"20px"},attrs:{size:"mini",type:"number",placeholder:"请输入税率"},model:{value:t.value,callback:function(e){t.value=e},expression:"value"}}),t._v(" "),s("el-button",{staticStyle:{margin:"20px auto"},attrs:{size:"mini",type:"primary"},on:{click:t.complite}},[t._v("确定")])],1)])},staticRenderFns:[]};var o=s("VU/8")(i,l,!1,function(t){s("97++")},null,null);e.default=o.exports},"8IUt":function(t,e,s){(t.exports=s("FZ+f")(!1)).push([t.i,"",""])},"97++":function(t,e,s){var n=s("8IUt");"string"==typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);s("rjj0")("2785b690",n,!0)}});