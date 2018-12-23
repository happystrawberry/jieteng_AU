<template>
    <div class="zone-content table">
      <div class="table-header">
          <h3>消费积分余额记录</h3>
      </div>
       <el-table
                    ref="multipleTable"
                    border
                    :data="multipleTable"
                    tooltip-effect="dark"
                    v-loading="loading"
                    style="width:1400px"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="订单编号"
                    header-align="center"
                    align="center"
                    prop="order_sn"
                    width="300">
                    </el-table-column>
                    <el-table-column
                    prop="materialsid"
                    header-align="center"
                    align="center"
                    label="时间"
                   >
                     <template slot-scope="scope">
                      {{checkdate(scope.row.dateline)}}
                     </template>
                    </el-table-column>
                    <el-table-column
                    prop="change_cause"
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
                    width="200"
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
                    prop="change_value"
                    header-align="center"
                    align="center"
                    label="消费金额"
                    width="100"
                    >
                    </el-table-column>
                     <el-table-column
                    prop="credit"
                    header-align="center"
                    align="center"
                    label="积分余额"
                    width="100"
                    >
                      <template slot-scope="scope">
                      {{scope.row.is_add==1?scope.row.credit+scope.row.change_value:scope.row.credit-scope.row.change_value}}
                     </template>
                    </el-table-column>
                </el-table>
              <pageing  @pagechange="pagechange" v-show="page.totalnum" :pageing="page"></pageing>
      
       <div class="table-header" style="margin-top:80px;">
          <h3>消费余额记录</h3>
      </div>
       <el-table
                    ref="balance"
                    border
                    :data="balance"
                    tooltip-effect="dark"
                    v-loading="loadings"
                    style="width:100%"
                    stripe
                    @selection-change="handleSelectionChange">
                    <el-table-column
                    label="订单编号"
                    header-align="center"
                    align="center"
                    prop="order_sn"
                    width="300">
                    </el-table-column>
                    <el-table-column
                    prop="materialsid"
                    header-align="center"
                    align="center"
                    label="时间"
                    width="200">
                     <template slot-scope="scope">
                      {{checkdate(scope.row.dateline)}}
                     </template>
                    </el-table-column>
                    <el-table-column
                    prop="change_cause"
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
                    width="200"
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
                    prop="change_value"
                    header-align="center"
                    align="center"
                    label="消费金额"
                    width="100"
                    >
                    </el-table-column>
                     <el-table-column
                    prop="balance"
                    header-align="center"
                    align="center"
                    label="账号余额"
                    >
                    <template slot-scope="scope">
                      {{scope.row.is_add==1?scope.row.balance+scope.row.change_value:scope.row.balance-scope.row.change_value}}
                     </template>
                    </el-table-column>
                </el-table>
              <pageing  @pagechange="pagechanges" v-show="pages.totalnum" :pageing="pages"></pageing>
    </div>
</template>

<script>
import Pageing from "@/components/Paging/Paging";
import { Axios } from "@/api/login";
import { parseTime } from "@/utils/index";
export default {
  components: {
    Pageing
  },
      methods: {
        handleSelectionChange(value){
            this.checklist=value;
        },
        amend(type){
          this.centerDialogVisible.form.label=type?'积分':'余额'
        },
        checkdate(time){
          return parseTime(time)
        },
        getbalance(params){
          this.loadings=true;
           Axios("member/getbalanceLog",params).then(res=>{
              this.balance=res.data.list;
             let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.pages=page;
              this.loadings=false;
          })
        },
        getlist(params){
          this.loading=true
          Axios("member/getcreditlog",params).then(res=>{
              this.multipleTable=res.data.list;
               let page=res.data.page;
        for(let i in page){
              page[i]=parseInt(page[i])
            }
            this.page=page;
              this.loading=false;
          })
        },
        pagechanges(value){
          this.getbalance({
          uid:this.$route.query.uid,
          page:value
          })
        },
        pagechange(value){
            this.getlist({
              page:value,
              uid:this.$route.query.uid
            })
        },
      },
      mounted() {
        this.getlist({
          uid:this.$route.query.uid,
          page:1
        })
        this.getbalance({
          uid:this.$route.query.uid,
          page:1
        })
      },
      data(){
        return{
          page:{},
          pages:{},
          centerDialogVisible:{
            showkey:false,
            form:{
              phone:"",
              label:"",
              name:"",
            }
          },
          checklist:{},
          balance:[],
          loadings:true,
          loading:true,
           multipleTable:[]
        }
      }

}
</script>

<style scoped lang='scss'>
    .table-header{
      margin:30px 0;
    }
</style>