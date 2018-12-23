<template>
   <div class="zone-content kucunlist">
       <comheader 
       :formInline="formInline"
       @search="search"
       ></comheader>
       <div class="kucunlist-table">
           <el-col class="header">
              <h3>库存表</h3> 
              <div class="header-list">
                <el-button type="primary" size="small" @click="checkexport">导出库存表</el-button>
                <el-upload
                class="upload-demo"
                action="/api/parts/import"
                :on-success="success"
                :on-error="error"
                :show-file-list=false
                accept="excel">
                <el-button size="small" type="primary">Excel导入库存</el-button>
              </el-upload>
                <el-button type="primary" size="small" @click="addmaterials" >添加材料</el-button>
              </div>
           </el-col>
           <el-table
            ref="multipleTable"
            border
            v-loading="loading"
            :data="tabledata"
            tooltip-effect="dark"
            style="width: 100%">
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="id"
              width="80">
            </el-table-column>
            <el-table-column
              prop="sn"
              header-align="center"
              align="center"
              label="材料编码"
              width="100">
            </el-table-column>
            <el-table-column
              prop="parts_name"
              header-align="center"
              align="center"
              label="材料名称"
              width="160"
              >
            </el-table-column>
             <el-table-column
              prop="target_model"
              header-align="center"
              align="center"
              label="对应车型"
              >
            </el-table-column>
             <el-table-column
              prop="spec"
              header-align="center"
              align="center"
              label="规格"
              width="70"
              >
            </el-table-column>
            <el-table-column
              prop="unit"
              header-align="center"
              align="center"
              label="单位"
              width="70"
              >
            </el-table-column>
             <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="剩余数量"
              width="80"
              >
            </el-table-column>
             <el-table-column
              prop="purchase_price_tax"
              label="进货单价(含税)"
              header-align="center"
              align="center"
              width="90"
              >
            </el-table-column>
             <el-table-column
              prop="purchase_price_notax"
              header-align="center"
              align="center"
              label="进货单价(不含税)"
              width="90"
              >
            </el-table-column>
            <el-table-column
              prop="sell_price"
              header-align="center"
              align="center"
              label="出货单价"
              width="100"
              >
            </el-table-column>
             <el-table-column
              prop="handletime"
              header-align="center"
              align="center"
              label="最后修改时间"
              width="180"
              >
              <template slot-scope="scope">入库出库
                  {{checkdate(scope.row.update_time)}}
              </template>
            </el-table-column>
             <el-table-column
              header-align="center"
              align="center"
              label="操作"
              >
            <template slot-scope="scope" >
                <div style="display:flex; justify-content:space-around">
                   <el-popover
                        placement="top"
                        width="160"
                        v-model="scope.row.visible">
                        <p>确定{{scope.row.enable==1?'关闭':'开启'}}该材料吗？</p>
                        <div style="text-align: right; margin: 0">
                            <el-button size="mini" type="text" @click="scope.row.visible = false">取消</el-button>
                            <el-button type="primary" size="mini" @click="surecheck(scope)">确定</el-button>
                        </div>
                        <el-button slot="reference" size="mini" :type="scope.row.enable==1?'danger':'primary'">{{scope.row.enable==0?'开启':'关闭'}}</el-button>
                        </el-popover>
                    <el-button type="primary" size="mini" @click="gotodetails(scope.row.id)">查看详情</el-button>
                </div>
            </template>
            </el-table-column>
           
          </el-table>
       </div>
       <el-dialog 
       title="添加材料"
       width="50%"
       center
       :visible.sync="dialogFormVisible.showkey">
        <el-form :model="dialogFormVisible.form"
            label-position="left"
            ref="zoneform"
            :rules="rule"
            v-model="dialogFormVisible.form"
            >
            <el-row>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="材料编码" label-width="140px" prop="sn">
                        <el-input v-model="dialogFormVisible.form.sn" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="数量" label-width="140px" prop="amount">
                        <el-input v-model="dialogFormVisible.form.amount" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="材料名称" label-width="140px" prop="parts_name">
                        <el-input v-model="dialogFormVisible.form.parts_name" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="送货单价(含税)" label-width="140px" prop="purchase_price_tax">
                        <el-input v-model="dialogFormVisible.form.purchase_price_tax" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="对应车型" label-width="140px" >
                        <el-input v-model="dialogFormVisible.form.target_model" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="进货单价(不含税)" label-width="140px" prop="purchase_price_notax">
                        <el-input v-model="dialogFormVisible.form.purchase_price_notax" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="规格" label-width="140px" prop="spec">
                        <el-input v-model="dialogFormVisible.form.spec" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="出货单价" label-width="140px" prop="sell_price">
                        <el-input v-model="dialogFormVisible.form.sell_price" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="单位" label-width="140px" prop="unit">
                        <el-input v-model="dialogFormVisible.form.unit" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:12}>
                    <el-form-item label="库存位置" label-width="140px" prop="parts_location">
                        <el-input v-model="dialogFormVisible.form.parts_location" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col style="text-align:center;">
                    <el-button type="primary" @click="overadd()" size="small">添加</el-button>
                </el-col>
            </el-row>
        </el-form>
        </el-dialog>
       <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
   </div>
</template>

