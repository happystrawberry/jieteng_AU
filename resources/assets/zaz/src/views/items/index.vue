<template>
   <div class="zone-content index items">
       <el-form :inline="true" :model="items" class="demo-form-inline" >
            <el-input  style="width:210px;" class="item" size="mini" v-model="items.user" placeholder="请输入项目名称"></el-input>
            <el-button class="item"   type="primary"  size="mini" @click.native="onSubmit">查询</el-button>
        </el-form>
         <div class="table">
          <div class="table-header">
            <h2>维修项目</h2>
            <div>
                <el-button type="primary" size="mini" @click="addmaterials">添加项目</el-button>
                <el-upload
                class="upload-demo"
                action="/api/items/import"
                :on-success="success"
                :on-error="error"
                :show-file-list=false
                accept="excel">
                <el-button size="mini" type="primary">点击上传</el-button>
              </el-upload>
            </div>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="multipleTable"
            tooltip-effect="dark"
            v-loading="loading"
            style="width:100%"
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="id"
              width="70">
            </el-table-column>
            <el-table-column
              prop="op_number"
              header-align="center"
              align="center"
              label="项目编码"
              width="100">
            </el-table-column>
            
            <el-table-column
              prop="item_name"
              header-align="center"
              align="center"
              label="业务名称"
              width="200"
              >
            </el-table-column>
             <el-table-column
              prop="a0_times"
              header-align="center"
              align="center"
              label="A0标准工时"
              >
            </el-table-column>
              <el-table-column
              prop="a_times"
              header-align="center"
              align="center"
              label="A标准工时"
              >
            </el-table-column>
              <el-table-column
              prop="b_times"
              header-align="center"
              align="center"
              label="B标准工时"
              >
            </el-table-column>
              <el-table-column
              prop="c_times"
              header-align="center"
              align="center"
              label="C标准工时"
              >
            </el-table-column>
              <el-table-column
              prop="d_times"
              header-align="center"
              align="center"
              label="D标准工时"
              >
            </el-table-column>
             <el-table-column
              prop="a0_cost"
              header-align="center"
              align="center"
              label="A0工时费"
              >
            </el-table-column>
             <el-table-column
              prop="a_cost"
              header-align="center"
              align="center"
              label="A工时费"
              >
            </el-table-column>
              <el-table-column
              prop="b_cost"
              header-align="center"
              align="center"
              label="B工时费"
              >
            </el-table-column>
              <el-table-column
              prop="c_cost"
              header-align="center"
              align="center"
              label="C工时费"
              >
            </el-table-column>
              <el-table-column
              prop="d_cost"
              header-align="center"
              align="center"
              label="D工时费"
              >
            </el-table-column>
            <el-table-column
              prop="check"
              header-align="center"
              align="center"
              label="操作"
              width="300"
              >
               <template slot-scope="scope">
                    <el-button size="mini" type="primary" @click="compiled(scope)">编辑</el-button>
               <el-popover
                            placement="top"
                            width="160"
                            v-model="scope.row.visible">
                            <p>确认{{scope.row.enable==1?'关闭':'开启'}}{{scope.row.vehicle_name}}车型吗？</p>
                            <div style="text-align: right; margin: 0">
                                <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                                <el-button type="primary" size="mini" @click="suredetale(scope)">确定</el-button>
                            </div>
                            <el-button slot="reference" size="mini" :type="scope.row.enable==1?'danger':'succeed'">{{scope.row.enable==1?'关闭':'开启'}}</el-button>
                            </el-popover>
                  <a target="_blank" :href="'/api/common/getcodebar?sn='+scope.row.op_number">           
                 <el-button size="mini" type="success">打印编码</el-button>
                  </a>
              </template>
            </el-table-column>
          </el-table>
        </div>
        <el-dialog 
       :title="title"
       width="50%"
       @close="dialogclose"
       :visible.sync="dialogFormVisible.showkey">
        <el-form :model="dialogFormVisible.form"
            :rules="rules2"
            ref="rule"
            label-position="left">
            <el-row>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12} prop="op_number">
                    <el-form-item prop="op_number" label="项目编码" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.op_number" auto-complete="off" placeholder="请填写项目编码"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item prop="item_name" label="项目名称" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.item_name" placeholder="请填写项目名称" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item prop="a0_times" label="A0标准工时" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.a0_times" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="a_times" label="A标准工时" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.a_times" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item   prop="b_times"  label="B标准工时" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.b_times" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="c_times" label="C标准工时" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.c_times" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="d_times" label="D标准工时" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.d_times" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="a0_cost" label="A0工时费" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.a0_cost" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="a_cost" label="A工时费" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.a_cost" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="b_cost" label="B工时费" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.b_cost" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="c_cost" label="C工时费" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.c_cost" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item  prop="d_cost" label="D工时费" label-width="140px">
                        <el-input v-model="dialogFormVisible.form.d_cost" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col style="text-align:center">
                    <el-button type="primary" @click="overadd()">{{compile?'保存':'新建'}}</el-button>
                </el-col>
            </el-row>
        </el-form>
        </el-dialog>
                  <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>

   </div>
