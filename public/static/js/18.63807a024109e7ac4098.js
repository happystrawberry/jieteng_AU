webpackJsonp([18],{"+yKN":function(t,e,i){(t.exports=i("FZ+f")(!1)).push([t.i,"\n.el-tag + .el-tag[data-v-43a586d2] {\n  margin-left: 10px;\n}\n.button-new-tag[data-v-43a586d2] {\n  margin-left: 10px;\n  height: 32px;\n  line-height: 30px;\n  padding-top: 0;\n  padding-bottom: 0;\n}\n.input-new-tag[data-v-43a586d2] {\n  width: 90px;\n  margin-left: 10px;\n  vertical-align: bottom;\n}\n.details .item[data-v-43a586d2] {\n  width: 100%;\n  overflow: hidden;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap;\n  margin-top: 20px;\n  margin-bottom: 30px;\n}\n.details .item .left[data-v-43a586d2] {\n    display: inline-block;\n    margin-right: 20px;\n    display: -webkit-box;\n    display: -ms-flexbox;\n    display: flex;\n    -webkit-box-align: center;\n        -ms-flex-align: center;\n            align-items: center;\n    margin-bottom: 20px;\n}\n.details .item .right[data-v-43a586d2] {\n    display: inline-block;\n}\n.details .item .right .el-tag[data-v-43a586d2] {\n      margin-right: 20px;\n}\n.details .item .right .el-textarea[data-v-43a586d2] {\n      width: 400px;\n}\n.details .item.other .right[data-v-43a586d2] {\n    -webkit-box-flex: 0;\n        -ms-flex: 0 0 100%;\n            flex: 0 0 100%;\n    padding-left: 60px;\n    display: -webkit-box;\n    display: -ms-flexbox;\n    display: flex;\n}\n.details .item.other .ct[data-v-43a586d2] {\n    -webkit-box-flex: 0;\n        -ms-flex: 0 0 400px;\n            flex: 0 0 400px;\n}\n",""])},"2YZR":function(t,e,i){var a=i("+yKN");"string"==typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);i("rjj0")("16126b1a",a,!0)},wGWa:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=i("M9A7"),n={data:function(){return{info:{},dialog:{showkey:!1,result:"",checkList:[]},dynamicTags:[],inputVisible:!1,inputValue:"",chartData:{columns:["答案","占比份数"],rows:[]}}},mounted:function(t){console.log("query",this.$route.query.id),this.getlist(this.$route.query.id)},methods:{add:function(){this.dialog.result=this.info.question;var t=[],e=JSON.parse(this.info.need_cause);for(var i in e)console.log(e[i]),1==e[i]&&this.dialog.checkList.push(parseInt(i));for(var a in this.info.options)t.push(this.info.options[a]);this.dynamicTags=t,this.dialog.showkey=!0},handleClose:function(t){this.dynamicTags.splice(this.dynamicTags.indexOf(t),1)},showInput:function(){var t=this;this.inputVisible=!0,this.$nextTick(function(e){t.$refs.saveTagInput.$refs.input.focus()})},handleInputConfirm:function(){var t=this.inputValue;t&&this.dynamicTags.push(t),this.inputVisible=!1,this.inputValue=""},getlist:function(t){var e=this;Object(a.a)("service/viewquestion",{id:t}).then(function(t){e.info=t.data.info;var i=[];for(var a in t.data.info.stat)i.push({"答案":t.data.info.stat[a].option,"占比份数":parseInt(t.data.info.stat[a].num)});e.chartData.rows=i,e.$message({message:"数据已更新",type:"success"})})},sureckeck:function(t){var e=this;if(0!=this.dialog.result.length)if(0!=this.dynamicTags.length){var i=[];for(var n in this.dynamicTags)this.dialog.checkList.indexOf(this.dynamicTags[n]>-1)?i.push(1):i.push(0);this.dialog.showkey=!1,Object(a.a)("service/savequestion",{id:this.info.id,question:this.dialog.result,options:this.dynamicTags,cause:i}).then(function(t){e.getlist(e.$route.query.id)})}else this.$message({message:"是不是忘记写答案了",type:"warning"});else this.$message({message:"请填写问题题目",type:"warning"})}}},s={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"zone-content "},[i("div",{staticClass:"details"},[i("div",{staticClass:"item"},[t._m(0),t._v(" "),i("div",{staticClass:"right"},[i("el-input",{attrs:{type:"textarea",rows:6,placeholder:"请输入问题内容"},model:{value:t.info.question,callback:function(e){t.$set(t.info,"question",e)},expression:"info.question"}}),t._v(" "),i("el-button",{staticStyle:{"margin-left":"20px",transform:"translateY(-50px)"},attrs:{size:"mini",type:"danger"},on:{click:t.add}},[t._v("修改问题")])],1)]),t._v(" "),i("div",{staticClass:"item "},[t._m(1),t._v(" "),i("div",{staticClass:"right"},t._l(t.info.options,function(e,a){return i("el-tag",{key:a},[t._v("\n             "+t._s(e)+"\n           ")])}))]),t._v(" "),i("div",{staticClass:"item other"},[t._m(2),t._v(" "),i("div",{staticClass:"right"},[0==t.info.stat.length?i("p",{staticStyle:{"text-align":"center",width:"400px"}},[t._v("暂无统计数据")]):t._e(),t._v(" "),t.info.stat.length>0?i("div",{staticClass:"ct"},[i("ve-pie",{attrs:{data:t.chartData}})],1):t._e(),t._v(" "),i("div",{staticClass:"tips"},[t.info.stat.length>0?i("el-table",{staticStyle:{width:"300px"},attrs:{data:t.info.stat,border:"",align:"center"}},[i("el-table-column",{attrs:{prop:"option",label:"答案",align:"center","header-align":"center",width:"100"}}),t._v(" "),i("el-table-column",{attrs:{prop:"num",label:"占比份数",align:"center","header-align":"center",width:"100"}}),t._v(" "),i("el-table-column",{attrs:{prop:"percentage",align:"center","header-align":"center",label:"占比"}})],1):t._e()],1)])])]),t._v(" "),i("el-dialog",{attrs:{center:"",width:"600px",title:"添加新的答案",visible:t.dialog.showkey},on:{"update:visible":function(e){t.$set(t.dialog,"showkey",e)}}},[i("el-form",{ref:"temporarys",attrs:{"label-position":"left",model:t.dialog},nativeOn:{submit:function(t){t.preventDefault()}}},[i("el-form-item",{attrs:{label:"新增问题","label-width":"120px"}},[i("el-input",{attrs:{type:"textarea",rows:2,placeholder:"请输入内容"},model:{value:t.dialog.result,callback:function(e){t.$set(t.dialog,"result",e)},expression:"dialog.result"}})],1),t._v(" "),i("el-form-item",{attrs:{label:"答案(勾选即为需要输入原因)","label-width":"120px"}},[i("el-checkbox-group",{model:{value:t.dialog.checkList,callback:function(e){t.$set(t.dialog,"checkList",e)},expression:"dialog.checkList"}},[t._l(t.dynamicTags,function(e,a){return i("el-checkbox",{key:a,staticClass:"otherel",attrs:{label:a}},[i("el-tag",{attrs:{closable:"","disable-transitions":!1},on:{close:function(i){t.handleClose(e)}}},[t._v("\n      "+t._s(e)+"\n    ")])],1)}),t._v(" "),t.inputVisible?i("el-input",{ref:"saveTagInput",staticClass:"input-new-tag",attrs:{size:"small"},on:{blur:t.handleInputConfirm},nativeOn:{keyup:function(e){if(!("button"in e)&&t._k(e.keyCode,"enter",13,e.key))return null;t.handleInputConfirm(e)}},model:{value:t.inputValue,callback:function(e){t.inputValue=e},expression:"inputValue"}}):i("el-button",{staticClass:"button-new-tag",attrs:{size:"small",type:"primary"},on:{click:t.showInput}},[t._v("新增答案")])],2)],1)],1),t._v(" "),i("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[i("el-button",{attrs:{size:"small"},on:{click:function(e){t.dialog.showkey=!1}}},[t._v("取 消")]),t._v(" "),i("el-button",{attrs:{size:"small",type:"primary"},on:{click:function(e){t.sureckeck(t.dialog)}}},[t._v("确 定")])],1)],1)],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"left"},[e("h3",[this._v("问题")])])},function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"left"},[e("h3",[this._v("答案")])])},function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"left"},[e("h3",[this._v("统计")])])}]};var l=i("VU/8")(n,s,!1,function(t){i("2YZR")},"data-v-43a586d2",null);e.default=l.exports}});