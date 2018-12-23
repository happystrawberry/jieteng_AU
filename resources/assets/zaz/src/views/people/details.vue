<template>
    <div class="zone-content form" v-loading="loading">
      <div class="table">
        <div class="table-header">
          <h2>员工列表</h2>
        </div>
        <div>
           <el-form  class="form" label-position="left" label-width="100px" :model="formInline" >
              <div class="left">
                <img style="width:150px;height:150px;" :src="avatar" class="avatar">
                <p>{{formInline.username}}</p>
              </div>
              <div class="right">
                <el-form-item label="工号">
                 <span>{{formInline.ding_jobnumber}}</span>
                </el-form-item>
                <el-form-item label="钉钉部门">
                 <span>{{formInline.ding_department}}</span>
                </el-form-item>
                 <el-form-item label="权限组">
                 <span>{{formInline.groupname?formInline.groupname:"暂无"}}</span>
                </el-form-item>
                <el-form-item label="手机">
                 <span>{{formInline.phone}}</span>
                </el-form-item>
                <el-form-item label="职位">
                 <span>{{formInline.ding_position}}</span>
                </el-form-item>
                <el-form-item label="备注">
                  <span>{{formInline.notes}}</span>
                </el-form-item>
              </div>
          </el-form>
        </div>
      </div>
      <div class="table" v-show="false">
        <div class="table-header">
          <h2>工时统计</h2>
          <div class="check">
              <el-button  size="mini"  @click="checkquery(1)" :class="{active:checkindex==1}" >本月</el-button>
               <el-button  size="mini" @click="checkquery(2)"  :class="{active:checkindex==2}">前三个月</el-button>
              <!--<el-button type="primary" size="mini">导出Excel</el-button>-->
          </div>
        </div>
        <el-table
            ref="multipleTable"
            border
            :data="multipleTable"
            tooltip-effect="dark"
            style="width: 450px"
            :summary-method="getSummaries"
            show-summary
            stripe
            @selection-change="handleSelectionChange">
            <el-table-column
              label="订单编号"
              header-align="center"
              align="center"
              prop="order_sn"
              width="200">
            </el-table-column>
            <el-table-column
              prop="times"
              header-align="center"
              align="center"
              label="工时"
              width="100">
            </el-table-column>
            <el-table-column
              prop="costs"
              header-align="center"
              align="center"
              label="工时费"
              >
               <template slot-scope="scope">
                  {{scope.row.costs*scope.row.times}}
               </template>
            </el-table-column>
          </el-table>
      </div>
    </div>
</template>

<script>
import { Axios } from "@/api/login";
import avatar from "./avatar.jpg"
export default {
  mounted() {
    Axios("people/view", {
      id: this.$route.query.id
    }).then(res => {
      this.loading = false;
      this.formInline=res.data.info;
    });
    // 这里默认时间都传空
    this.getmain({
      id:this.$route.query.id
    });
  },
  methods: {
    handleSelectionChange() {
      console.log(123123);
    },
    checkquery(type){
      this.checkindex=type;
      let time=new Date();
      if(type==1){
        let day=time.getDate(),
            startime=(time.getTime()-day*24*3600*1000)/1000;
         this.getmain({
           id:this.$route.query.id,
           begin_time:startime,
           end_time:time.getTime()
         })   
      }else{
        let day=time.getDate(),
            startime=(time.getTime()-90*24*3600*1000)/1000;
         this.getmain({
           id:this.$route.query.id,
           begin_time:startime,
           end_time:time.getTime()
         })   
      }
    },
    getmain(params){
      Axios("people/maintainstat",params).then(res=>{
       this.multipleTable=res.data.list
      })
    },
    getowns() {
      this.list[this.itemcheck].driving_license.img_1 = loading;
    },
    beforeowns(){

    },
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = "合计";
          return;
        }
        const values = data.map(item => Number(item[column.property]));
        if (!values.every(value => isNaN(value)) && index == 2) {
          sums[index] = values.reduce((prev, curr) => {
            const value = Number(curr);
            if (!isNaN(value)) {
              return prev + curr;
            } else {
              return prev;
            }
          }, 0);
          sums[index]=sums[index].toFixed(2);
          sums[index] += " 元";
        } else {
          sums[index] = "";
        }
      });
      return sums;
    }
  },
  data() {
    return {
      avatar:avatar,
      loading: true,
      formInline: {
        
      },
      checkindex:0,
      multipleTable: [
        {
          serial: "WXPPP12018040000181",
          materialsid: "3",
          materialsnum: "50"
        },
        {
          serial: "WXPPP12018040000181",
          materialsid: "3",
          materialsnum: "50"
        },
        {
          serial: "WXPPP12018040000181",
          materialsid: "3",
          materialsnum: "50"
        },
        {
          serial: "WXPPP12018040000181",
          materialsid: "3",
          materialsnum: "50"
        }
      ]
    };
  }
};
</script>

<style  lang='scss'>
.form {
  text-align: center;
  vertical-align: middle;
  .left {
    display: inline-block;
    margin-right: 100px;
  }
  .right {
    display: inline-block;
  }
  .check{
    .el-button{
      &.active{
        background:#409EFF;
        color:white;
      }
    }
  }
}
</style>
