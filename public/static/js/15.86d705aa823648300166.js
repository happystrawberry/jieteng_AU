webpackJsonp([15],{NQoL:function(e,t,n){var i=n("ywn3");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);n("rjj0")("9f72011c",i,!0)},fTv5:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=n("QiB2"),a=n("0xDb"),l=n("M9A7"),r={components:{Pageing:i.a},data:function(){return{formInline:{time:""},pickerOptions0:{disabledDate:function(e){return e.getTime()>Date.now()-864e5}},rate:"",turnover:"",multipleTable:[],page:{}}},mounted:function(){this.$message({showClose:!0,message:"请点击日历查询"})},methods:{checkdate:function(e){return Object(a.a)(e)},getlist:function(e){var t=this;Object(l.a)("settlement/accountlist",e).then(function(e){t.$message({showClose:!0,message:"数据请求成功",type:"success"}),t.multipleTable=e.data.list;var n=e.data.page;for(var i in n)n[i]=parseInt(n[i]);t.page=n,t.rate=e.data.rate,t.turnover=e.data.turnover})},pagechange:function(e){this.getlist({begin_time:this.formInline.time[0]/1e3,end_time:this.formInline.time[1]/1e3,page:e})},gotodetails:function(e){var t=this.$router.resolve({name:"settlemendetails",query:{id:e}});window.open(t.href,"_blank")},donwload:function(){var e=this.formInline;this.HttpDownload("/api/settlement/accountlist?page="+this.page.curpage+"&export=1&begin_time="+(e.time?e.time[0]/1e3:"")+"&end_time="+(e.time?e.time[1]/1e3:""),"业绩列表.xls")},checksure:function(){var e=this;Object(l.a)("settlement/accounting",{begin_time:this.formInline.time?this.formInline.time[0]/1e3:"",end_time:this.formInline.time?this.formInline.time[1]/1e3:""}).then(function(t){e.$message({showClose:!0,message:"确认提成奖励成功",type:"success"})})},onSubmit:function(e){this.formInline.time?this.getlist({begin_time:this.formInline.time[0]/1e3,end_time:this.formInline.time[1]/1e3,page:1}):this.$message({showClose:!0,message:"请选先选择时间",type:"warning"})}}},o={render:function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"zone-content"},[n("el-form",{staticClass:"demo-form-inline",attrs:{inline:!0,model:e.formInline},nativeOn:{keyup:function(t){if(!("button"in t)&&e._k(t.keyCode,"enter",13,t.key))return null;e.onSubmit(t)}}},[e.formInline.time||""==e.formInline.time||null==e.formInline.time?n("el-form-item",{directives:[{name:"show",rawName:"v-show",value:!e.formInline.hidetime,expression:"!formInline.hidetime"}]},[n("el-date-picker",{attrs:{size:"mini",type:"daterange","range-separator":"至","start-placeholder":"开始日期","value-format":"timestamp","picker-options":e.pickerOptions0,"default-time":["00:00:00","23:59:59"],"end-placeholder":"结束日期"},model:{value:e.formInline.time,callback:function(t){e.$set(e.formInline,"time",t)},expression:"formInline.time"}})],1):e._e(),e._v(" "),n("el-form-item",[n("el-button",{staticStyle:{transform:"translateY(-1px)"},attrs:{size:"mini",type:"primary"},on:{click:e.onSubmit},nativeOn:{keyup:function(t){if(!("button"in t)&&e._k(t.keyCode,"enter",13,t.key))return null;e.onSubmit(t)}}},[e._v("搜索")])],1)],1),e._v(" "),e.multipleTable.length>0?n("div",{staticClass:"content-ct"},[n("div",{staticClass:"title"},[n("h3",[e._v("薪酬核算")]),e._v(" "),n("el-button",{attrs:{size:"mini",type:"primary"},on:{click:e.donwload}},[e._v("下载数据")]),e._v(" "),n("el-button",{attrs:{size:"mini",type:"primary"},on:{click:e.checksure}},[e._v("确定提成奖励")])],1),e._v(" "),n("div",{staticClass:"ct-show"},[n("p",[e._v("查询时间:"+e._s(e.checkdate(e.formInline.time[0]/1e3))+"-"+e._s(e.checkdate(e.formInline.time[1]/1e3)))]),e._v(" "),n("p",[e._v("总营业额:"+e._s(e.turnover))]),e._v(" "),n("p",[e._v("提成比例:"+e._s(100*e.rate)+"%")]),e._v(" "),n("p",[e._v("总提成金额:"+e._s(e.turnover*e.rate))])]),e._v(" "),n("h3",{staticClass:"title"},[e._v("业绩列表")]),e._v(" "),n("div",[n("el-table",{ref:"multipleTable",staticStyle:{width:"1400px"},attrs:{border:"",data:e.multipleTable,"tooltip-effect":"dark",stripe:""}},[n("el-table-column",{attrs:{label:"单号","header-align":"center",align:"center",prop:"order_sn",width:"200"}}),e._v(" "),n("el-table-column",{attrs:{label:"结算时间","header-align":"center",align:"center",prop:"order_sn"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                  "+e._s(e.checkdate(t.row.pay_time))+"\n               ")]}}])}),e._v(" "),n("el-table-column",{attrs:{prop:"username","header-align":"center",align:"center",label:"维修人员",width:"200"},scopedSlots:e._u([{key:"default",fn:function(t){return e._l(t.row.commission_list,function(t,i){return n("div",{key:i},[e._v(e._s(t.operator))])})}}])}),e._v(" "),n("el-table-column",{attrs:{prop:"total_times","header-align":"center",align:"center",label:"总工时",width:"100"}}),e._v(" "),n("el-table-column",{attrs:{prop:"order_cost","header-align":"center",align:"center",label:"总工时费",width:"100"}}),e._v(" "),n("el-table-column",{attrs:{prop:"order_cost","header-align":"center",align:"center",label:"核算状态",width:"100"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v("\n                  "+e._s(1==t.row.is_check?"已核算":"未核算")+"\n               ")]}}])}),e._v(" "),n("el-table-column",{attrs:{prop:"commission_cost","header-align":"center",align:"center",label:"提成工时费(元)",width:"200"}}),e._v(" "),n("el-table-column",{attrs:{prop:"wallet_birthday","header-align":"center",align:"center",label:"提成(元)",width:"200"},scopedSlots:e._u([{key:"default",fn:function(t){return e._l(t.row.commission_list,function(t,i){return n("div",{key:i},[e._v(e._s(t.commission))])})}}])}),e._v(" "),n("el-table-column",{attrs:{prop:"specification","header-align":"center",align:"center",label:"操作"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("el-button",{attrs:{size:"mini",type:"primary"},on:{click:function(n){e.gotodetails(t.row.id)}}},[e._v("查看详情")])]}}])})],1),e._v(" "),n("pageing",{attrs:{pageing:e.page},on:{pagechange:e.pagechange}})],1)]):e._e()],1)},staticRenderFns:[]};var s=n("VU/8")(r,o,!1,function(e){n("NQoL")},"data-v-5e2019ab",null);t.default=s.exports},ywn3:function(e,t,n){(e.exports=n("FZ+f")(!1)).push([e.i,"\n.content-ct[data-v-5e2019ab] {\n  margin-top: 40px;\n}\n.content-ct .title[data-v-5e2019ab] {\n    display: -webkit-box;\n    display: -ms-flexbox;\n    display: flex;\n    margin: 30px 0;\n    -webkit-box-align: center;\n        -ms-flex-align: center;\n            align-items: center;\n}\n.content-ct .title h3[data-v-5e2019ab] {\n      margin-right: 30px;\n}\n.content-ct .ct-show[data-v-5e2019ab] {\n    display: -webkit-box;\n    display: -ms-flexbox;\n    display: flex;\n    margin-top: 20px;\n}\n.content-ct .ct-show p[data-v-5e2019ab] {\n      -webkit-box-flex: 0;\n          -ms-flex: 0 0 300px;\n              flex: 0 0 300px;\n}\n.content-ct .ct-show p[data-v-5e2019ab]:first-of-type {\n        -webkit-box-flex: 0;\n            -ms-flex: 0 0 500px;\n                flex: 0 0 500px;\n}\n",""])}});