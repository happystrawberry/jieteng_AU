<template>
  <div class="zone-content">
      <div class="details-info">
        <h3 style="margin-bottom:20px;">材料详情</h3>
           <el-form :model="info"
           ref="zoneinfo"
           :rules="rule"
            label-position="left">
            <el-row class="input">
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="材料编码" label-width="140px" prop="sn">
                        <el-input style="width:210px" v-model="info.sn" disabled auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="材料名称" label-width="140px" prop="parts_name">
                        <el-input style="width:210px" v-model="info.parts_name" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="对应车型" label-width="140px" >
                        <el-input style="width:210px" v-model="info.target_model" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="规格" label-width="140px" prop="spec">
                        <el-input style="width:210px" v-model="info.spec" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="单位" label-width="140px" prop="unit">
                        <el-input style="width:210px" v-model="info.unit" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="剩余数量" label-width="140px" prop="amount">
                        <el-input style="width:210px" v-model="info.amount" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="进货单价(含税)" label-width="140px" prop="purchase_price_tax">
                        <el-input style="width:210px" v-model="info.purchase_price_tax" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="进货单价(不含税)" label-width="140px" prop="purchase_price_notax">
                        <el-input style="width:210px" v-model="info.purchase_price_notax" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col :xs={span:24} :sm={span:12} :md={span:12} :lg={span:8}>
                    <el-form-item label="出货单价" label-width="140px" prop="sell_price">
                        <el-input style="width:210px" v-model="info.sell_price" auto-complete="off"></el-input>
                    </el-form-item>
                </el-col>
                <el-col style="text-align:center">
                    <el-button type="primary" @click="save" size="small">保存</el-button>
                </el-col>
            </el-row>
        </el-form>
      </div>
      <div class="details-record">
        <div class="header">
          <h3 >库存记录</h3>
         <!-- <p v-if="tabledata">数据最后变化时间:{{tabledata.length>0?checkdate(tabledata[0].dateline):''}}</p>-->
        </div>
        <div class="content">
          <el-form :inline="true" :model="formInline" class="demo-form-inline">
            <el-form-item label="状态">
              <el-select 
              size="mini"
              v-model="formInline.types" placeholder="请选择方式">
                <el-option
                style="width:200px"
                v-for="(item,index) in formInline.type"
                :key="index" 
                :label="item.label" 
                :value="item.value"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-date-picker
                size="mini"
                v-model="formInline.time"
                type="daterange"
                 value-format="timestamp"
                  :default-time="['00:00:00', '23:59:59']"
                range-separator="至"
                start-placeholder="开始日期"
                end-placeholder="结束日期">
              </el-date-picker>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" @click="search" size="small">搜索</el-button>
            </el-form-item>
          </el-form>
        </div>
        <div class="table-ct">
              <el-table
            ref="multipleTable"
            border
            :data="tabledata"
            tooltip-effect="dark"
            style="width:1500px;"
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              label="订单编号"
              header-align="center"
              align="center"
              prop="order_sn"
              width="200"
              >
            </el-table-column>
             <el-table-column
              label="车牌号"
              header-align="center"
              align="center"
              prop="vehicle_number"
              width="200"
              >
            </el-table-column>
             <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="数量"
              width="160"
              >
              </el-table-column>
             <el-table-column
              prop="purchase_price_tax"
              label="进货单价(含税)"
              header-align="center"
              align="center"
              width="160"
              >
            </el-table-column>
             <el-table-column
              prop="purchase_price_notax"
              header-align="center"
              align="center"
              label="进货单价(不含税)"
               width="160"
              >
            </el-table-column>
            <el-table-column
              prop="sell_price"
              header-align="center"
              align="center"
              label="出货单价"
              width="160"
              >
            </el-table-column>
            <el-table-column
              prop="time"
              header-align="center"
              align="center"
              label="时间"
               width="160"
              >
               <template slot-scope="scope">
                 {{checkdate(scope.row.dateline)}}
               </template>
            </el-table-column>
             <el-table-column
              prop="status"
              header-align="center"
              align="center"
              label="状态"
               width="160"
              >
            </el-table-column>
            <el-table-column
              prop="operator_user"
              header-align="center"
              align="center"
              label="操作员"
              
              >
            </el-table-column>
          </el-table>
          <pageing  @pagechange="pagechange" :pageing="page"></pageing>
        </div>
        <div class="details-other" >
          <h3 style="margin:30px  0">备注</h3>
          <div class="textarea" style="width:800px;">
            <el-input  type="textarea" :rows="10" v-model="info.notes"></el-input>
            <el-button type="primary" size="mini" @click="remark">保存</el-button>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
