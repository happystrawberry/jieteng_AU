webpackJsonp([25],{"2yKw":function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=i("QiB2"),a=(i("0xDb"),i("M9A7")),o={components:{Pageing:n.a},methods:{getlist:function(e){var t=this;Object(a.a)("setting/commissionlist",e).then(function(e){t.$message({message:"数据已更新",type:"success"}),t.list=e.data.list;var i=e.data.page;for(var n in i)i[n]=parseInt(i[n]);t.page=i})},pagechange:function(e){this.getlist({page:e})},danger:function(e){var t=this;e.row.visible=!1,Object(a.a)("setting/deletecommission",{id:e.row.id}).then(function(e){t.$notify({title:"成功",message:"删除提成规则成功",type:"success"}),t.getlist({page:1})})},create:function(){console.log()},surecheck:function(){var e=this;parseInt(this.form.min)>=parseInt(this.form.max)?this.$message({message:"截止金额应大于起始金额",type:"warning"}):this.form.rate>100||this.form.rate<0?this.$message({message:"提成比例应在0-100之内",type:"warning"}):Object(a.a)("setting/savecommission",{id:this.form.id,min:this.form.min,max:this.form.max,rate:this.form.rate/100}).then(function(t){if(e.dialogFormVisible=!1,e.$notify({title:"成功",message:(e.titlekey?"新建":"编辑")+"提成规则成功",type:"success"}),e.titlekey)e.getlist({page:1});else{var i=e.list.findIndex(function(t){return t.id==e.form.id});e.list[i].min_turnover=e.form.min,e.list[i].max_turnover=e.form.max,e.list[i].commission_rate=e.form.rate/100}})},complite:function(e){var t={};e?(t={id:e.row.id,min:e.row.min_turnover,max:e.row.max_turnover,rate:100*e.row.commission_rate},this.titlekey=!1):(t={id:0,min:"",max:"",rate:""},this.titlekey=!0),this.form=t,this.dialogFormVisible=!0}},mounted:function(){this.getlist()},data:function(){return{list:[],page:{},titlekey:!1,form:{id:"",min:"",max:"",rate:""},dialogFormVisible:!1}}},r={render:function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"zone-content"},[i("h3",[e._v("维修阶梯提成设置")]),e._v(" "),i("el-button",{staticStyle:{margin:"20px auto"},attrs:{size:"mini",type:"primary"},on:{click:function(t){e.complite(!1)}}},[e._v("添加提成规则")]),e._v(" "),i("div",{staticClass:"table"},[i("el-table",{staticStyle:{width:"800px"},attrs:{data:e.list,border:"",align:"center"}},[i("el-table-column",{attrs:{prop:"id",label:"编号",align:"center","header-align":"center"}}),e._v(" "),i("el-table-column",{attrs:{prop:"bill_time",label:"总营业额范围",align:"center","header-align":"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                "+e._s(t.row.min_turnover+"一"+t.row.max_turnover)+"\n          ")]}}])}),e._v(" "),i("el-table-column",{attrs:{label:"提成比例",prop:"total_cost",align:"center","header-align":"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                "+e._s(100*parseFloat(t.row.commission_rate).toFixed(2))+"%\n          ")]}}])}),e._v(" "),i("el-table-column",{attrs:{prop:"num",label:"操作",align:"center","header-align":"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("el-button",{attrs:{size:"mini",type:"primary"},on:{click:function(i){e.complite(t)}}},[e._v("编辑")]),e._v(" "),i("el-popover",{attrs:{placement:"top",width:"200"},model:{value:t.row.visible,callback:function(i){e.$set(t.row,"visible",i)},expression:"scope.row.visible"}},[i("p",[e._v("确定删除该计分规则吗?")]),e._v(" "),i("div",{staticStyle:{"text-align":"right",margin:"0"}},[i("el-button",{attrs:{size:"mini",type:"text"},on:{click:function(e){t.row.visible=!1}}},[e._v("取消")]),e._v(" "),i("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(i){e.danger(t)}}},[e._v("确定")])],1),e._v(" "),i("el-button",{attrs:{slot:"reference",type:"danger",size:"mini"},slot:"reference"},[e._v("删除")])],1)]}}])})],1),e._v(" "),i("pageing",{directives:[{name:"show",rawName:"v-show",value:e.page.totalnum,expression:"page.totalnum"}],attrs:{pageing:e.page},on:{pagechange:e.pagechange}})],1),e._v(" "),i("el-dialog",{attrs:{title:e.titlekey?"新建提成规则":"编辑提成规则",center:"",width:"500px",visible:e.dialogFormVisible},on:{"update:visible":function(t){e.dialogFormVisible=t}}},[i("el-form",{attrs:{model:e.form,"label-position":"left"}},[i("el-form-item",{attrs:{label:"起始金额","label-width":"120px"}},[i("el-input",{model:{value:e.form.min,callback:function(t){e.$set(e.form,"min",t)},expression:"form.min"}})],1),e._v(" "),i("el-form-item",{attrs:{label:"截止金额","label-width":"120px"}},[i("el-input",{model:{value:e.form.max,callback:function(t){e.$set(e.form,"max",t)},expression:"form.max"}})],1),e._v(" "),i("el-form-item",{attrs:{label:"提成比例(0-100)","label-width":"120px"}},[i("el-input",{model:{value:e.form.rate,callback:function(t){e.$set(e.form,"rate",t)},expression:"form.rate"}})],1)],1),e._v(" "),i("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[i("el-button",{on:{click:function(t){e.dialogFormVisible=!1}}},[e._v("取 消")]),e._v(" "),i("el-button",{attrs:{type:"primary"},on:{click:e.surecheck}},[e._v("确 定")])],1)],1)],1)},staticRenderFns:[]};var s=i("VU/8")(o,r,!1,function(e){i("XuWn")},null,null);t.default=s.exports},XuWn:function(e,t,i){var n=i("abR5");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);i("rjj0")("71def870",n,!0)},abR5:function(e,t,i){(e.exports=i("FZ+f")(!1)).push([e.i,"",""])}});