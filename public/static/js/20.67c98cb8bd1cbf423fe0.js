webpackJsonp([20],{"6p/2":function(e,t,i){(e.exports=i("FZ+f")(!1)).push([e.i,"\n.table-header[data-v-3d921bd5] {\n  margin-bottom: 30px;\n}\n.el-checkbox + .el-checkbox[data-v-3d921bd5] {\n  margin-left: 0px;\n}\n.el-checkbox[data-v-3d921bd5] {\n  margin-right: 30px;\n}\n",""])},yIh8:function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var l=i("QiB2"),a=i("M9A7"),o={components:{Pageing:l.a},data:function(){return{rules2:{group_name:[{validator:function(e,t,i){if(!t)return i(new Error("部门名称不能为空"));i()},trigger:"blur"}]},loadings:!0,detailform:{id:0,group_name:"",taglist:[],region:[],newlist:[],params:[],perms:"",type:[]},multipleTable:[],getpermslist:{},page:{},typelist:[],title:["修改部门","新建部门"],dialogFormVisible:{showkey:!1,form:{id:"",group_name:"",taglist:[],region:[],newlist:[],params:[],perms:"",type:[]}}}},methods:{addmaterials:function(e,t){for(var i in this.dialogFormVisible.form)this.dialogFormVisible.form[i]=e[i];this.dialogFormVisible.showkey=!0},surecheck:function(e){console.log(e.row.id),Object(a.a)("account/switchGroupStatus",{id:e.row.id}).then(function(t){e.row.visible=!1,e.row.enable=t.data.enable})},saveaccount:function(){var e=this,t=this.dialogFormVisible.form,i=this;this.$refs.ruleForm2.validate(function(l){if(!l)return console.log("error submit!!"),!1;if(0!=e.dialogFormVisible.form.newlist.length)if(0!=t.type.length){var o=[],s=[],n=[];for(var r in e.getpermslist)t.newlist.indexOf(e.getpermslist[r])>-1&&o.push(r);for(var c in console.log("type",t.type),e.typelist)t.type.indexOf(e.typelist[c].name)>-1&&(s.push(e.typelist[c].id),n.push(e.typelist[c].name));t.types=n,t.perms=o.join(","),Object(a.a)("/account/savegroup",{id:t.id,group_name:t.group_name,items:s.join(","),perms:o.join(",")}).then(function(l){if(e.$message({showClose:!0,message:t.id?"修改用户组成功":"新建用户组成功",type:"success"}),0!=t.id){var a=e.multipleTable.findIndex(function(e){return e.id==t.id});for(var o in e.multipleTable[a])t[o]&&(e.multipleTable[a][o]=t[o]);console.log(e.multipleTable[0].type,t.type),i.checkstatsu(e.multipleTable)}else e.getlist();e.dialogFormVisible.showkey=!1})}else e.$message({showClose:!0,message:"请先勾选业务类型分组",type:"warning"});else e.$message({showClose:!0,message:"请先勾选可查看列表组",type:"warning"})})},handleSelectionChange:function(){console.log("啦啦啦")},pagechange:function(e){this.getlist({page:e})},checkstatsu:function(e){for(var t in this.multipleTable){var i=e[t].perms.split(","),l=[],a=[];for(var o in this.multipleTable[t].params=i,i)l.push({text:this.getpermslist[i[o]],value:i[o]}),a.push(this.getpermslist[i[o]]);var s=[];for(var n in this.multipleTable[t].types)s.push(this.multipleTable[t].types[n]);this.multipleTable[t].taglist=l,this.multipleTable[t].type=s,this.multipleTable[t].newlist=a}console.log(this.multipleTable[0])},getlist:function(e){var t=this;Object(a.a)("/account/grouplist",e).then(function(e){t.multipleTable=e.data.list;var i=e.data.page;for(var l in i)i[l]=parseInt(i[l]);t.page=i,t.checkstatsu(e.data.list),t.loadings=!1})}},mounted:function(){var e=this;Object(a.a)("/account/getpermslist").then(function(t){e.getpermslist=t.data.list,e.getlist()}),Object(a.a)("/common/getoptypelist").then(function(t){e.typelist=t.data.list})}},s={render:function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"zone-content"},[i("div",{staticClass:"table"},[i("div",{staticClass:"table-header"},[i("h3",[e._v("部门组列表")]),e._v(" "),i("div",[i("el-button",{attrs:{size:"mini",type:"primary"},on:{click:function(t){e.addmaterials(e.detailform)}}},[e._v("添加部门")])],1)]),e._v(" "),i("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.loadings,expression:"loadings"}],ref:"multipleTable",staticStyle:{width:"100%"},attrs:{border:"",data:e.multipleTable,"tooltip-effect":"dark",stripe:""},on:{"selection-change":e.handleSelectionChange}},[i("el-table-column",{attrs:{label:"序号","header-align":"center",align:"center",prop:"id",width:"120"}}),e._v(" "),i("el-table-column",{attrs:{prop:"group_name","header-align":"center",align:"center",label:"部门",width:"200"}}),e._v(" "),i("el-table-column",{attrs:{prop:"phone","header-align":"center",align:"center",label:"可查看列表组"},scopedSlots:e._u([{key:"default",fn:function(t){return e._l(t.row.taglist,function(t,l){return i("el-tag",{key:l,staticStyle:{margin:"5px"},attrs:{type:"success"}},[e._v(e._s(t.text))])})}}])}),e._v(" "),i("el-table-column",{attrs:{prop:"types","header-align":"center",align:"center",label:"业务类型分组","show-overflow-tooltip":""},scopedSlots:e._u([{key:"default",fn:function(t){return e._l(t.row.types,function(t,l){return i("el-tag",{key:l,staticStyle:{margin:"0 10px"},attrs:{type:"success"}},[e._v(e._s(t))])})}}])}),e._v(" "),i("el-table-column",{attrs:{prop:"specification","header-align":"center",align:"center",label:"操作",width:"200"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("el-button",{attrs:{size:"mini",type:"primary"},on:{click:function(i){e.addmaterials(t.row,1)}}},[e._v("编辑")]),e._v(" "),i("el-popover",{attrs:{placement:"top",width:"160"},model:{value:t.row.visible,callback:function(i){e.$set(t.row,"visible",i)},expression:"scope.row.visible"}},[i("p",[e._v("真的要"+e._s(0==t.row.enable?"恢复":"禁用")+e._s(t.row.username)+"吗?")]),e._v(" "),i("div",{staticStyle:{"text-align":"right",margin:"0"}},[i("el-button",{attrs:{size:"mini",type:"text"},on:{click:function(e){t.row.visible=!1}}},[e._v("取消")]),e._v(" "),i("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(i){e.surecheck(t)}}},[e._v("确定")])],1),e._v(" "),i("el-button",{attrs:{slot:"reference",type:0==t.row.enable?"success":"danger",size:"mini"},slot:"reference"},[e._v(e._s(0==t.row.enable?"恢复":"禁用"))])],1)]}}])})],1)],1),e._v(" "),i("el-dialog",{attrs:{title:0==e.dialogFormVisible.form.id?"新建部门":"修改部门",width:"50%",visible:e.dialogFormVisible.showkey},on:{"update:visible":function(t){e.$set(e.dialogFormVisible,"showkey",t)}}},[i("el-form",{ref:"ruleForm2",attrs:{model:e.dialogFormVisible.form,rules:e.rules2,"label-position":"left"}},[i("el-row",[i("el-col",[i("el-form-item",{attrs:{label:"部门名称","label-width":"140px",prop:"group_name"}},[i("el-input",{staticStyle:{width:"210px"},model:{value:e.dialogFormVisible.form.group_name,callback:function(t){e.$set(e.dialogFormVisible.form,"group_name",t)},expression:"dialogFormVisible.form.group_name"}})],1)],1),e._v(" "),i("el-col",[i("el-form-item",{attrs:{label:"可查看列表组","label-width":"140px"}},[i("el-checkbox-group",{model:{value:e.dialogFormVisible.form.newlist,callback:function(t){e.$set(e.dialogFormVisible.form,"newlist",t)},expression:"dialogFormVisible.form.newlist"}},e._l(e.getpermslist,function(e,t,l){return i("el-checkbox",{key:l,attrs:{label:e,name:e}})}))],1)],1),e._v(" "),i("el-col",[i("el-form-item",{attrs:{label:"业务类型分组","label-width":"140px"}},[i("el-checkbox-group",{model:{value:e.dialogFormVisible.form.type,callback:function(t){e.$set(e.dialogFormVisible.form,"type",t)},expression:"dialogFormVisible.form.type"}},e._l(e.typelist,function(e,t){return i("el-checkbox",{key:t,attrs:{label:e.name,name:e.value}})}))],1)],1),e._v(" "),i("el-col",{staticStyle:{"text-align":"center"}},[i("el-button",{attrs:{type:"primary"},on:{click:function(t){e.saveaccount()}}},[e._v("保存")])],1)],1)],1)],1),e._v(" "),i("pageing",{directives:[{name:"show",rawName:"v-show",value:e.page.totalnum,expression:"page.totalnum"}],attrs:{pageing:e.page},on:{pagechange:e.pagechange}})],1)},staticRenderFns:[]};var n=i("VU/8")(o,s,!1,function(e){i("z//U")},"data-v-3d921bd5",null);t.default=n.exports},"z//U":function(e,t,i){var l=i("6p/2");"string"==typeof l&&(l=[[e.i,l,""]]),l.locals&&(e.exports=l.locals);i("rjj0")("3f2886bf",l,!0)}});