import Pageing from "@/components/Paging/Paging";
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
export default {
  components: {
    Pageing
  },
  methods: {
    dialogFormVisible() {
      console.log(123);
    },
    checkdate(time) {
      return parseTime(time);
    },
    handleSelectionChange() {
      console.log(1);
    },
    save() {
      this.$refs.zoneinfo.validate(valid => {
        if (valid) {
           if(this.info.op_type==0){
                this.$message({
                message: "材料类型异常,请重试",
                type: "warning"
              });
              return;
            }
          Axios("parts/saveparts", this.info).then(res => {
            this.$message({
              message: "材料保存成功",
              type: "success"
            });
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    remark(){
      if(this.info.notes==undefined||this.info.notes.length==0){
          this.$message({
              message: "备注不能为空哟",
              type: "warning"
            });
            return;
      }
      Axios("parts/savenotes",{
        id:this.info.id,
        notes:this.info.notes
      }).then(res=>{
          this.$message({
              message: "保存备注成功",
              type: "success"
            });
      })
    },
    getlist(params) {
      Axios("parts/getpartsloglist", params).then(res => {
        this.tabledata = res.data.list;
         let page=res.data.page;
            for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
      });
    },
    pagechange(value) {
      this.getlist({
        parts_id:this.formInline.id,
        is_out: this.formInline.types,
        end_time:
          this.formInline.time? this.formInline.time[1] / 1000 : "",
        begin_time:
          this.formInline.time? this.formInline.time[0] / 1000 : "",
        page: value
      });
    },
    search() {
      this.getlist({
        parts_id:this.formInline.id,
        is_out: this.formInline.types,
        end_time:
          this.formInline.time? this.formInline.time[1] / 1000 : "",
        begin_time:
          this.formInline.time? this.formInline.time[0] / 1000 : "",
        page: this.page.curpage
      });
    },
    checkdate(time) {
      return parseTime(time);
    }
  },
  mounted() {
    let id = this.$route.query.id;
    this.formInline.id = id;
    this.info.id = id;
    Axios("parts/viewparts", {
      id: id
    }).then(res => {
      this.info = res.data.info;
      this.$message({
        message: "数据已更新",
        type: "success"
      });
    });
    // is_out:this.formInline.types,
    // end_time:this.formInline.time.length>0?this.formInline.time[0]/1000:'',
    // begin_time:this.formInline.time.length>0?this.formInline.time[1]/1000:'',
    // page:this.page.curpage
    this.getlist({
      parts_id:this.formInline.id,
      page: 1
    });
    // 获取通用备注接口
    Axios("common/ordernotelist",{
      order_id:this.info.id
    }).then(res=>{
        this.textarea=res.data.list[res.data.list.length-1].nessage
    })
  },
  data() {
    return {
      rule: {
        sn: [{ required: true, message: "请填写材料编码", trigger: "blur" }],
        parts_name: [
          { required: true, message: "请填写材料名称", trigger: "blur" }
        ],
        target_model: [
          { required: true, message: "请填写对应车型", trigger: "blur" }
        ],
        spec: [{ required: true, message: "请填写材料规格", trigger: "blur" }],
        unit: [{ required: true, message: "请填写材料单位", trigger: "blur" }],
        amount: [
          { required: true, message: "请填写剩余数量", trigger: "blur" }
        ],
        purchase_price_tax: [
          { required: true, message: "请填写材料进货单价", trigger: "blur" }
        ],
        purchase_price_notax: [
          { required: true, message: "请填写材料进货单价", trigger: "blur" }
        ],
        sell_price: [
          { required: true, message: "请填写材料出货单价", trigger: "blur" }
        ]
      },
      textarea: "",
      page: {},
      info: {},
      tabledata: [],
      formInline: {
        time: "",
        types: "",
        type: [
          {
            label: "全部",
            value: "-1"
          },
          {
            label: "出库",
            value: "1"
          },
          {
            label: "入库",
            value: "0"
          }
        ]
      }
    };
  }
};
</script>

<style scoped lang='scss'>
.header {
  display: flex;
  margin: 15px auto;
  line-height: 40px;
  h3 {
    margin-right: 20px;
  }
  p {
    margin: 0;
    font-size: 14px;
  }
}
  .input{
    .el-input__inner{
      width:210px !important;
    }
  }
.textarea {
  position: relative;
  .el-button {
    position: absolute;
    bottom: 20px;
    right: 50px;
  }
}
</style>