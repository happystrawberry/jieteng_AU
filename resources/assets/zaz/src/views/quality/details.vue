<template>
    <div class="zone-content details quelist-details" v-loading="loading">
        <div class="print-ct" id="form1" >
            <div class="print-ct-header" style="display:-webkit-box;display:-ms-flexbox;display:flex;margin-bottom:20px;">
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">单号:{{item.order_sn}}</div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;"></div>
        <div style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;line-height:30px;height:30px;margin:0;line-height:30px;font-size:16px;">打印时间:{{printtime}}</div>
              <!-- <img style="margin-left:40px;max-width:400px;" :src="'data:image/jpeg;base64,'+item.barcode" alt=""> -->
          </div>
          <div>
            <span>订单状态:{{item.order_status}}</span>
          </div>
          <div style="margin:20px 0;">
            <span>业务类型:{{item.op_type?item.op_type.join(" "):''}}</span>
          </div>
           <h3 style="margin-bottom:20px">车辆信息</h3>
          <div class="ul" style="width:700px;display:-webkit-box;display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;-webkit-box-pack:justify;-ms-flex-pack:justify;justify-content:space-between;">
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;">单号:{{item.order_sn?item.oder_sn:'未填写'}}</p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;">车主:{{item.vehicle_owner?item.vehicle_owner:"未填写"}}</p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;">车牌:{{item.vehicle_number?item.vehicle_number:'未填写'}}</p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;">车型:{{item.vehicle_model?item.vehicle_model:'未填写'}}</p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;">预计完工时间:{{item.plan_endtime?checkdate(item.plan_endtime):'未说明完工时间'}}</p>
              <p class="list" style="-webkit-box-flex:0;-ms-flex:0 0 33%;flex:0 0 33%;font-size:13px;line-height:30px;height:30px;margin:0;">完工时间:{{item.final_endtime?checkdate(item.final_endtime):'未完成'}}</p>
          </div> 
            <div class="printitem" v-if="multipleTable.length>0" style="margin-bottom:40px;">
            <h3 style="margin-bottom:20px">项目与工时</h3>
               <table border="1" width="600" height="30" cellspacing="0"  style="margin:0 auto;border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="104" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">材料编码</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">维修</td>
            <td width="150" height="30" align="center" style="border:solid 1px black">维修班组</td>
          </tr>
          <tr v-for="(items,index) in multipleTable" :key="index">
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.order_id}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.op_number}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.item_name}}</td>
            <td width="150" height="30" align="center" style="border:solid 1px black">{{items.operator}}</td>
          </tr>
  
        </table>
            </div>
            <div class="printitem"  v-if="materials.length>0" >
            <h3 style="margin-bottom:20px">材料质检</h3>
                  <table border="1" width="600" height="30" cellspacing="0"  style="margin:0 auto;border-collapse:collapse;table-layout:fixed;border:solid 1px black;">
          <tr>
            <td width="104" height="30" align="center" style="border:solid 1px black">编号</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">材料编码</td>
            <td width="200" height="30" align="center" style="border:solid 1px black">材料名称</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">规格</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">单位</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">数量</td>
            <!-- <td width="104" height="30" align="center" style="border:solid 1px black">质检状态</td> -->
          </tr>
          <tr v-for="(items,index) in materials" :key="index">
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.id}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.parts_sn}}</td>
            <td width="200" height="30" align="center" style="border:solid 1px black">{{items.parts_name.split(" ")[0]}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.spec}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.unit}}</td>
            <td width="104" height="30" align="center" style="border:solid 1px black">{{items.amount}}</td>
            <!-- <td width="104" height="30" align="center" style="border:solid 1px black">{{items.quality_status==0?'未质检':items.quality_status==1?'质检合格':'返工'}}</td> -->
          </tr>
        </table>
            </div>
              </div>
          <div class="noprint">
          <messagelist  v-show="info" :info="info"></messagelist>
           <el-row type="flex" jusitfy="space-between">
            <el-col class="left">
              <div>单号:{{item.order_sn}}</div>
              <img height="50px" @click="onecode" style="max-width:400px" :src="'data:image/jpeg;base64,'+item.barcode" alt="">
            </el-col>
            <el-col class="right">
              <el-button  @click.native="print"  size="medium" type="primary">打印质检单</el-button>
              <el-button  @click.native="gotobeizhu"  size="medium" type="primary">备注 ({{item.notes}})</el-button>
          </el-col>
        </el-row>
            <el-col >
             <span> 订单状态:{{item.order_status}}</span>
             <span style="margin-left:100px;"> 排号:{{item.wait_number}}</span>
            </el-col>
        <el-col class="model">
          <span>业务类型</span>
           <el-checkbox-group v-model="item.op_type">
                            <el-checkbox label="维修"></el-checkbox>
                            <el-checkbox label="保养"></el-checkbox>
                            <el-checkbox label="钣喷"></el-checkbox>
                            <el-checkbox label="美容"></el-checkbox>
                            <el-checkbox label="改装"></el-checkbox>
                            <el-checkbox label="其他"></el-checkbox>
           </el-checkbox-group>
        </el-col>
       <div style="margin:30px 0;">
          <h2 >车辆信息</h2>
        <el-row :gutter="20" class="info">
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>单号:</span>
            <span>{{item.order_sn}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>车主:</span>
            <span>{{item.vehicle_owner}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>车牌:</span>
            <span>{{item.vehicle_number}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>车型:</span>
            <span>{{item.vehicle_model}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>预计完工时间:</span>
            <span>{{item.plan_endtime?checkdate(item.plan_endtime):'未说明完工时间'}}</span>
          </el-col>
          <el-col el-col shadow="always" :xs={span:12} :sm={span:6} :md={span:6} :lg={span:6}>
            <span>完工时间:</span>
            <span>{{item.final_endtime?checkdate(item.final_endtime):'未完成'}}</span>
          </el-col>
        </el-row>
       </div>
        <div class="table">
          <div class="table-header">
            <h2>维修项目</h2>
            <div>
              <el-button size="mini" type="primary" @click="check(false)">返工给班组</el-button>
              <el-button size="mini" type="primary" @click="check(true)">质检合格</el-button>
            </div>
          </div>
           <el-table
            ref="multipleTable"
            border
            :data="multipleTable"
            tooltip-effect="dark"
            style="width: 756px"
            stripe
            @selection-change="handleSelectionChange">
             <el-table-column
              type="selection"
              width="55">
            </el-table-column>
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="order_id"
              width="100">
            </el-table-column>
            <el-table-column
              prop="op_number"
              header-align="center"
              align="center"
              label="编码"
              width="100">
            </el-table-column>
            <el-table-column
              prop="item_name"
              header-align="center"
              align="center"
              label="维修"
              width="200"
              >
            </el-table-column>
             <el-table-column
              prop="operator"
              header-align="center"
              align="center"
              label="维修班组"
              width="200"
              >
            </el-table-column>
             <el-table-column
              prop="status"
              header-align="center"
              align="center"
              label="质检状态"
              width="100"
              >
              <template slot-scope="scope">
                  {{scope.row.quality_status==0?'未质检':scope.row.quality_status==1?'质检合格':'返工'}}
               </template>
            </el-table-column>
          </el-table>
        </div>
        <div class="table">
          <div class="table-header">
            <h2>材料质检</h2>
            <div>
              <!-- <el-button size="mini" type="primary" @click="retruns()">返回到开单</el-button> -->
              <el-button size="mini" type="primary" @click="checks(false)">返工</el-button>
              <el-button size="mini" type="primary" @click="checks(true)">质检合格</el-button>
            </div>
          </div>
           <el-table
            ref="multipleTables"
            border
            :data="materials"
            tooltip-effect="dark"
            style="width:900px"
            stripe
            @selection-change="handleSelectionChanges">
             <el-table-column
              type="selection"
              width="55">
            </el-table-column>
            <el-table-column
              label="编号"
              header-align="center"
              align="center"
              prop="id"
              width="100">
            </el-table-column>
            <el-table-column
              prop="parts_sn"
              header-align="center"
              align="center"
              label="编码"
              width="100">
            </el-table-column>
             <el-table-column
              prop="parts_name"
              header-align="center"
              align="center"
              label="材料名称"
              width="200"
              >
            </el-table-column>
             <el-table-column
              prop="spec"
              header-align="center"
              align="center"
              label="规格"
              width="100"
              >
            </el-table-column>
            <el-table-column
              prop="unit"
              header-align="center"
              align="center"
              label="单位"
              >
            </el-table-column>
             <el-table-column
              prop="amount"
              header-align="center"
              align="center"
              label="数量"
              >
            </el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              label="质检状态"
              >
               <template slot-scope="scope">
                  {{scope.row.quality_status==0?'未质检':scope.row.quality_status==1?'质检合格':'返工'}}
               </template>
            </el-table-column>
          </el-table>
        </div>
        </div>
    </div>
</template>

<script>
let LODOP;
import { parseTime } from "@/utils/index";
import { Axios } from "@/api/login";
import {
  getCookie,
  setToken,
  removeToken,
  setCookie,
  removeCookie
} from "@/utils/auth";
import Messagelist from "@/components/Messagelist/Messagelist";

export default {
  components: {
    Messagelist
  },
  methods: {
    handleSelectionChange(value) {
      this.checklist = value;
    },
    toggleSelection(rows) {
      if (rows) {
        rows.forEach(row => {
          this.$refs.multipleTable.toggleRowSelection(row);
        });
      } else {
        this.$refs.multipleTable.clearSelection();
      }
    },
    toggleSelections(rows) {
      if (rows) {
        rows.forEach(row => {
          this.$refs.multipleTables.toggleRowSelection(row);
        });
      } else {
        this.$refs.multipleTables.clearSelection();
      }
    },
    handleSelectionChanges(value) {
      this.checklists = value;
    },
    gotobeizhu() {
      let routeData = this.$router.resolve({
        name: "remarkindex",
        query: { id: this.$route.query.id }
      });
      window.open(routeData.href, "_blank");
    },
     onecode(){
          LODOP=getLodop(document.getElementById('LODOP1'),document.getElementById('LODOP_EM1')); 
            LODOP.ADD_PRINT_BARCODE(40,200,400,47,"EAN128B",this.item.order_sn);
	        	LODOP.SET_PRINT_STYLEA(0,"AlignJustify",2);
            LODOP.PRINT_DESIGN();		
        },
    print() {
      this.printtime=this.checkdate(new Date().getTime());
      setTimeout(()=>{
          try{ 
		     var LODOP=getLodop(); 
			if (LODOP.VERSION) {
         LODOP.PRINT_INIT("");
        LODOP.SET_PRINT_STYLE("FontSize", 18);
        LODOP.SET_PRINT_STYLE("Bold", 1);
        LODOP.ADD_PRINT_HTM(40, 5, "100%", "100%", document.getElementById("form1").innerHTML);
        LODOP.PREVIEW();
			}else{
        this.$message({
              showClose: true,
              message: "打印控件未安装,请先联系管理员下载安装",
              type: "warning"
            });
      }
		 }catch(err){
       
 		 } 
      },500)
       
    },
    retruns() {
      // 这里改为直接请求
      this.$router.push({
        name: "orderdetails",
        query: { id: this.$route.query.id }
      });
    },
    checks(type) {
      let that = this;
      if (this.checklists.length == 0) {
        this.$message({
          message: "请先勾选表格",
          type: "warning"
        });
        return;
      } else {
        let id = "",
          ids = [];
        let checklist = that.checklists;
        for (let i in that.checklists) {
          ids.push(that.checklists[i].id);
        }
        id = ids.join(",");
        Axios("quality/setstatus", {
          id: id,
          type: "part",
          pass: type ? 1 : 0
        }).then(res => {
          that.$notify({
            message: `勾选${type ? "材料质检合格" : "材料返工"}操作成功`,
            type: "success",
            offset: 100,
            duration: 1500
          });
          for (let i in ids) {
            let index = this.materials.findIndex(el => {
              console.log(el.id,ids[i])
              return el.id == ids[i];
            });
            if (index > -1) {
              this.materials[index].quality_status = `${type ? 1 : 2}`
            }
          }
         this.toggleSelections();
        });
      }
    },
    check(type) {
      let that = this;
      if (this.checklist.length == 0) {
        this.$message({
          message: "请先勾选表格",
          type: "warning"
        });
        return;
      } else {
        let id = "",
          ids = [];
        let checklist = that.checkList;
        for (let i in this.checklist) {
          console.log(this.checklist[i].id);
          ids.push(this.checklist[i].id);
        }
        id = ids.join(",");
        Axios("quality/setstatus", {
          id: id,
          type: "item",
          pass: type ? 1 : 0
        }).then(res => {
          that.$notify({
            message: `勾选${type ? "项目质检合格" : "项目返工给班组"}操作成功`,
            type: "success",
            offset: 100,
            duration: 1500
          });
        
          for (let i in ids) {
            let index = this.multipleTable.findIndex(el => {
              console.log(el.id,ids[i])
              return el.id == ids[i];
            });
            if (index > -1) {
              this.multipleTable[index].quality_status = `${type ? 1 : 2}`;
            }
          }
            this.toggleSelection();
          // this.update();
        });
      }
    },
    update() {
      Axios("quality/vieworder", {
        order_id: this.$route.query.id
      }).then(res => {
        this.item = res.data;
        this.multipleTable = res.data.item_list;
        let materlist = res.data.part_list;

        this.materials = materlist.filter(el => {
          return el.parts_status != 2;
        });
        this.$message({
          message: "数据已更新",
          type: "success"
        });
        this.loading = false;
      });
      Axios("common/ordernotelist", {
        order_id: getCookie("uid")
      }).then(res => {
        this.info = res.data.list[0] ||{};
      });
    },
    checkdate(time) {
      return parseTime(time);
    }
  },
  mounted() {
    let id = this.$route.query.id;
    this.update();
  },
  data() {
    return {
      item: {},
      info: {},
      printtime:"",
      loading: true,
      checklists: [],
      materials: [],
      multipleTable: [],
      otherdata: [],
      remuneration: [],
      formInline: {},
      checkList: [],
      data: []
    };
  }
};
</script>

<style  lang='scss'>
.el-tag {
  padding: 0 15px;
}
.print-ct {
  display: none;
}

@media print {
  .el-message {
    display: none !important;
  }
  .printitem {
    div {
      display: flex;
      &:nth-of-type(1) {
        span {
          border-top: 2xp solid #ebeef5;
        }
      }
      &:last-of-type {
        span {
          border-bottom: 2xp solid #ebeef5;
        }
      }
      span {
        flex: 1;
        border: 1px solid #ebeef5;
        font-size: 10px;
        text-align: center;
        word-wrap: break-word;
        line-height: 30px;
        &:nth-of-type(1) {
          border-left: 2xp solid #ebeef5;
        }
        &:last-of-type {
          border-right: 2xp solid #ebeef5;
        }
      }
    }
  }
  .print-ct {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    min-height: 100%;
    z-index: 99999;
    display: block;
    &-header {
      display: flex;
      margin-bottom: 20px;
      div {
        margin-top: 20px;
        margin-right: 40px;
        display: inline-block;
      }
    }
    .ul {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      .list {
        flex: 0 0 30%;
        font-size: 13px;
        margin-bottom: 5px;
      }
    }
  }
  .details {
    margin: 0;
  }

  .main-container {
    margin-left: 0 !important;
  }
  .el-scrollbar__bar {
    display: none !important;
  }
  .sidebar-container {
    display: none !important;
  }
  .el-scrollbar__view {
    display: none;
  }
  .el-menu {
    display: none;
  }
  .noprint {
    display: none;
  }
}
.model {
  display: flex;
  position: relative;
  &:after {
    content: "";
    display: block;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 123;
    position: absolute;
  }
  > span {
    margin-right: 20px;
  }
}
.info {
  span {
    line-height: 20px;
    &:last-child {
      color: #aaa;
      margin-left: 10px;
    }
  }
}
.el-col {
  margin-bottom: 20px;
  &.left {
    display: flex;
    align-items: center;
    img {
      margin-left: 30px;
    }
  }
  &.right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
}
.table {
  margin-bottom: 40px;
  &-header {
    display: flex;
    align-items: center;
    div {
      margin-left: 50px;
    }
  }
}
</style>