</template>

<script>
import Iconlist from "@/components/Iconlist/Iconlist";
import Comheader from "@/components/comHeader/comheader";
import Pageing from "@/components/Paging/Paging";

import { Axios } from "@/api/login";

export default {
  name: "zhijian",
  components: {
    Iconlist,
    Comheader,
    Pageing
  },
  mounted() {
    this.getlist();
    // 获取下拉选择框
    Axios("common/getoptypelist").then(res => {
      this.getoptypelist = res.data.list;
    });
  },
  methods: {
    details(index) {
      this.$router.push({ name: "settlemendetails", query: { id: index } });
    },
    dialogclose() {
      let that = this;
      setTimeout(() => {
        that.$refs.rule.resetFields();
      }, 500);
    },
     error(res){
        this.$notify.error({
          title: '错误',
          message:"导入文档失败,请重试",
          duration: 4500
        });  
    },
    success(res) {
      if (res.code == 200) {
        this.$notify({
          title: "成功",
          message: "导入文档成功",
          type: "success",
          duration: 4500
        });
        this.getlist();
        // 获取下拉选择框
        Axios("common/getoptypelist").then(res => {
          this.getoptypelist = res.data.list;
        });
      } else {
        this.$notify({
          title: "警告",
          message: res.msg,
          type: "warning",
          duration: 4500
        });
      }
    },
    importfxx(obj) {
      let _this = this;
      console.log("xxxxxxxxxxxxxxxxxxxxxxxxxxx1");
      let inputDOM = this.$refs.inputer;
      // 通过DOM取文件数据

      this.file = event.currentTarget.files[0];

      var rABS = false; //是否将文件读取为二进制字符串
      var f = this.file;

      var reader = new FileReader();
      //if (!FileReader.prototype.readAsBinaryString) {
      FileReader.prototype.readAsBinaryString = function(f) {
        var binary = "";
        var rABS = false; //是否将文件读取为二进制字符串
        var pt = this;
        var wb; //读取完成的数据
        var outdata;
        var reader = new FileReader();
        reader.onload = function(e) {
          var bytes = new Uint8Array(reader.result);
          var length = bytes.byteLength;
          for (var i = 0; i < length; i++) {
            binary += String.fromCharCode(bytes[i]);
          }
          var XLSX = require("xlsx");
          if (rABS) {
            wb = XLSX.read(btoa(fixdata(binary)), {
              //手动转化
              type: "base64"
            });
          } else {
            wb = XLSX.read(binary, {
              type: "binary"
            });
          }
          outdata = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]); //outdata就是你想要的东西
          Axios("items/import", {
            excel: outdata
          }).then(res => {
            console.log(res);
          });

          //这里转换下
        };
        reader.readAsArrayBuffer(f);
      };
      if (rABS) {
        reader.readAsArrayBuffer(f);
      } else {
        reader.readAsBinaryString(f);
      }
    },
    onSubmit(e) {
      console.log(e);
      this.getlist({
        name: this.items.user,
        op_type: this.items.region,
        page: 1
      });
    },
    compiled(scope) {
      console.log("这是我的id", scope.row.id);
      let that = this;
      this.compile = true;
      // that.$refs.rule[0].;
      let ele = this.dialogFormVisible;
      ele.showkey = true;
      this.title = "编辑维修项目";
      for (let i in this.dialogFormVisible.form) {
        console.log(i);
        this.dialogFormVisible.form[i] = scope.row[i];
      }
      this.dialogFormVisible.form.brand_id = scope.row.id;

      // this.$nextTick(() => {
      //   that.$refs.rule.resetFields();
      // });
    },
    overadd() {
      this.$refs.rule.validate(valid => {
        if (valid) {
          this.dialogFormVisible.showkey = false;
          //这里区分下是关闭还是开启
          Axios("items/save", this.dialogFormVisible.form).then(res => {
            console.log(this.compile);
            this.$message({
              message: `恭喜,${this.compile ? "编辑" : "新建"}品牌成功`,
              type: "success"
            });
            if (!this.compile) {
              this.getlist();
            } else {
              var index = this.multipleTable.findIndex(ele => {
                return (ele.id = this.dialogFormVisible.form.id);
              });
              for (let i in this.multipleTable[index]) {
                this.multipleTable[index][i] = this.dialogFormVisible.form[i];
              }
            }
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    handleSelectionChange() {
      console.log(123);
    },
    addmaterials() {
      let that = this;
      this.dialogFormVisible.showkey = true;
      this.title = "新建维修项目";
      for (let i in this.dialogFormVisible.form) {
        this.dialogFormVisible.form[i] = "";
      }
      //  this.$nextTick(()=>{
      //      this.$refs.rule.resetFields();
      //  })
    },
    pagechange(value) {
      this.getlist({
        name: this.formInline.user,
        op_type: this.items.region,
        page: value
      });
    },
    suredetale(scope) {
      //这里区分下是关闭还是开启
      scope.row.visible = false;
      let url = scope.row.enable == 1 ? "items/disable" : "items/enable";
      Axios(url, {
        id: scope.row.id
      }).then(res => {
        this.$message({
          showClose: true,
          message: `${scope.row.enable == 1 ? "关闭" : "开启"}项目成功`,
          type: "success"
        });
        scope.row.enable = scope.row.enable == 1 ? 0 : 1;
      });
    },
    getlist(params) {
      this.loading = true;
      Axios("items/list", params).then(res => {
        this.loading = false;
        let list = res.data.list;
        for (let i in res.data.list) {
          list[i].visible = false;
        }
        this.multipleTable = list;
        let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        this.$message({
          showClose: true,
          message: "列表数据已更新",
          type: "success"
        });
      });
    }
  },
  data() {
    var contentname = (rule, value, callback) => {
      if (value == "") {
        callback(new Error("请先填写数据"));
      }
    };
    var contentnum = (rule, value, callback) => {
      if (value == "") {
        callback(new Error("请先填写数据"));
      } else if (isNaN(value)) {
        callback(new Error("请不要输入非数字以外字符"));
      }
    };
    return {
      items: {
        user: "",
        region: ""
      },
      compile: "",
      title: "",
      formInline: {},
      getoptypelist: [],
      loading: true,
      page: {},
      rules2: {
        op_number: [
          { required: true, message: "请填写项目编码", trigger: "blur" }
        ],
        op_type: [
          { required: true, message: "请填写项目类型", trigger: "change" }
        ],
        id: [{ required: true, message: "请填写数据", trigger: "blur" }],
        item_name: [{ required: true, message: "请填写数据", trigger: "blur" }],
        a0_times: [{ required: true, message: "请填写数据", trigger: "blur" }],
        a_times: [{ required: true, message: "请填写数据", trigger: "blur" }],
        b_times: [{ required: true, message: "请填写数据", trigger: "blur" }],
        c_times: [{ required: true, message: "请填写数据", trigger: "blur" }],
        d_times: [{ required: true, message: "请填写数据", trigger: "blur" }],
        a0_cost: [{ required: true, message: "请填写数据", trigger: "blur" }],
        a_cost: [{ required: true, message: "请填写数据", trigger: "blur" }],
        b_cost: [{ required: true, message: "请填写数据", trigger: "blur" }],
        c_cost: [{ required: true, message: "请填写数据", trigger: "blur" }],
        d_cost: [{ required: true, message: "请填写数据", trigger: "blur" }]
      },
      dialogFormVisible: {
        showkey: false,
        form: {
          id: "",
          brand_id: "",
          op_number: "",
          op_type:"",
          item_name: "",
          a0_times: "",
          a_times: "",
          b_times: "",
          c_times: "",
          d_times: "",
          a0_cost: "",
          a_cost: "",
          b_cost: "",
          c_cost: "",
          d_cost: "",
          enable: "",
          dateline: "",
          type_name: ""
        }
      },
      data: [
        {
          name: "我的任务",
          num: 1111
        },
        {
          name: "我的任务",
          num: 1
        },
        {
          name: "我的任务",
          num: 1
        }
      ],
      multipleTable: []
    };
  }
};
</script>

<style  lang="scss">
.item {
  display: inline-block !important;
  margin-right:20px;
}
.items {
  .upload-demo {
    display: inline-block;
    margin-left: 20px;
    > div {
      margin-left: 0;
    }
  }
  .importfxx {
    display: inline-block;
    position: relative;
    margin-left: 10px;
    input {
      display: block;
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0;
      cursor: pointer;
    }
  }
  .el-form-item {
    .el-form-item__content {
      width: 210px !important;
      .el-input {
        width: 210px !important;
      }
    }
  }
}
</style>
