webpackJsonp([1],{DOUG:function(e,t,a){var n=a("nZhF");"string"==typeof n&&(n=[[e.i,n,""]]),n.locals&&(e.exports=n.locals);a("rjj0")("ad3c328a",n,!0)},HRXT:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n=a("bOdI"),i=a.n(n),l=a("QiB2"),s=a("0xDb"),r=a("M9A7"),o={components:{Pageing:l.a},methods:i()({dialogFormVisible:function(){console.log(123)},checkdate:function(e){return Object(s.a)(e)},handleSelectionChange:function(){console.log(1)},save:function(){var e=this;this.$refs.zoneinfo.validate(function(t){if(!t)return console.log("error submit!!"),!1;0!=e.info.op_type?Object(r.a)("parts/saveparts",e.info).then(function(t){e.$message({message:"材料保存成功",type:"success"})}):e.$message({message:"材料类型异常,请重试",type:"warning"})})},remark:function(){var e=this;void 0!=this.info.notes&&0!=this.info.notes.length?Object(r.a)("parts/savenotes",{id:this.info.id,notes:this.info.notes}).then(function(t){e.$message({message:"保存备注成功",type:"success"})}):this.$message({message:"备注不能为空哟",type:"warning"})},getlist:function(e){var t=this;Object(r.a)("parts/getpartsloglist",e).then(function(e){t.tabledata=e.data.list;var a=e.data.page;for(var n in a)a[n]=parseInt(a[n]);t.page=a})},pagechange:function(e){this.getlist({parts_id:this.formInline.id,is_out:this.formInline.types,end_time:this.formInline.time?this.formInline.time[1]/1e3:"",begin_time:this.formInline.time?this.formInline.time[0]/1e3:"",page:e})},search:function(){this.getlist({parts_id:this.formInline.id,is_out:this.formInline.types,end_time:this.formInline.time?this.formInline.time[1]/1e3:"",begin_time:this.formInline.time?this.formInline.time[0]/1e3:"",page:this.page.curpage})}},"checkdate",function(e){return Object(s.a)(e)}),mounted:function(){var e=this,t=this.$route.query.id;this.formInline.id=t,this.info.id=t,Object(r.a)("parts/viewparts",{id:t}).then(function(t){e.info=t.data.info,e.$message({message:"数据已更新",type:"success"})}),this.getlist({parts_id:this.formInline.id,page:1}),Object(r.a)("common/ordernotelist",{order_id:this.info.id}).then(function(t){e.textarea=t.data.list[t.data.list.length-1].nessage})},data:function(){return{rule:{sn:[{required:!0,message:"请填写材料编码",trigger:"blur"}],parts_name:[{required:!0,message:"请填写材料名称",trigger:"blur"}],target_model:[{required:!0,message:"请填写对应车型",trigger:"blur"}],spec:[{required:!0,message:"请填写材料规格",trigger:"blur"}],unit:[{required:!0,message:"请填写材料单位",trigger:"blur"}],amount:[{required:!0,message:"请填写剩余数量",trigger:"blur"}],purchase_price_tax:[{required:!0,message:"请填写材料进货单价",trigger:"blur"}],purchase_price_notax:[{required:!0,message:"请填写材料进货单价",trigger:"blur"}],sell_price:[{required:!0,message:"请填写材料出货单价",trigger:"blur"}]},textarea:"",page:{},info:{},tabledata:[],formInline:{time:"",types:"",type:[{label:"全部",value:"-1"},{label:"出库",value:"1"},{label:"入库",value:"0"}]}}}},p={render:function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"zone-content"},[a("div",{staticClass:"details-info"},[a("h3",{staticStyle:{"margin-bottom":"20px"}},[e._v("材料详情")]),e._v(" "),a("el-form",{ref:"zoneinfo",attrs:{model:e.info,rules:e.rule,"label-position":"left"}},[a("el-row",{staticClass:"input"},[a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"材料编码","label-width":"140px",prop:"sn"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{disabled:"","auto-complete":"off"},model:{value:e.info.sn,callback:function(t){e.$set(e.info,"sn",t)},expression:"info.sn"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"材料名称","label-width":"140px",prop:"parts_name"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.parts_name,callback:function(t){e.$set(e.info,"parts_name",t)},expression:"info.parts_name"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"对应车型","label-width":"140px"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.target_model,callback:function(t){e.$set(e.info,"target_model",t)},expression:"info.target_model"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"规格","label-width":"140px",prop:"spec"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.spec,callback:function(t){e.$set(e.info,"spec",t)},expression:"info.spec"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"单位","label-width":"140px",prop:"unit"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.unit,callback:function(t){e.$set(e.info,"unit",t)},expression:"info.unit"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"剩余数量","label-width":"140px",prop:"amount"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.amount,callback:function(t){e.$set(e.info,"amount",t)},expression:"info.amount"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"进货单价(含税)","label-width":"140px",prop:"purchase_price_tax"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.purchase_price_tax,callback:function(t){e.$set(e.info,"purchase_price_tax",t)},expression:"info.purchase_price_tax"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"进货单价(不含税)","label-width":"140px",prop:"purchase_price_notax"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.purchase_price_notax,callback:function(t){e.$set(e.info,"purchase_price_notax",t)},expression:"info.purchase_price_notax"}})],1)],1),e._v(" "),a("el-col",{attrs:{xs:{span:24},sm:{span:12},md:{span:12},lg:{span:8}}},[a("el-form-item",{attrs:{label:"出货单价","label-width":"140px",prop:"sell_price"}},[a("el-input",{staticStyle:{width:"210px"},attrs:{"auto-complete":"off"},model:{value:e.info.sell_price,callback:function(t){e.$set(e.info,"sell_price",t)},expression:"info.sell_price"}})],1)],1),e._v(" "),a("el-col",{staticStyle:{"text-align":"center"}},[a("el-button",{attrs:{type:"primary",size:"small"},on:{click:e.save}},[e._v("保存")])],1)],1)],1)],1),e._v(" "),a("div",{staticClass:"details-record"},[e._m(0),e._v(" "),a("div",{staticClass:"content"},[a("el-form",{staticClass:"demo-form-inline",attrs:{inline:!0,model:e.formInline}},[a("el-form-item",{attrs:{label:"状态"}},[a("el-select",{attrs:{size:"mini",placeholder:"请选择方式"},model:{value:e.formInline.types,callback:function(t){e.$set(e.formInline,"types",t)},expression:"formInline.types"}},e._l(e.formInline.type,function(e,t){return a("el-option",{key:t,staticStyle:{width:"200px"},attrs:{label:e.label,value:e.value}})}))],1),e._v(" "),a("el-form-item",[a("el-date-picker",{attrs:{size:"mini",type:"daterange","value-format":"timestamp","default-time":["00:00:00","23:59:59"],"range-separator":"至","start-placeholder":"开始日期","end-placeholder":"结束日期"},model:{value:e.formInline.time,callback:function(t){e.$set(e.formInline,"time",t)},expression:"formInline.time"}})],1),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary",size:"small"},on:{click:e.search}},[e._v("搜索")])],1)],1)],1),e._v(" "),a("div",{staticClass:"table-ct"},[a("el-table",{ref:"multipleTable",staticStyle:{width:"1500px"},attrs:{border:"",data:e.tabledata,"tooltip-effect":"dark",stripe:""},on:{"selection-change":e.handleSelectionChange}},[a("el-table-column",{attrs:{label:"订单编号","header-align":"center",align:"center",prop:"order_sn",width:"200"}}),e._v(" "),a("el-table-column",{attrs:{label:"车牌号","header-align":"center",align:"center",prop:"vehicle_number",width:"200"}}),e._v(" "),a("el-table-column",{attrs:{prop:"amount","header-align":"center",align:"center",label:"数量",width:"160"}}),e._v(" "),a("el-table-column",{attrs:{prop:"purchase_price_tax",label:"进货单价(含税)","header-align":"center",align:"center",width:"160"}}),e._v(" "),a("el-table-column",{attrs:{prop:"purchase_price_notax","header-align":"center",align:"center",label:"进货单价(不含税)",width:"160"}}),e._v(" "),a("el-table-column",{attrs:{prop:"sell_price","header-align":"center",align:"center",label:"出货单价",width:"160"}}),e._v(" "),a("el-table-column",{attrs:{prop:"time","header-align":"center",align:"center",label:"时间",width:"160"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n               "+e._s(e.checkdate(t.row.dateline))+"\n             ")]}}])}),e._v(" "),a("el-table-column",{attrs:{prop:"status","header-align":"center",align:"center",label:"状态",width:"160"}}),e._v(" "),a("el-table-column",{attrs:{prop:"operator_user","header-align":"center",align:"center",label:"操作员"}})],1),e._v(" "),a("pageing",{attrs:{pageing:e.page},on:{pagechange:e.pagechange}})],1),e._v(" "),a("div",{staticClass:"details-other"},[a("h3",{staticStyle:{margin:"30px  0"}},[e._v("备注")]),e._v(" "),a("div",{staticClass:"textarea",staticStyle:{width:"800px"}},[a("el-input",{attrs:{type:"textarea",rows:10},model:{value:e.info.notes,callback:function(t){e.$set(e.info,"notes",t)},expression:"info.notes"}}),e._v(" "),a("el-button",{attrs:{type:"primary",size:"mini"},on:{click:e.remark}},[e._v("保存")])],1)])])])},staticRenderFns:[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"header"},[t("h3",[this._v("库存记录")])])}]};var c=a("VU/8")(o,p,!1,function(e){a("DOUG")},"data-v-95cfc280",null);t.default=c.exports},nZhF:function(e,t,a){(e.exports=a("FZ+f")(!1)).push([e.i,"\n.header[data-v-95cfc280] {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  margin: 15px auto;\n  line-height: 40px;\n}\n.header h3[data-v-95cfc280] {\n    margin-right: 20px;\n}\n.header p[data-v-95cfc280] {\n    margin: 0;\n    font-size: 14px;\n}\n.input .el-input__inner[data-v-95cfc280] {\n  width: 210px !important;\n}\n.textarea[data-v-95cfc280] {\n  position: relative;\n}\n.textarea .el-button[data-v-95cfc280] {\n    position: absolute;\n    bottom: 20px;\n    right: 50px;\n}\n",""])}});