<script>
import Comheader from "@/components/comHeader/comheader";
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
export default {
  components: {
    Comheader,
    Pageing
  },
  methods: {
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
     error(res){
        this.$notify.error({
          title: '错误',
          message:'导入文档失败,请重试',
          duration: 4500
        });  
    },
    success(res) {
     if(res.code==200){
        this.$notify({
          title: '成功',
          message: '导入文档成功',
          type: 'success',
          duration:4500,
        });
      Axios("common/getoptypelist").then(res => {
        this.getoptypelist = res.data.list;
      });
      this.getlist();
     }else{
       this.$notify({
          title: '警告',
          message: res.msg,
          type: 'warning',
          duration:4500,
        });
     }
      
    },
    checkexport() {
      let ele = this.formInline;
      // Axios("parts/supplylist",{
      //   page:this.page.curpage,
      //    parts_name:ele.accessories,
      //     export:1,
      //      sn:ele.materials,
      //      begin_time:ele.time? ele.time[0] / 1000 : "",
      //       end_time:ele.time? ele.time[1] / 1000 : "",
      // })
      window.open(
        `/api/parts/supplylist?page=${
          this.page.curpage
        }&parts_name=${ele.accessories}&export=1&sn=${
          ele.materials
        }&begin_time=${
          ele.time? ele.time[0] / 1000 : ""
        }&end_time=${ele.time? ele.time[1] / 1000 : ""}`,
        "_blank"
      );
    },
    pagechange(value) {
      let ele = this.formInline;
      this.getlist({
        page: value,
        parts_name: ele.accessories,
        sn: ele.materials,
        begin_time: ele.time? ele.time[0] / 1000 : "",
        end_time: ele.time? ele.time[1] / 1000 : ""
      });
    },
    search(value) {
      let ele = this.formInline;
      this.getlist({
        page: this.page.curpage,
        parts_name: ele.accessories,
        sn: ele.materials,
        begin_time: ele.time? ele.time[0] / 1000 : "",
        end_time: ele.time? ele.time[1] / 1000 : ""
      });
    },
    surecheck(scope) {
      scope.row.visible = false
      Axios("parts/delete", {
        id: scope.row.id,
        enable:scope.row.enable==1?0:1
      }).then(res => {
        let index = this.tabledata.findIndex(el => {
          return (el.id ==scope.row.id);
        });
        this.tabledata[index].enable=scope.row.enable==1?0:1;
        this.$message({
          message: `${scope.row.enable==1?'开启':'关闭'}该材料成功`,
          type: "success"
        });
      });
    },
    checkdate(time) {
      return parseTime(time);
    },
    getlist(params) {
      this.loading = true;
      Axios("parts/supplylist", params).then(res => {
        let list = res.data.list;
         let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
        for (let i in list) {
          list[i].visibility = false;
        }
        this.tabledata = list;
        this.$message({
          message: "数据已更新",
          type: "success"
        });
        this.loading = false;
      });
    },
    addmaterials() {
      this.dialogFormVisible.showkey = true;
      this.$nextTick(() => {
        this.$refs.zoneform.resetFields();
      });
    },
    overadd() {
      this.$refs.zoneform.validate(valid => {
        if (valid) {
           if(this.dialogFormVisible.op_type==0){
                this.$message({
                message: "材料类型异常,请重试",
                type: "warning"
              });
              return;
            }
          Axios("parts/saveparts", this.dialogFormVisible.form).then(res => {
            this.getlist();
            this.dialogFormVisible.showkey = false;
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    gotodetails(index) {
      this.$router.push({ name: "kuncuns", query: { id: index } });
    }
  },
  mounted() {
    Axios("common/getoptypelist").then(res => {
      this.getoptypelist = res.data.list;
    });
    this.getlist();
  },
  data() {
    return {
      formInline: {
        accessories: "",
        materials: "",
        time: ""
      },
      getoptypelist: {},
      rule: {
        sn: [{ required: true, message: "请输入材料编码", trigger: "blur" }],
        parts_name: [
          { required: true, message: "请输入材料名称", trigger: "blur" }
        ],
        target_model: [
          { required: false, message: "请输入对应车型", trigger: "blur" }
        ],
        spec: [{ required: true, message: "请输入材料规格", trigger: "blur" }],
        unit: [{ required: true, message: "请输入材料单位", trigger: "blur" }],
        amount: [
          { required: true, message: "请输入材料数量", trigger: "blur" }
        ],
        purchase_price_tax: [
          { required: true, message: "请输入材料进货单价", trigger: "blur" }
        ],
        purchase_price_notax: [
          { required: true, message: "请输入材料进货单价", trigger: "blur" }
        ],
        sell_price: [
          { required: true, message: "请输入材料出货价格", trigger: "blur" }
        ],
        parts_location: [
          { required: true, message: "请输入材料库存位置", trigger: "blur" }
        ]
      },
      loading: false,
      page: {},
      dialogFormVisible: {
        showkey: false,
        form: {
          id: "",
          op_type: "",
          materialsid: "",
          nums: "",
          materialsname: "",
          cost: "",
          selling: "",
          motorcycle: "",
          specification: "",
          shipment: "",
          unit: "",
          location: ""
        }
      },
      tabledata: []
    };
  }
};
</script>

<style  lang='scss'>
.kucunlist {
  
  .header-list{
     .upload-demo{
        display:inline-block;
        margin:0 10px;
     }
  }
  .header {
    display: flex;
    margin-bottom: 30px;
    vertical-align: center;
    h3 {
      margin: 0 40px 0 0px;
      line-height: 40px;
    }
  }
  .el-dialog {
    .el-input__inner {
      width: 200px;
    }
  }
}
</style>