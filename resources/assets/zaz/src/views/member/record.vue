<template>
    <div class="zone-content table">
      <h3>维修保养记录</h3>
          <div style="margin:20px 0">
            <span>
              车牌号: 鄂A93205
            </span>
            <span>
              VIN码: 684688
            </span>
          </div>
       <el-table
                    ref="multipleTable"
                    border
                    v-loading="loading"
                    :data="multipleTable"
                    tooltip-effect="dark"
                    style="width:100%"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="订单编号"
                    header-align="center"
                    align="center"
                    prop="serial"
                    width="300">
                     <template slot-scope="scope">
                      <a style="color:#409eff"  @click="goto(scope.row.order_sn)">{{scope.row.order_sn}}</a>
                     </template>
                    </el-table-column>
                    <el-table-column
                    prop="materialsid"
                    header-align="center"
                    align="center"
                    label="付款时间"
                    width="200">
                     <template slot-scope="scope">
                     <el-tag type="danger" v-if="!scope.row.pay_time">{{scope.row.pay_status}}</el-tag>
                     <p v-if="scope.row.pay_time">{{checkdate(scope.row.pay_time)}}</p>
                     </template>
                    </el-table-column>
                    <el-table-column
                    prop="service_items"
                    header-align="center"
                    align="center"
                    label="项目"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="phone"
                    header-align="center"
                    align="center"
                    label="账号"
                    width="120"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="vehicle_number"
                    header-align="center"
                    align="center"
                    label="车牌"
                    width="200"
                    >
                    </el-table-column>
                    <el-table-column
                    prop="total_cost"
                    header-align="center"
                    align="center"
                    label="消费金额"

                    >
                    </el-table-column>
                </el-table>
                
    </div>
</template>

<script>
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";
export default {
      methods: {
        handleSelectionChange(){
          console.log(123)
        },
        checkdate(time){
          return parseTime(time)
        },
        goto(value){
            console.log(value)
        }
      },
      mounted() {
        this.loading=true
        Axios("member/getservicelog",{
          vehicle_number:this.$route.query.vehicle_number
        }).then(res=>{
          this.multipleTable=res.data.list;
          this.loading=false;
        })
      },
      data(){
        return{
           loading:true,
           multipleTable:[
           ]
        }
      }

}
</script>

<style scoped lang='scss'>
    .table-header{
      margin:30px 0;
    }
</style>